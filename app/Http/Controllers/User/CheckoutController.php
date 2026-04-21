<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Daftar kurir statis (nanti bisa integrasi API RajaOngkir)
    private array $couriers = [['name' => 'JNE', 'service' => 'REG', 'cost' => 12000, 'estimate' => 3], ['name' => 'JNE', 'service' => 'YES', 'cost' => 25000, 'estimate' => 1], ['name' => 'J&T', 'service' => 'REG', 'cost' => 11000, 'estimate' => 3], ['name' => 'SiCepat', 'service' => 'BEST', 'cost' => 10000, 'estimate' => 2], ['name' => 'Pos Indonesia', 'service' => 'Paket Kilat', 'cost' => 9000, 'estimate' => 4]];

    public function index()
    {
        $carts = Cart::with(['product', 'productSize'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kamu kosong.');
        }

        $subtotal = $carts->sum('subtotal');
        $couriers = $this->couriers;
        $cartCount = $carts->sum('quantity');

        return view('user.checkout', compact('carts', 'subtotal', 'couriers', 'cartCount'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'receiver_name' => 'required|string|max:100',
                'receiver_phone' => 'required|string|max:20',
                'receiver_address' => 'required|string',
                'receiver_province' => 'required|string',
                'receiver_city' => 'required|string',
                'receiver_postal_code' => 'required|string|max:10',
                'courier_index' => 'required|integer|between:0,' . (count($this->couriers) - 1),
            ],
            [
                'receiver_name.required' => 'Nama penerima wajib diisi.',
                'receiver_phone.required' => 'Nomor HP penerima wajib diisi.',
                'receiver_address.required' => 'Alamat lengkap wajib diisi.',
                'courier_index.required' => 'Pilih jasa pengiriman.',
            ],
        );

        $carts = Cart::with(['product', 'productSize'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong.');
        }

        $courier = $this->couriers[$request->courier_index];
        $subtotal = $carts->sum('subtotal');
        $total = $subtotal + $courier['cost'];

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code' => 'CSY-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'receiver_province' => $request->receiver_province,
                'receiver_city' => $request->receiver_city,
                'receiver_postal_code' => $request->receiver_postal_code,
                'courier_name' => $courier['name'],
                'courier_service' => $courier['service'],
                'shipping_cost' => $courier['cost'],
                'shipping_estimate' => $courier['estimate'],
                'subtotal' => $subtotal,
                'total' => $total,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($carts as $cart) {
                $price = $cart->product->price_sale ?? $cart->product->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'product_size' => $cart->productSize->size,
                    'product_image' => $cart->product->image,
                    'quantity' => $cart->quantity,
                    'price' => $price,
                    'subtotal' => $price * $cart->quantity,
                ]);

                // Kurangi stok
                $cart->productSize->decrement('stock', $cart->quantity);
            }

            // Kosongkan keranjang
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('user.orders.show', $order)->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Coba lagi.');
        }
    }

    // Upload bukti bayar
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($order->payment_proof) {
            \Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim!');
    }
}
