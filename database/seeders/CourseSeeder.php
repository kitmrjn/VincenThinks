<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // College
            ['type' => 'College', 'acronym' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology'],
            ['type' => 'College', 'acronym' => 'BSHM', 'name' => 'Bachelor of Science in Hospitality Management'],
            ['type' => 'College', 'acronym' => 'BSTM', 'name' => 'Bachelor of Science in Tourism Management'],
            ['type' => 'College', 'acronym' => 'BSBA', 'name' => 'Bachelor of Science in Business Administration'],
            ['type' => 'College', 'acronym' => 'BSA', 'name' => 'Bachelor of Science in Accountancy'],
            ['type' => 'College', 'acronym' => 'BSCRIM', 'name' => 'Bachelor of Science in Criminology'],
            ['type' => 'College', 'acronym' => 'BSED', 'name' => 'Bachelor of Secondary Education'],
            ['type' => 'College', 'acronym' => 'BEED', 'name' => 'Bachelor of Elementary Education'],
            ['type' => 'College', 'acronym' => 'BSPSY', 'name' => 'Bachelor of Science in Psychology'],
            ['type' => 'College', 'acronym' => 'BPE', 'name' => 'Bachelor of Physical Education'],
            ['type' => 'College', 'acronym' => 'BSECE', 'name' => 'Bachelor of Science in Early Childhood Education'],
            // Senior High
            ['type' => 'SHS', 'acronym' => 'STEM', 'name' => 'Science, Technology, Engineering, and Mathematics'],
            ['type' => 'SHS', 'acronym' => 'ABM', 'name' => 'Accountancy, Business, and Management'],
            ['type' => 'SHS', 'acronym' => 'GAS', 'name' => 'General Academic Strand'],
            ['type' => 'SHS', 'acronym' => 'HE', 'name' => 'Home Economics'],
            ['type' => 'SHS', 'acronym' => 'ICT', 'name' => 'Information and Communications Technology'],
            // Junior High
            ['type' => 'JHS', 'acronym' => 'JHS', 'name' => 'Junior High School'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}