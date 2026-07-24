<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Query utama shop: Product::active()->where('category', ...)
            $table->index(['is_active', 'category'], 'products_is_active_category_index');

            // Query beranda: Product::active()->featured()
            $table->index(['is_active', 'is_featured'], 'products_is_active_is_featured_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Riwayat pesanan user: Order::where('user_id', ..)->where('status', ..)
            $table->index(['user_id', 'status'], 'orders_user_id_status_index');

            // Dashboard admin: filter by status saja + badge count per status
            $table->index('status', 'orders_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_is_active_category_index');
            $table->dropIndex('products_is_active_is_featured_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_id_status_index');
            $table->dropIndex('orders_status_index');
        });
    }
};
