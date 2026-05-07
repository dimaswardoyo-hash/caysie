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
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->string('awb')->unique()->index(); // Nomor resi
            $table->string('courier'); // Kurir (jne, tiki, pos, dll)
            $table->string('status')->nullable(); // Status pengiriman
            $table->json('history')->nullable(); // Riwayat tracking
            $table->json('delivery_status')->nullable(); // Status pengiriman detail
            $table->json('manifest')->nullable(); // Manifest pengiriman
            $table->timestamp('last_checked_at')->nullable(); // Terakhir dicek
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackings');
    }
};
