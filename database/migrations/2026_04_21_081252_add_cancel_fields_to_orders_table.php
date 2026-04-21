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
            $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            $table->string('cancel_reason')->nullable()->after('cancelled_at');
            $table->string('cancelled_by')->nullable()->after('cancel_reason')->comment('user atau admin');
            $table->timestamp('payment_deadline')->nullable()->after('cancelled_by')->comment('batas waktu bayar 24 jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancel_reason', 'cancelled_by', 'payment_deadline']);
        });
    }
};
