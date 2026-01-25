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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // e.g., 'question_posted', 'ai_flagged', 'user_banned'
            $table->string('message');
            $table->json('meta_data')->nullable(); // Store IDs, scores, etc.
            $table->timestamps(); // We will query 'created_at' for the 5s polling window
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};