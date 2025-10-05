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
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            // Ubah kolom 'option_id' untuk mengizinkan nilai NULL
            $table->foreignId('option_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            // Kembalikan seperti semula jika migrasi di-rollback
            $table->foreignId('option_id')->nullable(false)->change();
        });
    }
};
