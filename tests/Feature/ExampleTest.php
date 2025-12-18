<?php

namespace Tests\Feature;

// --- FIXED: Uncommented this line ---
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    // --- FIXED: Added this trait to run migrations ---
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Create a dummy user or category if needed, but for the homepage
        // simply having the tables exist (via RefreshDatabase) is usually enough
        // unless your view strictly requires data to not crash.
        
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}