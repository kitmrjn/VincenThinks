<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('member_type')->default('student')->after('email'); // student, teacher
            $table->string('student_number')->nullable()->unique()->after('member_type');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->after('student_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['member_type', 'student_number', 'course_id']);
        });
    }
};