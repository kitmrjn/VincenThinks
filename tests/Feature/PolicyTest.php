<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use App\Models\Category; // Required to create a question
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_own_question()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);

        // Login as the owner and try to visit the edit page
        $response = $this->actingAs($user)->get(route('question.edit', $question->id));

        $response->assertStatus(200); // 200 OK
    }

    public function test_user_cannot_edit_others_question()
    {
        $owner = User::factory()->create();
        $hacker = User::factory()->create();
        $category = Category::factory()->create();
        $question = Question::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);

        // Login as "Hacker" and try to edit "Owner's" question
        $response = $this->actingAs($hacker)->get(route('question.edit', $question->id));

        $response->assertStatus(403); // 403 Forbidden
    }

    public function test_admin_can_delete_anything()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);

        // Admin tries to delete User's question
        $response = $this->actingAs($admin)->delete(route('question.destroy', $question->id));

        // Should redirect to home (success) and DB should be empty
        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}