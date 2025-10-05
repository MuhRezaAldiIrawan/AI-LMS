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
                // Tambahkan kolom baru 'completed_at' setelah 'enrolled_at'
                $table->timestamp('completed_at')->nullable()->after('enrolled_at');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('course_user', function (Blueprint $table) {
                // Hapus kolom jika migration di-rollback
                $table->dropColumn('completed_at');
            });
        }
};
