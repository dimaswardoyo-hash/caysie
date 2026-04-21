<?php
namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NavbarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share data ke semua view yang pakai layout user
        View::composer('layouts.user', function ($view) {
            if (!auth()->check() || !auth()->user()->isUser()) {
                return;
            }

            $userId = auth()->id();

            // Jumlah item keranjang
            $cartCount = Cart::where('user_id', $userId)->sum('quantity');

            // 3 pesanan terbaru untuk dropdown
            $recentOrders = Order::with('items')->where('user_id', $userId)->latest()->take(3)->get();

            // Badge: pesanan yang butuh perhatian user
            // (pending belum upload bukti, atau status baru berubah)
            $pendingCount = Order::where('user_id', $userId)->where('status', 'pending')->whereNull('payment_proof')->whereNotNull('payment_deadline')->where('payment_deadline', '>', now())->count();

            $activeOrderCount = Order::where('user_id', $userId)
                ->whereIn('status', ['pending', 'paid', 'processing', 'shipped'])
                ->count();

            // Statistik ringkasan
            $orderStats = [
                'active' => $activeOrderCount,
                'delivered' => Order::where('user_id', $userId)->where('status', 'delivered')->count(),
                'pending' => $pendingCount,
            ];

            $view->with(compact('cartCount', 'recentOrders', 'pendingCount', 'activeOrderCount', 'orderStats'));
        });
    }
}
