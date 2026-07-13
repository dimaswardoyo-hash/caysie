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
            // Referensi ke sisi Biteship — dipakai saat generate resi otomatis
            if (!Schema::hasColumn('orders', 'biteship_order_id')) {
                $table->string('biteship_order_id')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'biteship_area_id')) {
                $table->string('biteship_area_id')->nullable()->after('biteship_order_id');
            }
            if (!Schema::hasColumn('orders', 'biteship_courier_company')) {
                $table->string('biteship_courier_company')->nullable()->after('biteship_area_id');
            }
            if (!Schema::hasColumn('orders', 'biteship_courier_type')) {
                $table->string('biteship_courier_type')->nullable()->after('biteship_courier_company');
            }
            if (!Schema::hasColumn('orders', 'biteship_status')) {
                // Diisi & diupdate oleh webhook Biteship (confirmed, allocated, picking_up, picked, dropping_off, delivered, dst.)
                $table->string('biteship_status')->nullable()->after('biteship_courier_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['biteship_order_id', 'biteship_area_id', 'biteship_courier_company', 'biteship_courier_type', 'biteship_status'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
