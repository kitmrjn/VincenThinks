<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Randomly decide if this fake user is a student or teacher
        $type = fake()->randomElement(['student', 'teacher']);
        
        // Generate the specific ID format: "AY" + Year + "-" + 5 digits
        $idNumber = 'AY' . fake()->year() . '-' . fake()->numerify('#####');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            
            // --- NEW FIELDS ---
            'member_type' => $type,
            'avatar' => null,
            'is_admin' => false,
            'is_banned' => false,
            
            // Fill the correct ID based on type
            'student_number' => $type === 'student' ? $idNumber : null,
            'teacher_number' => $type === 'teacher' ? $idNumber : null,
            
            // Note: We leave course_id and department_id null by default 
            // to avoid errors if those tables are empty during testing.
            'course_id' => null,
            'department_id' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    
    // Helper to easily create an Admin
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}