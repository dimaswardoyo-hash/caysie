<?php

use Illuminate\Support\Facades\Route;

// Import Controller
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\ProductController;

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
    });
