<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Course; // Import Course model

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // 1. Create a dummy course because Student registration requires a valid course_id
        $course = Course::create([
            'name' => 'Bachelor of Science in Computer Science',
            'acronym' => 'BSCS',
            'type' => 'College'
        ]);

        // 2. Submit a payload that satisfies ALL validation rules
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            
            // --- REQUIRED NEW FIELDS ---
            'member_type' => 'student',
            'student_number' => 'AY2023-12345', // Must match regex ^AY\d{4}-\d{5}$
            'course_id' => $course->id,         // Must exist in database
        ]);

        $this->assertAuthenticated();
        
        // Note: Your controller redirects to 'home' (route name), verify if it's 'home' or 'dashboard' in your web.php
        // Based on your RegisteredUserController, it uses route('home', absolute: false)
        $response->assertRedirect(route('home', absolute: false)); 
    }
}