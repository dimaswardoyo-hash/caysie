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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Identitas penerima
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->text('receiver_address');
            $table->string('receiver_province');
            $table->string('receiver_city');
            $table->string('receiver_postal_code');

            // Pengiriman
            $table->string('courier_name');
            $table->string('courier_service');
            $table->integer('shipping_cost');
            $table->integer('shipping_estimate')->nullable()->comment('hari');

            // Total
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);

            // Status
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');

            // Pembayaran
            $table->string('payment_method')->default('transfer');
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
