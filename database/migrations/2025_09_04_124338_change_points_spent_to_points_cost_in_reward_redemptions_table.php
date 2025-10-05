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
        Schema::table('reward_redemptions', function (Blueprint $table) {
            // Mengubah nama kolom 'points_spent' menjadi 'points_cost'
            $table->renameColumn('points_spent', 'points_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_redemptions', function (Blueprint $table) {
            // Mengembalikan nama kolom jika migrasi di-rollback
            $table->renameColumn('points_cost', 'points_spent');
        });
    }
};
