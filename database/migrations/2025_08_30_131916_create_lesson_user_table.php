<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_user', function (Blueprint $table) {
            // Kunci utama gabungan untuk memastikan seorang user hanya bisa menyelesaikan satu pelajaran sekali.
            $table->primary(['user_id', 'lesson_id']);

            // Foreign key ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Foreign key ke tabel lessons
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');

            // Timestamp untuk mencatat kapan pelajaran diselesaikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_user');
    }
};
