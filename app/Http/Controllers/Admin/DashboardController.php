<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total_amount'),
            'revenue_this_month' => Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('status', 'confirmed')->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $lowStock = Product::with('sizes')->get()->filter(fn($p) => $p->total_stock <= 5 && $p->total_stock > 0)->take(5);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStock'));
    }
}
