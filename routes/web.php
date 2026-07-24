<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\TestimonialController;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\RevenueController as AdminRevenue;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonial;
use App\Http\Controllers\Admin\AdminChatController as AdminChat;
use App\Http\Controllers\User\ChatController as UserChat;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Api\OngkirController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\XenditWebhookController;
use App\Http\Controllers\ProfileController;

// ── Halaman publik ───────────────────────────────────────
Route::get('/', [UserDashboard::class, 'index'])->name('home');
require __DIR__ . '/auth.php';

// ── XENDIT WEBHOOK (tanpa auth, tanpa CSRF — lihat VerifyCsrfToken.php) ──
Route::post('/webhooks/xendit', [XenditWebhookController::class, 'handle'])->name('webhooks.xendit');

// ── DASHBOARD & PROFIL (dipakai layouts/navigation.blade.php & resources/views/profile/*) ──
Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── ADMIN ────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class);
        Route::delete('products/{product}/images', [ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::get('orders', [AdminOrder::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrder::class, 'updateStatus'])->name('orders.status');
        Route::patch('orders/{order}/confirm-payment', [AdminOrder::class, 'confirmPayment'])->name('orders.confirm');
        Route::post('orders/{order}/shipping/resolve-area', [AdminOrder::class, 'resolveShippingArea'])->name('orders.shipping.resolve-area');
        Route::post('orders/{order}/shipping/rates', [AdminOrder::class, 'shippingRates'])->name('orders.shipping.rates');
        Route::post('orders/{order}/shipping/generate', [AdminOrder::class, 'generateShipment'])->name('orders.shipping.generate');
        Route::get('revenue', [AdminRevenue::class, 'index'])->name('revenue.index');
        Route::get('users', [AdminUser::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUser::class, 'show'])->name('users.show');
        Route::patch('users/{user}/toggle-ban', [AdminUser::class, 'toggleBan'])->name('users.toggle-ban');

        // Testimoni
        Route::get('testimonials', [AdminTestimonial::class, 'index'])->name('testimonials.index');
        Route::patch('testimonials/{testimonial}/toggle', [AdminTestimonial::class, 'toggleApprove'])->name('testimonials.toggle');
        Route::put('testimonials/{testimonial}', [AdminTestimonial::class, 'update'])->name('testimonials.update');
        Route::delete('testimonials/{testimonial}', [AdminTestimonial::class, 'destroy'])->name('testimonials.destroy');

        // Chat
        Route::get('chat', [AdminChat::class, 'index'])->name('chat.index');
        Route::get('chat/unread-count', [AdminChat::class, 'unreadCount'])->name('chat.unread-count');
        Route::get('chat/{user}', [AdminChat::class, 'show'])->name('chat.show');
        Route::get('chat/{user}/messages', [AdminChat::class, 'messages'])->name('chat.messages');
        Route::post('chat/{user}/send', [AdminChat::class, 'send'])->name('chat.send');
        Route::delete('chat/{user}', [AdminChat::class, 'destroy'])->name('chat.destroy');
    });

// ── USER ─────────────────────────────────────────────────
Route::prefix('user')
    ->name('user.')
    ->middleware(['auth', 'user'])
    ->group(function () {
        Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
        Route::get('/shop', [ShopController::class, 'index'])->name('shop');
        Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('product.show');
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        // Halaman pembayaran Xendit
        Route::get('/payment/{order}', [CheckoutController::class, 'showPayment'])->name('payment.show');
        Route::get('/payment/{order}/success', [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/{order}/failed', [CheckoutController::class, 'paymentFailed'])->name('payment.failed');
        Route::get('/payment/{order}/status', [CheckoutController::class, 'checkStatus'])->name('payment.status');
        Route::post('/payment/{order}/retry', [CheckoutController::class, 'retryPayment'])->name('payment.retry');

        // Pesanan
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
        Route::post('/orders/{order}/proof', [CheckoutController::class, 'uploadProof'])->name('orders.proof');

        // Testimoni
        Route::post('/testimoni', [TestimonialController::class, 'store'])->name('testimoni.store');
        Route::delete('/testimoni', [TestimonialController::class, 'destroy'])->name('testimoni.destroy');

        // Chat widget (popup, polling)
        Route::get('/chat/messages', [UserChat::class, 'messages'])->name('chat.messages');
        Route::post('/chat/send', [UserChat::class, 'send'])->name('chat.send');
        Route::delete('/chat', [UserChat::class, 'destroy'])->name('chat.destroy');
    });

// ── API ──────────────────────────────────────────────────
Route::prefix('api')
    ->name('api.')
    ->middleware('auth')
    ->group(function () {
        Route::get('wilayah/provinces', [WilayahController::class, 'provinces'])->name('wilayah.provinces');
        Route::get('wilayah/cities', [WilayahController::class, 'cities'])->name('wilayah.cities');
        Route::get('wilayah/districts', [WilayahController::class, 'districts'])->name('wilayah.districts');
        Route::get('wilayah/villages', [WilayahController::class, 'villages'])->name('wilayah.villages');
        Route::post('ongkir/check', [OngkirController::class, 'check'])->name('ongkir.check');
        Route::get('location/search', [LocationController::class, 'search'])->name('location.search');
        Route::post('tracking/track', [TrackingController::class, 'track'])->name('tracking.track');
        Route::get('tracking/order/{order}', [TrackingController::class, 'trackOrder'])->name('tracking.order');
    });
