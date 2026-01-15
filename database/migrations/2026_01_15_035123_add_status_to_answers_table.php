<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FIX: Check if the column exists first
        if (!Schema::hasColumn('answers', 'status')) {
            Schema::table('answers', function (Blueprint $table) {
                $table->string('status')->default('published')->after('content');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('answers', 'status')) {
            Schema::table('answers', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};