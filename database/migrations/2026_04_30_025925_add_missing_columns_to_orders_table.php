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
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'courier_code')) {
                $table->string('courier_code')->after('receiver_postal_code');
            }
            if (!Schema::hasColumn('orders', 'courier_service')) {
                $table->string('courier_service')->after('courier_name');
            }
            if (!Schema::hasColumn('orders', 'shipping_estimate')) {
                $table->string('shipping_estimate')->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('shipping_estimate');
            }
            if (!Schema::hasColumn('orders', 'receiver_district')) {
                $table->string('receiver_district')->nullable()->after('receiver_city');
            }
            if (!Schema::hasColumn('orders', 'receiver_village')) {
                $table->string('receiver_village')->nullable()->after('receiver_district');
            }
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('subtotal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['order_number', 'courier_code', 'courier_service', 'shipping_estimate', 'tracking_number', 'receiver_district', 'receiver_village', 'subtotal', 'total_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
