<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache 5 menit: angka statistik ini di-hit di setiap load halaman admin,
        // tapi datanya tidak perlu real-time detik-per-detik. Mengurangi beban
        // 8 query aggregate berulang kalau admin bolak-balik / auto-refresh halaman.
        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(5), function () {
            return [
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
        });

        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Sebelumnya: Product::with('sizes')->get()->filter(...) — menarik SEMUA
        // produk + semua ukurannya ke memory PHP lalu filter manual. Aman untuk
        // puluhan produk, tapi tumbuh linear dan makin lambat seiring katalog
        // membesar. Diganti agregasi SUM(stock) langsung di level SQL.
        $lowStock = Product::query()->select('products.*')->join('product_sizes', 'products.id', '=', 'product_sizes.product_id')->groupBy('products.id')->havingRaw('SUM(product_sizes.stock) <= 5 AND SUM(product_sizes.stock) > 0')->orderByRaw('SUM(product_sizes.stock) ASC')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStock'));
    }
}
