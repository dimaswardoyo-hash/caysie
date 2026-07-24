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
        DB::statement("ALTER TABLE product_sizes MODIFY size ENUM('S','M','L','XL','XXL','One Size') NOT NULL");
    }

    public function down(): void
    {
        // Pastikan tidak ada baris yang masih pakai 'One Size' sebelum rollback,
        // supaya enum lama tidak menolak data yang sudah ada.
        DB::table('product_sizes')->where('size', 'One Size')->delete();

        DB::statement("ALTER TABLE product_sizes MODIFY size ENUM('S','M','L','XL','XXL') NOT NULL");
    }
};
