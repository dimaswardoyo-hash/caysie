<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $featured = Product::active()->featured()->with('sizes')->take(4)->get();
        $newArrivals = Product::active()->with('sizes')->latest()->take(8)->get();
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        return view('user.dashboard', compact('featured', 'newArrivals', 'cartCount'));
    }
}
