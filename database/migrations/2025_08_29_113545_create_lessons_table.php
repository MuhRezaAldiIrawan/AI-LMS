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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->integer('order')->default(0);
            $table->string('content_type'); // 'video', 'text', 'file'
            $table->string('video_url')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->integer('duration_in_minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
