<?php

use Illuminate\Support\Facades\Route;

// Import Controller
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\RevenueController as AdminRevenue;
use App\Http\Controllers\Admin\UserController as AdminUser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ✅ Halaman publik — bisa diakses siapa saja
Route::get('/', [UserDashboard::class, 'index'])->name('home');

// Auth routes (Breeze)
require __DIR__ . '/auth.php';

// ===== ADMIN ROUTES =====
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Manage Produk
        Route::resource('products', ProductController::class);
        Route::delete('products/{product}/images', [ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');

        // Manage Pesanan
        Route::get('orders', [AdminOrder::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrder::class, 'updateStatus'])->name('orders.status');
        Route::patch('orders/{order}/confirm-payment', [AdminOrder::class, 'confirmPayment'])->name('orders.confirm');

        // Manage Pemasukan
        Route::get('revenue', [AdminRevenue::class, 'index'])->name('revenue.index');

        // Manage User
        Route::get('users', [AdminUser::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUser::class, 'show'])->name('users.show');
        Route::patch('users/{user}/toggle-ban', [AdminUser::class, 'toggleBan'])->name('users.toggle-ban');
    });

// ===== USER ROUTES =====
Route::prefix('user')
    ->name('user.')
    ->middleware(['auth', 'user'])
    ->group(function () {
        Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

        // Toko & produk
        Route::get('/shop', [ShopController::class, 'index'])->name('shop');
        Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('product.show');

        // Keranjang
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::post('/orders/{order}/proof', [CheckoutController::class, 'uploadProof'])->name('orders.proof');

        // Pesanan
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

        // Upload bukti pembayaran
        Route::post('/orders/{order}/proof', [CheckoutController::class, 'uploadProof'])->name('orders.proof');
    });
