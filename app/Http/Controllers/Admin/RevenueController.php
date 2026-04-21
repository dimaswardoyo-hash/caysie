<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? null;

        // ── Ringkasan Utama ──────────────────────────────
        $totalRevenue = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->sum('total');

        $monthRevenue = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $totalOrders = Order::count();
        $paidOrders = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // ── Grafik Bulanan (tahun dipilih) ───────────────
        $monthlyData = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartMonths = [];
        $chartRevenue = [];
        $chartOrders = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonths[] = \Carbon\Carbon::create()->month($m)->isoFormat('MMM');
            $chartRevenue[] = $monthlyData->get($m)?->revenue ?? 0;
            $chartOrders[] = $monthlyData->get($m)?->orders ?? 0;
        }

        // ── Produk Terlaris ──────────────────────────────
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))->groupBy('product_name')->orderByDesc('total_qty')->take(5)->get();

        // ── Transaksi Terbaru ────────────────────────────
        $recentOrders = Order::with('user')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->latest('paid_at')
            ->take(8)
            ->get();

        // ── Tahun tersedia untuk filter ──────────────────
        $years = Order::selectRaw('YEAR(created_at) as year')->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.revenue.index', compact('totalRevenue', 'monthRevenue', 'totalOrders', 'paidOrders', 'pendingOrders', 'chartMonths', 'chartRevenue', 'chartOrders', 'topProducts', 'recentOrders', 'year', 'years'));
    }
}
