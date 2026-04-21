<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
        return view('user.orders', compact('orders', 'cartCount'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load('items');
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
        return view('user.order-detail', compact('order', 'cartCount'));
    }
}
