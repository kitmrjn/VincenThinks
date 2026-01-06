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
        Schema::table('questions', function (Blueprint $table) {
            // We use 'unsignedBigInteger' because it references an ID
            $table->foreignId('best_answer_id')->nullable()->constrained('answers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['best_answer_id']);
            $table->dropColumn('best_answer_id');
        });
    }
};
