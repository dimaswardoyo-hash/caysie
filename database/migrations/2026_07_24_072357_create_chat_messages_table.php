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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('sender', ['user', 'admin']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Dipakai untuk: polling pesan baru per user (user_id + created_at),
            // dan menghitung unread per user di inbox admin (user_id + is_read).
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
