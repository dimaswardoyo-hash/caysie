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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'waiting_confirmation', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');

            // Penerima
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_province');
            $table->string('receiver_city');
            $table->string('receiver_district')->nullable();
            $table->string('receiver_village')->nullable();
            $table->string('receiver_postal_code')->nullable();
            $table->text('receiver_address');

            // Kurir — disimpan dari hasil cek ongkir
            $table->string('courier_code'); // jne, jnt, sicepat, dst.
            $table->string('courier_name'); // JNE, J&T Express, dst.
            $table->string('courier_service'); // REG, OKE, YES, EZ, dst.
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->string('shipping_estimate')->nullable(); // "2-3"
            $table->string('tracking_number')->nullable(); // diisi admin saat shipped

            // Keuangan
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Pembayaran
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
