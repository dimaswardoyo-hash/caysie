<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ── Daftar semua pesanan ─────────────────────────────
    public function index(Request $request)
    {
        $query = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest();

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Auto-cancel pesanan expired
        $this->autoExpireOrders();

        $orders = $query->paginate(10)->withQueryString();
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        // Hitung per status untuk badge filter
        $statusCounts = Order::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('user.orders', compact('orders', 'cartCount', 'statusCounts'));
    }

    // ── Detail pesanan ───────────────────────────────────
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load('items');
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
        return view('user.order-detail', compact('order', 'cartCount'));
    }

    // ── Batalkan pesanan (oleh user) ─────────────────────
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Validasi: hanya bisa cancel jika pending atau paid
        if (!$order->can_cancel) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan. Status pesanan: ' . $order->status_label);
        }

        $request->validate(
            [
                'cancel_reason' => 'required|string|max:255',
            ],
            [
                'cancel_reason.required' => 'Alasan pembatalan wajib diisi.',
            ],
        );

        DB::beginTransaction();
        try {
            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $size = $product->sizes()->where('size', $item->product_size)->first();
                    if ($size) {
                        $size->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason,
                'cancelled_by' => 'user',
            ]);

            DB::commit();
            return redirect()
                ->route('user.orders')
                ->with('success', 'Pesanan #' . $order->order_code . ' berhasil dibatalkan. Stok produk telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan. Coba lagi.');
        }
    }

    // ── Beli lagi (re-order) ─────────────────────────────
    public function reorder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $added = 0;
        $skipped = 0;

        foreach ($order->items as $item) {
            $product = \App\Models\Product::active()->find($item->product_id);
            if (!$product) {
                $skipped++;
                continue;
            }

            $size = $product->sizes()->where('size', $item->product_size)->first();
            if (!$size || $size->stock <= 0) {
                $skipped++;
                continue;
            }

            $qty = min($item->quantity, $size->stock);

            $cart = Cart::where([
                'user_id' => auth()->id(),
                'product_id' => $item->product_id,
                'product_size_id' => $size->id,
            ])->first();

            if ($cart) {
                $newQty = min($cart->quantity + $qty, $size->stock);
                $cart->update(['quantity' => $newQty]);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                    'product_size_id' => $size->id,
                    'quantity' => $qty,
                ]);
            }
            $added++;
        }

        $msg = $added > 0 ? "{$added} produk berhasil ditambahkan ke keranjang." : '';
        if ($skipped > 0) {
            $msg .= " {$skipped} produk dilewati (stok habis/tidak tersedia).";
        }

        return redirect()->route('user.cart')->with('success', trim($msg));
    }

    // ── Auto expire pesanan yang lewat deadline ──────────
    private function autoExpireOrders(): void
    {
        $expired = Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->get();

        foreach ($expired as $order) {
            DB::beginTransaction();
            try {
                // Kembalikan stok
                foreach ($order->items as $item) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $size = $product->sizes()->where('size', $item->product_size)->first();
                        if ($size) {
                            $size->increment('stock', $item->quantity);
                        }
                    }
                }
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancel_reason' => 'Otomatis dibatalkan karena melewati batas waktu pembayaran.',
                    'cancelled_by' => 'system',
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }
}
