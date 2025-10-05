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
        Schema::table('quizzes', function (Blueprint $table) {
            // Tambahkan kolom setelah 'duration_in_minutes'
            // Default 0 berarti tidak terbatas
            $table->unsignedTinyInteger('max_attempts')->default(0)->after('duration_in_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('max_attempts');
        });
    }
};
