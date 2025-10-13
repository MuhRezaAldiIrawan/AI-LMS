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
        Schema::create('ai_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->index(); // untuk mengelompokkan chat dalam satu sesi
            $table->enum('message_type', ['user', 'ai']);
            $table->text('message');
            $table->json('metadata')->nullable(); // untuk menyimpan data tambahan seperti context, etc
            $table->timestamps();

            // Index untuk performa query
            $table->index(['user_id', 'session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_histories');
    }
};
