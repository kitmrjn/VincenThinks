<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRefactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_loads_with_service_data()
    {
        // 1. Create an Admin
        $admin = User::factory()->create(['is_admin' => true]);

        // 2. Seed some data to count
        Question::factory()->count(3)->create();
        Report::create(['user_id' => $admin->id, 'question_id' => 1, 'reason' => 'Spam']);

        // 3. Visit Dashboard
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // 4. Assert it works
        $response->assertStatus(200);
        $response->assertViewHas('totalQuestions', 3);
        $response->assertViewHas('pendingReports', 1);
    }

    public function test_analytics_page_loads_charts()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        // Create a category to test the "Distribution" chart logic
        $category = Category::factory()->create(['name' => 'Laravel']);
        Question::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $response->assertStatus(200);
        
        // Assert the View received the data from AnalyticsService
        $response->assertViewHas('stats'); 
        $response->assertViewHas('charts');
        
        // Check if specific data points exist in the response
        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['total_questions']);
    }
}