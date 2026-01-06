<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Add user_id column
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Ensure a user can only rate a specific answer ONCE
            // This prevents duplicates at the database level
            $table->unique(['user_id', 'answer_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            //
        });
    }
};
