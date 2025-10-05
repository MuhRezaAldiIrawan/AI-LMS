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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->unique()->nullable()->after('email');
            $table->date('join_date')->nullable()->after('nik');
            $table->string('position')->nullable()->after('join_date');
            $table->string('division')->nullable()->after('position');

            // Menambahkan foreign key ke tabel locations
            $table->foreignId('location_id')->nullable()->after('division')->constrained('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Urutan drop penting, drop foreign key dulu
            $table->dropForeign(['location_id']);

            $table->dropColumn([
                'nik',
                'join_date',
                'position',
                'division',
                'location_id',
            ]);
        });
    }
};
