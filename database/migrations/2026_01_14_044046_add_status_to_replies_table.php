<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('replies', function (Blueprint $table) {
            // Default to 'published' so old replies stay visible
            $table->string('status')->default('published')->after('content');
        });
    }

    public function down()
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};