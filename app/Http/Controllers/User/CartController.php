<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['product', 'productSize'])
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $carts->sum('subtotal');
        $cartCount = $carts->sum('quantity');

        return view('user.cart', compact('carts', 'subtotal', 'cartCount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_size_id' => 'required|exists:product_sizes,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $size = ProductSize::findOrFail($request->product_size_id);

        if ($size->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi untuk ukuran ini.');
        }

        $cart = Cart::where([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'product_size_id' => $request->product_size_id,
        ])->first();

        if ($cart) {
            $newQty = $cart->quantity + $request->quantity;
            if ($newQty > $size->stock) {
                return back()->with('error', 'Total di keranjang melebihi stok tersedia.');
            }
            $cart->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'product_size_id' => $request->product_size_id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        if ($request->quantity > $cart->productSize->stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }
        $cart->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
