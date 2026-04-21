<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('sizes')->latest();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('price');
        }

        $products = $query->paginate(12)->withQueryString();
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        return view('user.shop', compact('products', 'cartCount'));
    }

    public function show(Product $product)
    {
        $product->load('sizes');
        $related = Product::active()->where('category', $product->category)->where('id', '!=', $product->id)->take(4)->get();
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        return view('user.product-detail', compact('product', 'related', 'cartCount'));
    }
}
