<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer; // Added
use App\Models\Reply;  // Added
use App\Models\Department;
use App\Models\Course;
use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Fetch quick stats for the main Dashboard (Lifetime / Recent).
     */
    public function getDashboardStats(): array
    {
        // [FIXED] Calculate total pending items (Reports + AI Flags) to match JS polling logic
        $aiPendingCount = Question::where('status', 'pending_review')->count()
                        + Answer::where('status', 'pending_review')->count()
                        + Reply::where('status', 'pending_review')->count();

        $totalPending = Report::count() + $aiPendingCount;

        return [
            'pendingReports' => $totalPending, // Now returns the combined total (e.g., 50)
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'totalQuestions' => Question::count(),
            'recentLogs' => AuditLog::with('admin')->latest()->take(5)->get()
        ];
    }

    /**
     * Helper to determine date range
     */
    private function getDateRange($range)
    {
        return match ($range) {
            'day' => now()->subDay(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(), // 'week'
        };
    }

    /**
     * Fetch comprehensive stats.
     */
    public function getFullAnalytics($range = 'week'): array
    {
        return Cache::remember("analytics_full_stats_{$range}", 5, function () use ($range) {
            $startDate = $this->getDateRange($range);

            // Flow Metrics (Filtered by Date)
            $newUsers = User::where('created_at', '>=', $startDate)->count();
            $newQuestions = Question::where('created_at', '>=', $startDate)->count();
            $solvedCount = Question::where('created_at', '>=', $startDate)
                ->whereNotNull('best_answer_id')
                ->count();

            // Stock Metrics (Snapshots - Always Total)
            $totalDepartments = Department::count();
            $totalCourses = Course::count();
            $totalCategories = Category::count();
            $pendingReports = Report::count(); 

            return [
                'total_users' => $newUsers,
                'total_questions' => $newQuestions,
                'total_solved' => $solvedCount,
                'total_departments' => $totalDepartments,
                'total_courses' => $totalCourses,
                'total_categories' => $totalCategories,
                'pending_reports' => $pendingReports,
                'unsolved_count' => $newQuestions - $solvedCount,
            ];
        });
    }

    /**
     * Generate data for the "Questions over Time" chart.
     */
    public function getGrowthChartData($range = 'week'): array
    {
        return Cache::remember("analytics_growth_chart_{$range}", 5, function () use ($range) {
            $startDate = $this->getDateRange($range);
            
            if ($range === 'day') {
                $select = "DATE_FORMAT(created_at, '%H:00') as label";
            } elseif ($range === 'year') {
                $select = "DATE_FORMAT(created_at, '%Y-%m') as label";
            } else {
                $select = "DATE(created_at) as label";
            }

            $query = Question::selectRaw("$select, COUNT(*) as count")
                ->where('created_at', '>=', $startDate)
                ->groupBy('label')
                ->orderBy('label', 'ASC')
                ->get();

            return [
                'labels' => $query->pluck('label'),
                'data' => $query->pluck('count')
            ];
        });
    }

    /**
     * Generate data for the "Questions per Category" chart.
     */
    public function getCategoryDistribution($range = 'week'): array
    {
        return Cache::remember("analytics_category_dist_{$range}", 5, function () use ($range) {
            $startDate = $this->getDateRange($range);

            $distQuery = Category::withCount(['questions' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->get()
            ->where('questions_count', '>', 0); 

            return [
                'labels' => $distQuery->pluck('name')->values(),
                'data' => $distQuery->pluck('questions_count')->values()
            ];
        });
    }

    /**
     * Generate Solved vs Unsolved for the selected range.
     */
    public function getResolutionStats($range = 'week'): array
    {
        return Cache::remember("analytics_resolution_{$range}", 5, function () use ($range) {
            $startDate = $this->getDateRange($range);

            $totalInPeriod = Question::where('created_at', '>=', $startDate)->count();
            $solvedInPeriod = Question::where('created_at', '>=', $startDate)
                ->whereNotNull('best_answer_id')
                ->count();

            return [
                'labels' => ['Solved', 'Unsolved'],
                'data' => [$solvedInPeriod, $totalInPeriod - $solvedInPeriod]
            ];
        });
    }

    /**
     * Get trending content and contributors filtered by range.
     */
    public function getTopContent($range = 'week'): array
    {
        return Cache::remember("analytics_top_content_{$range}", 5, function () use ($range) {
            $startDate = $this->getDateRange($range);

            return [
                'trendingQuestions' => Question::with('category')
                    ->where('created_at', '>=', $startDate) // Created within range
                    ->orderBy('views', 'desc')
                    ->take(5)
                    ->get(),
                
                'topContributors' => User::withCount(['answers' => function ($query) use ($startDate) {
                        $query->where('created_at', '>=', $startDate); // Count answers only within range
                    }])
                    ->having('answers_count', '>', 0) // Only users active in this period
                    ->orderBy('answers_count', 'desc')
                    ->take(5)
                    ->get()
            ];
        });
    }
}