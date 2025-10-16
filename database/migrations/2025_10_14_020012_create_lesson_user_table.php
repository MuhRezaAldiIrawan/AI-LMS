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
        // Sudah ada migration create dan add column terpisah, tidak perlu menambah kolom di sini
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu drop kolom di sini
    }
};
