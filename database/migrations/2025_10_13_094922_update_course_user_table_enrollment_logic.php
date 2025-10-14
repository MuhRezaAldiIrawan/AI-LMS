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
        Schema::table('course_user', function (Blueprint $table) {
            // Pastikan enrolled_at nullable untuk mendukung logika baru
            // enrolled_at = NULL : User assigned to course but not enrolled yet
            // enrolled_at = timestamp : User actively enrolled (clicked enroll button)
            $table->timestamp('enrolled_at')->nullable()->change();

            // Tambah completed_at jika belum ada
            if (!Schema::hasColumn('course_user', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('enrolled_at');
            }

            // Add index untuk performance
            $table->index(['user_id', 'course_id', 'enrolled_at'], 'course_user_enrollment_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            // Drop index
            $table->dropIndex('course_user_enrollment_idx');
        });
    }
};
