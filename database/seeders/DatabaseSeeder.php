<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the SUPER ADMIN account
        User::factory()->create([
            'name' => 'Vincent Admin',
            'email' => 'admin@vincenthinks.com',
            'password' => Hash::make('password'), // Password is 'password'
            'is_admin' => true,
            'member_type' => 'teacher', // Admins are usually teachers or staff
            'email_verified_at' => now(),
        ]);

        // 2. Create a standard Test Student (for testing regular features)
        User::factory()->create([
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'member_type' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}