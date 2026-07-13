<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\XenditService;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    // ── Halaman checkout ─────────────────────────────────────
    public function index()
    {
        $carts = Cart::with(['product', 'productSize'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kamu kosong.');
        }

        $subtotal = $carts->sum(fn($c) => ($c->product->price_sale ?? $c->product->price) * $c->quantity);

        return view('user.checkout', compact('carts', 'subtotal'));
    }

    // ── Proses checkout → buat order → redirect ke Xendit ────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_province' => 'required|string',
            'receiver_city' => 'required|string',
            'receiver_address' => 'required|string|max:1000',
            'receiver_postal_code' => 'required|string|max:10',
            'receiver_district' => 'nullable|string',
            'receiver_village' => 'nullable|string',
            'biteship_area_id' => 'required|string',
            'courier_code' => 'required|string',
            'courier_name' => 'required|string',
            'courier_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_estimate' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $carts = Cart::with(['product', 'productSize'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong.');
        }

        $subtotal = $carts->sum(fn($c) => ($c->product->price_sale ?? $c->product->price) * $c->quantity);
        $shippingCost = (int) $validated['shipping_cost'];
        $totalAmount = (int) ($subtotal + $shippingCost);

        DB::beginTransaction();
        try {
            // 1. Buat order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'receiver_address' => $validated['receiver_address'],
                'receiver_province' => $validated['receiver_province'],
                'receiver_city' => $validated['receiver_city'],
                'receiver_district' => $validated['receiver_district'] ?? null,
                'receiver_village' => $validated['receiver_village'] ?? null,
                'receiver_postal_code' => $validated['receiver_postal_code'] ?? null,
                'biteship_area_id' => $validated['biteship_area_id'],
                'courier_code' => $validated['courier_code'],
                'courier_name' => $validated['courier_name'],
                'courier_service' => $validated['courier_service'],
                'shipping_cost' => $shippingCost,
                'shipping_estimate' => $validated['shipping_estimate'] ?? null,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'payment_deadline' => now()->addHours(24),
            ]);

            // 2. Buat order items & kurangi stok
            $xenditItems = [];
            foreach ($carts as $cart) {
                $price = (float) ($cart->product->price_sale ?? $cart->product->price);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'product_size' => $cart->productSize->size ?? '-',
                    'product_image' => $cart->product->image ?? null,
                    'price' => $price,
                    'quantity' => $cart->quantity,
                    'subtotal' => $price * $cart->quantity,
                ]);

                // Kunci baris stok & cek ulang ketersediaan saat ini (mencegah oversell
                // jika beberapa user checkout bersamaan untuk stok yang sama/terbatas).
                $size = $cart->productSize()->lockForUpdate()->first();
                if (!$size || $size->stock < $cart->quantity) {
                    throw new \RuntimeException('Stok ' . $cart->product->name . ' (' . ($cart->productSize->size ?? '-') . ') tidak lagi mencukupi.');
                }
                $size->decrement('stock', $cart->quantity);

                $xenditItems[] = [
                    'name' => $cart->product->name . ' (' . ($cart->productSize->size ?? '-') . ')',
                    'quantity' => $cart->quantity,
                    'price' => (int) $price,
                    'category' => $cart->product->category ?? 'Fashion',
                    'url' => route('user.product.show', $cart->product->slug),
                ];
            }

            // Ongkir sebagai item terpisah di Xendit
            $xenditItems[] = [
                'name' => 'Ongkos Kirim (' . $validated['courier_name'] . ' ' . $validated['courier_service'] . ')',
                'quantity' => 1,
                'price' => $shippingCost,
                'category' => 'Shipping',
            ];

            // 3. Kosongkan keranjang
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Checkout] DB error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pesanan: ' . $e->getMessage());
        }

        // 4. Buat Xendit Invoice (di luar transaksi DB agar rollback tidak hapus order)
        try {
            $invoice = $this->xendit->createInvoice([
                'external_id' => $order->order_number,
                'amount' => $totalAmount,
                'payer_email' => auth()->user()->email,
                'description' => 'Pembayaran ' . $order->order_number . ' — Caysie',
                'customer_name' => $validated['receiver_name'],
                'customer_phone' => $validated['receiver_phone'],
                'success_redirect_url' => route('user.payment.success', $order),
                'failure_redirect_url' => route('user.payment.failed', $order),
                'items' => $xenditItems,
            ]);

            if (isset($invoice['error'])) {
                throw new \RuntimeException($invoice['error']);
            }

            // Simpan data Xendit ke order
            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'xendit_invoice_url' => $invoice['invoice_url'],
                'xendit_expires_at' => isset($invoice['expiry_date']) ? $invoice['expiry_date'] : null,
            ]);

            Log::info('[Checkout] Invoice Xendit dibuat', [
                'order_id' => $order->id,
                'invoice_url' => $invoice['invoice_url'],
            ]);

            // 5. Redirect ke halaman pembayaran Xendit
            return redirect($invoice['invoice_url']);
        } catch (\Throwable $e) {
            // Order sudah tersimpan, tapi Xendit gagal → arahkan ke halaman payment manual
            Log::error('[Checkout] Xendit error: ' . $e->getMessage());
            return redirect()->route('user.payment.show', $order)->with('warning', 'Pesanan berhasil dibuat, namun gagal terhubung ke Xendit. Silakan coba bayar dari halaman ini.');
        }
    }

    // ── Halaman detail pembayaran (fallback / retry) ─────────
    public function showPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.payment', compact('order'));
    }

    // ── Buat ulang invoice Xendit (tombol "Buat Link Pembayaran") ─
    public function retryPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('user.payment.show', $order)->with('error', 'Pesanan ini tidak lagi menunggu pembayaran.');
        }

        $xenditItems = $order->items
            ->map(
                fn($item) => [
                    'name' => $item->product_name . ' (' . $item->product_size . ')',
                    'quantity' => $item->quantity,
                    'price' => (int) $item->price,
                    'category' => 'Fashion',
                ],
            )
            ->toArray();

        $xenditItems[] = [
            'name' => 'Ongkos Kirim (' . $order->courier_name . ' ' . $order->courier_service . ')',
            'quantity' => 1,
            'price' => (int) $order->shipping_cost,
            'category' => 'Shipping',
        ];

        try {
            $invoice = $this->xendit->createInvoice([
                'external_id' => $order->order_number,
                'amount' => (int) $order->total_amount,
                'payer_email' => auth()->user()->email,
                'description' => 'Pembayaran ' . $order->order_number . ' — Caysie',
                'customer_name' => $order->receiver_name,
                'customer_phone' => $order->receiver_phone,
                'success_redirect_url' => route('user.payment.success', $order),
                'failure_redirect_url' => route('user.payment.failed', $order),
                'items' => $xenditItems,
            ]);

            if (isset($invoice['error'])) {
                throw new \RuntimeException($invoice['error']);
            }

            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'xendit_invoice_url' => $invoice['invoice_url'],
                'xendit_expires_at' => $invoice['expiry_date'] ?? null,
            ]);

            Log::info('[Checkout] Invoice Xendit dibuat ulang (retry)', [
                'order_id' => $order->id,
                'invoice_url' => $invoice['invoice_url'],
            ]);

            return redirect($invoice['invoice_url']);
        } catch (\Throwable $e) {
            Log::error('[Checkout] Retry Xendit error: ' . $e->getMessage());
            return redirect()->route('user.payment.show', $order)->with('error', 'Gagal membuat link pembayaran baru. Silakan coba lagi beberapa saat.');
        }
    }

    // ── Upload bukti pembayaran (transfer manual) ────────────
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Hanya pesanan yang masih pending & belum lewat deadline yang bisa diupload buktinya
        if (!$order->can_pay) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa diupload bukti pembayarannya.');
        }

        $request->validate(
            [
                'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'payment_proof.required' => 'Foto bukti transfer wajib diupload.',
                'payment_proof.image' => 'File harus berupa gambar.',
                'payment_proof.mimes' => 'Format gambar harus JPG atau PNG.',
                'payment_proof.max' => 'Ukuran gambar maksimal 2MB.',
            ],
        );

        // Hapus bukti lama jika user upload ulang
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'waiting_confirmation',
        ]);

        Log::info('[Checkout] Bukti pembayaran diupload', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);

        return redirect()
            ->route('user.orders')
            ->with('success', 'Bukti pembayaran untuk pesanan #' . $order->order_code . ' berhasil dikirim. Admin akan memverifikasi dalam 1×24 jam.');
    }

    // ── Callback: Xendit redirect setelah BERHASIL bayar ─────
    public function paymentSuccess(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('user.payment-success', compact('order'));
    }

    // ── Callback: Xendit redirect setelah GAGAL/batal ────────
    public function paymentFailed(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('user.payment-failed', compact('order'));
    }

    // ── API: Cek status pembayaran (polling dari JS) ──────────
    public function checkStatus(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Ambil status terbaru dari Xendit jika ada invoice ID
        if ($order->xendit_invoice_id) {
            $invoice = $this->xendit->getInvoice($order->xendit_invoice_id);

            $xenditStatus = $invoice['status'] ?? null;

            // Map status Xendit → status order kita
            if ($xenditStatus === 'PAID' && $order->status === 'pending') {
                $order->update([
                    'status' => 'confirmed',
                    'paid_at' => now(),
                ]);
            } elseif ($xenditStatus === 'EXPIRED' && $order->status === 'pending') {
                $order->update(['status' => 'cancelled']);
            }
        }

        return response()->json([
            'order_status' => $order->fresh()->status,
            'status_label' => $order->fresh()->status_label,
            'paid_at' => $order->fresh()->paid_at?->format('d M Y H:i'),
            'invoice_url' => $order->xendit_invoice_url,
        ]);
    }
}
