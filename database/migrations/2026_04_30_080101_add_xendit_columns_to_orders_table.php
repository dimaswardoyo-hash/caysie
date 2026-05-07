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
            // Xendit
            if (!Schema::hasColumn('orders', 'xendit_invoice_id')) {
                $table->string('xendit_invoice_id')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'xendit_invoice_url')) {
                $table->text('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            }
            if (!Schema::hasColumn('orders', 'xendit_expires_at')) {
                $table->timestamp('xendit_expires_at')->nullable()->after('xendit_invoice_url');
            }

            // Metode pembayaran (diisi oleh webhook)
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('xendit_expires_at');
            }
            if (!Schema::hasColumn('orders', 'payment_channel')) {
                $table->string('payment_channel')->nullable()->after('payment_method');
            }

            // Waktu bayar
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_channel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['xendit_invoice_id', 'xendit_invoice_url', 'xendit_expires_at', 'payment_method', 'payment_channel', 'paid_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
