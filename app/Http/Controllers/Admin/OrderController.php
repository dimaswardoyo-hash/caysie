<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\BiteshipService;

class OrderController extends Controller
{
    private array $statuses = ['pending', 'waiting_confirmation', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function __construct(private BiteshipService $biteship) {}

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = $this->statuses;

        // Hitung badge per status
        $statusCounts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'statuses', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        $statuses = $this->statuses;
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    // ── Langkah 1: cari area_id Biteship dari data alamat yang SUDAH tersimpan ──
    // (kota/kecamatan hasil dropdown RajaOngkir + kode pos otomatis).
    // Admin tidak perlu ketik alamat apa pun di sini.
    public function resolveShippingArea(Order $order)
    {
        $areas = $this->biteship->resolveAreaId(city: $order->receiver_city, district: $order->receiver_district ?? '', postalCode: $order->receiver_postal_code ?? '');

        if (empty($areas)) {
            return response()->json([
                'success' => false,
                'message' => 'Area tujuan tidak ditemukan di Biteship. Coba cek manual, atau isi resi secara manual di form "Update Status".',
            ]);
        }

        return response()->json(['success' => true, 'areas' => $areas]);
    }

    // ── Langkah 2: setelah area_id dipilih, tampilkan pilihan kurir + ongkir ──
    public function shippingRates(Request $request, Order $order)
    {
        $request->validate(['area_id' => 'required|string']);

        $weight = $order->totalWeightGram();
        $rates = $this->biteship->getRates($request->area_id, $weight);

        if (empty($rates)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kurir yang tersedia untuk area ini.',
            ]);
        }

        return response()->json(['success' => true, 'rates' => $rates, 'weight' => $weight]);
    }

    // ── Langkah 3: generate resi (createOrder ke Biteship) ──
    public function generateShipment(Request $request, Order $order)
    {
        $request->validate([
            'area_id' => 'required|string',
            'courier_code' => 'required|string',
            'courier_service' => 'required|string',
        ]);

        if ($order->tracking_number) {
            return back()->with('error', "Pesanan #{$order->order_number} sudah punya nomor resi.");
        }

        $origin = $this->biteship->getOriginContact();

        $items = $order->items
            ->map(
                fn($item) => [
                    'name' => $item->product_name,
                    'value' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'weight' => (int) ($item->product->weight ?? 200),
                ],
            )
            ->toArray();

        $result = $this->biteship->createOrder([
            'origin_contact_name' => $origin['name'],
            'origin_contact_phone' => $origin['phone'],
            'origin_address' => $origin['address'],
            'origin_postal_code' => $this->biteship->getOriginPostalCode(),
            'destination_contact_name' => $order->receiver_name,
            'destination_contact_phone' => $order->receiver_phone,
            'destination_address' => $order->receiver_address,
            'destination_area_id' => $request->area_id,
            'courier_company' => $request->courier_code,
            'courier_type' => $request->courier_service,
            'delivery_type' => 'now',
            'order_note' => 'Order #' . $order->order_number,
            'items' => $items,
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Gagal generate resi otomatis: ' . $result['error']);
        }

        $order->update([
            'tracking_number' => $result['waybill_id'] ?? $result['tracking_id'],
            'biteship_order_id' => $result['biteship_order_id'],
            'biteship_area_id' => $request->area_id,
            'biteship_courier_company' => $request->courier_code,
            'biteship_courier_type' => $request->courier_service,
            'biteship_status' => $result['status'],
            'status' => 'shipped',
        ]);

        return back()->with('success', "Resi berhasil dibuat otomatis via Biteship: {$order->tracking_number}");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(
            [
                'status' => 'required|in:' . implode(',', $this->statuses),
                'tracking_number' => 'required_if:status,shipped|nullable|string|max:100',
            ],
            [
                'tracking_number.required_if' => 'Nomor resi wajib diisi saat status diubah ke Dikirim.',
            ],
        );

        $old = $order->status;

        $data = ['status' => $request->status];

        // Kalau baru dikonfirmasi pembayarannya lewat form manual ini
        if ($request->status === 'confirmed' && !$order->paid_at) {
            $data['paid_at'] = now();
        }

        // Nomor resi wajib disertakan saat status diubah ke "Dikirim"
        if ($request->status === 'shipped') {
            $data['tracking_number'] = $request->tracking_number;
        }

        // Kembalikan stok jika admin membatalkan order yang sebelumnya belum dibatalkan
        if ($request->status === 'cancelled' && $old !== 'cancelled') {
            $order->restoreStock();
            $data['cancelled_at'] = now();
            $data['cancelled_by'] = 'admin';
        }

        $order->update($data);

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diubah dari " . ucfirst($old) . ' → ' . ucfirst($request->status) . '.');
    }

    public function confirmPayment(Order $order)
    {
        if (!in_array($order->status, ['pending', 'waiting_confirmation'])) {
            return back()->with('error', 'Pesanan tidak dalam status yang bisa dikonfirmasi.');
        }

        $order->update([
            'status' => 'confirmed',
            'paid_at' => $order->paid_at ?? now(),
        ]);

        return back()->with('success', "Pembayaran pesanan #{$order->order_number} berhasil dikonfirmasi. Status → Dikonfirmasi. Lanjutkan ke 'Diproses' saat pesanan mulai dikemas.");
    }
}
