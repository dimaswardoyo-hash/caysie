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

        // CRUD Produk
        Route::resource('products', ProductController::class);
        Route::delete('products/{product}/images', [ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
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
    });
