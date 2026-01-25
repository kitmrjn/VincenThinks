<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\Question;
use App\Models\Department;
use App\Models\Course;
use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Fetch quick stats for the main Dashboard.
     */
    public function getDashboardStats(): array
    {
        return [
            'pendingReports' => Report::count(),
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'totalQuestions' => Question::count(),
            'recentLogs' => AuditLog::with('admin')->latest()->take(5)->get()
        ];
    }

    /**
     * Fetch comprehensive stats for the Analytics page.
     * Cached for 5 seconds for near real-time updates.
     */
    public function getFullAnalytics(): array
    {
        return Cache::remember('analytics_full_stats', 5, function () {
            $solvedCount = Question::whereNotNull('best_answer_id')->count();
            $totalQuestions = Question::count();

            return [
                'total_users' => User::count(),
                'total_questions' => $totalQuestions,
                'total_solved' => $solvedCount,
                'total_departments' => Department::count(),
                'total_courses' => Course::count(),
                'total_categories' => Category::count(),
                'pending_reports' => Report::count(),
                // Calculated field for charts
                'unsolved_count' => $totalQuestions - $solvedCount,
            ];
        });
    }

    /**
     * Generate data for the "Questions over Time" chart (Last 7 Days).
     * Cached for 5 seconds.
     */
    public function getGrowthChartData(): array
    {
        return Cache::remember('analytics_growth_chart', 5, function () {
            $growthQuery = Question::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            return [
                'labels' => $growthQuery->pluck('date'),
                'data' => $growthQuery->pluck('count')
            ];
        });
    }

    /**
     * Generate data for the "Questions per Category" chart.
     * Cached for 5 seconds.
     */
    public function getCategoryDistribution(): array
    {
        return Cache::remember('analytics_category_dist', 5, function () {
            $distQuery = Category::withCount('questions')->get();
            return [
                'labels' => $distQuery->pluck('name'),
                'data' => $distQuery->pluck('questions_count')
            ];
        });
    }

    /**
     * Get trending questions and top contributors.
     */
    public function getTopContent(): array
    {
        return [
            'trendingQuestions' => Question::with('category')->orderBy('views', 'desc')->take(5)->get(),
            'topContributors' => User::withCount('answers')->orderBy('answers_count', 'desc')->take(5)->get()
        ];
    }
}