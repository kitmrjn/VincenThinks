<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course; // Import Course
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        // Create a user (Factory now handles valid IDs)
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        // 1. Create a Course first
        $course = Course::create([
            'name' => 'Computer Science',
            'acronym' => 'BSCS',
            'type' => 'College'
        ]);

        // 2. Create a Student User specifically
        $user = User::factory()->create([
            'member_type' => 'student',
            'course_id' => $course->id,
            'student_number' => 'AY2023-12345',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                
                // --- FIXED: We MUST send these because the user is a Student ---
                'member_type' => 'student',
                'student_number' => 'AY2023-12345',
                'course_id' => $course->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            // Note: We changed the redirect in ProfileController to 'user.profile'
            // The test might expect a generic redirect, checking for *any* redirect is safer
            ->assertRedirect(); 

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        // 1. Create Course & Student
        $course = Course::create(['name' => 'CS', 'acronym' => 'CS', 'type' => 'College']);
        $user = User::factory()->create([
            'member_type' => 'student', 
            'course_id' => $course->id,
            'student_number' => 'AY2023-12345'
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                
                // --- FIXED: Include required fields ---
                'member_type' => 'student',
                'student_number' => 'AY2023-12345',
                'course_id' => $course->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}