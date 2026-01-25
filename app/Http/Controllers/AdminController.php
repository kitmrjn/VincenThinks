<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Reply;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\BannedWord;
use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Services\AnalyticsService; 

class AdminController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    // --- HELPER: Filter & Link Events (The "Ghostbuster" Logic) ---
    private function formatAndFilterEvents($events) {
        return $events->map(function ($event) {
            $meta = $event->meta_data;
            $url = '#'; 
            $isValid = true; // We assume it's valid until proven otherwise

            switch ($event->type) {
                // 1. Handle New Content Events
                case 'new_question':
                case 'question_solved':
                    if (isset($meta['question_id'])) {
                        // Existence Check: Does the Question still exist?
                        if (Question::where('id', $meta['question_id'])->exists()) {
                            $url = route('question.show', $meta['question_id']);
                        } else {
                            $isValid = false; // Question deleted -> Hide Event
                        }
                    } else { $isValid = false; }
                    break;

                case 'new_answer':
                    if (isset($meta['question_id']) && isset($meta['answer_id'])) {
                        if (Answer::where('id', $meta['answer_id'])->exists()) {
                            $url = route('question.show', $meta['question_id']);
                        } else {
                            $isValid = false; // Answer deleted -> Hide Event
                        }
                    } else { $isValid = false; }
                    break;
                
                case 'new_reply':
                    if (isset($meta['answer_id']) && isset($meta['reply_id'])) {
                        $answer = Answer::withoutGlobalScope('published')->find($meta['answer_id']);
                        if ($answer && Reply::where('id', $meta['reply_id'])->exists()) {
                            $url = route('question.show', $answer->question_id);
                        } else {
                            $isValid = false; // Reply or Parent Answer deleted -> Hide Event
                        }
                    } else { $isValid = false; }
                    break;

                // 2. Handle AI Flags (The main issue you saw)
                case 'ai_flagged':
                    $model = $meta['model'] ?? '';
                    $id = $meta['id'] ?? 0;
                    
                    if ($model === 'Question') {
                        if (Question::withoutGlobalScope('published')->where('id', $id)->exists()) {
                            $url = route('admin.moderation');
                        } else { $isValid = false; }
                    } elseif ($model === 'Answer') {
                        if (Answer::withoutGlobalScope('published')->where('id', $id)->exists()) {
                            $url = route('admin.moderation');
                        } else { $isValid = false; }
                    } elseif ($model === 'Reply') {
                        if (Reply::withoutGlobalScope('published')->where('id', $id)->exists()) {
                            $url = route('admin.moderation');
                        } else { $isValid = false; }
                    } else {
                        $isValid = false;
                    }
                    break;

                // 3. Handle Reports
                case 'report_filed':
                    $url = route('admin.reports');
                    // Optional: Check if reported question still exists
                    if (isset($meta['question_id']) && !Question::where('id', $meta['question_id'])->exists()) {
                        $isValid = false; 
                    }
                    break;
            }

            if (!$isValid) return null; // Mark for removal

            $event->action_url = $url;
            return $event;
        })
        ->filter() // Remove the nulls (deleted items)
        ->values(); // Reset array keys
    }

    // --- DASHBOARD (COMMAND CENTER) ---
    public function index() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $data = $this->analyticsService->getDashboardStats();
        
        // Fetch a bit more than 10 to account for filtering deleted ones
        $rawEvents = AnalyticsEvent::latest()->take(20)->get();
        
        // [UPDATED] Use the new robust filter
        $data['recentEvents'] = $this->formatAndFilterEvents($rawEvents)->take(10);
        
        $data['latestEventId'] = $rawEvents->first()->id ?? 0;

        return view('admin.dashboard', $data);
    }

    // --- REAL-TIME POLLING ENDPOINT (DASHBOARD) ---
    public function fetchEvents(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $lastId = $request->input('last_id', 0);

        $rawEvents = AnalyticsEvent::where('id', '>', $lastId)
            ->latest()
            ->take(20)
            ->get();

        // [UPDATED] Use the new robust filter
        $events = $this->formatAndFilterEvents($rawEvents);

        $pendingReports = Report::count();
        $aiPending = Question::where('status', 'pending_review')->count() 
                   + Answer::where('status', 'pending_review')->count()
                   + Reply::where('status', 'pending_review')->count();

        return response()->json([
            'events' => $events,
            'stats' => [
                'pending_reports' => $pendingReports,
                'pending_reviews' => $aiPending
            ]
        ]);
    }

    // --- ANALYTICS (STATS VIEW) ---
    public function analytics() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $stats = $this->analyticsService->getFullAnalytics();
        $growthData = $this->analyticsService->getGrowthChartData();
        $catData = $this->analyticsService->getCategoryDistribution();
        $topContent = $this->analyticsService->getTopContent();

        $charts = [
            'growth' => $growthData,
            'distribution' => $catData,
            'resolution' => [
                'labels' => ['Solved', 'Unsolved'], 
                'data' => [$stats['total_solved'], $stats['unsolved_count']]
            ]
        ];

        return view('admin.analytics', array_merge(
            ['stats' => $stats, 'charts' => $charts], 
            $topContent
        ));
    }

    // --- REAL-TIME ANALYTICS DATA (POLLING) ---
    public function fetchAnalyticsData() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        // These methods are now cached in the service
        $stats = $this->analyticsService->getFullAnalytics();
        $growthData = $this->analyticsService->getGrowthChartData();
        $catData = $this->analyticsService->getCategoryDistribution();

        return response()->json([
            'stats' => $stats,
            'charts' => [
                'growth' => $growthData,
                'distribution' => $catData,
                'resolution' => [
                    'labels' => ['Solved', 'Unsolved'],
                    'data' => [$stats['total_solved'], $stats['unsolved_count']]
                ]
            ]
        ]);
    }

    // --- BANNED WORDS MANAGEMENT ---
    public function bannedWords() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $words = BannedWord::latest()->get();
        return view('admin.banned-words', compact('words'));
    }

    public function storeBannedWord(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $request->validate(['word' => 'required|string|unique:banned_words,word|max:50']);
        BannedWord::create(['word' => strtolower($request->word)]);
        Cache::forget('banned_words_list');
        $this->logAction('Added Banned Word', null, "Word: {$request->word}");
        return back()->with('success', 'Word added to blocklist.');
    }

    public function deleteBannedWord($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $word = BannedWord::findOrFail($id);
        $wordText = $word->word;
        $word->delete();
        Cache::forget('banned_words_list');
        $this->logAction('Deleted Banned Word', null, "Word: {$wordText}");
        return back()->with('success', 'Word removed from blocklist.');
    }

    // --- MODERATION QUEUE ---
    public function moderation() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $flaggedQuestions = Question::withoutGlobalScope('published')
            ->where('status', 'pending_review')->with('user')->latest()->get();
        $flaggedAnswers = Answer::withoutGlobalScope('published')
            ->where('status', 'pending_review')->with('user', 'question')->latest()->get();
        $flaggedReplies = Reply::withoutGlobalScope('published')
            ->where('status', 'pending_review')->with('user', 'answer.question')->latest()->get();

        return view('admin.moderation', compact('flaggedQuestions', 'flaggedAnswers', 'flaggedReplies'));
    }

    public function approveContent(Request $request, $type, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        if ($type === 'question') $item = Question::withoutGlobalScope('published')->findOrFail($id);
        elseif ($type === 'answer') $item = Answer::withoutGlobalScope('published')->findOrFail($id);
        elseif ($type === 'reply') $item = Reply::withoutGlobalScope('published')->findOrFail($id);
        else abort(404);

        $item->status = 'published';
        $item->save();
        $this->logAction('Approved Content', $item->user, ucfirst($type) . " ID {$id} marked as published.");
        return back()->with('success', 'Content approved and published.');
    }

    public function deleteFlaggedContent(Request $request, $type, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        if ($type === 'question') {
            $item = Question::withoutGlobalScope('published')->findOrFail($id);
            // 1. Clean Standard Events
            AnalyticsEvent::whereJsonContains('meta_data->question_id', (int)$id)->delete();
            // 2. Clean AI Flag Events
            AnalyticsEvent::where('type', 'ai_flagged')
                ->whereJsonContains('meta_data->id', (int)$id)
                ->whereJsonContains('meta_data->model', 'Question')
                ->delete();

        } elseif ($type === 'answer') {
            $item = Answer::withoutGlobalScope('published')->findOrFail($id);
            AnalyticsEvent::whereJsonContains('meta_data->answer_id', (int)$id)->delete();
            AnalyticsEvent::where('type', 'ai_flagged')
                ->whereJsonContains('meta_data->id', (int)$id)
                ->whereJsonContains('meta_data->model', 'Answer')
                ->delete();

        } elseif ($type === 'reply') {
            $item = Reply::withoutGlobalScope('published')->findOrFail($id);
            AnalyticsEvent::whereJsonContains('meta_data->reply_id', (int)$id)->delete();
            AnalyticsEvent::where('type', 'ai_flagged')
                ->whereJsonContains('meta_data->id', (int)$id)
                ->whereJsonContains('meta_data->model', 'Reply')
                ->delete();
        } else {
            abort(404);
        }

        $item->delete();
        $this->logAction('Deleted Flagged Content', $item->user, ucfirst($type) . " ID {$id} permanently deleted.");
        return back()->with('success', 'Content deleted.');
    }

    // --- REPORTS (USER MODERATION) ---
    public function reports() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $reports = Report::with(['question', 'question.user'])->latest()->get();
        return view('admin.reports', compact('reports'));
    }

    // --- AUDIT LOGS VIEW ---
    public function auditLogs() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $logs = AuditLog::with('admin')->latest()->paginate(20);
        return view('admin.audit-logs', compact('logs'));
    }

    // --- CATEGORIES ---
    public function categories() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $categories = Category::withCount('questions')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $request->validate([
            'name' => 'required|unique:categories,name',
            'acronym' => 'nullable|string|max:10|unique:categories,acronym'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'acronym' => $request->acronym ? strtoupper($request->acronym) : null,
            'slug' => Str::slug($request->name)
        ]);
        $this->logAction('Created Category', null, "Category: {$category->name} ({$category->acronym})");
        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    public function deleteCategory($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();
        $this->logAction('Deleted Category', null, "Category Name: {$name}");
        return redirect()->route('admin.categories')->with('success', 'Category deleted.');
    }

    // --- COURSES & STRANDS ---
    public function courses() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $courses = Course::orderBy('type')->orderBy('name')->get();
        return view('admin.courses', compact('courses'));
    }

    public function storeCourse(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'required|string|max:20', 
            'type' => 'required|in:College,SHS,JHS,Other',
            'other_type' => 'required_if:type,Other|nullable|string|max:50',
        ]);

        $finalType = $request->type === 'Other' ? $request->other_type : $request->type;
        $course = Course::create([
            'name' => $request->name,
            'acronym' => strtoupper($request->acronym),
            'type' => $finalType
        ]);
        $this->logAction('Created Course', null, "Course: {$course->acronym}");
        return redirect()->route('admin.courses')->with('success', 'Course/Strand added successfully!');
    }

    public function deleteCourse($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $course = Course::findOrFail($id);
        $acronym = $course->acronym;
        $course->delete(); 
        $this->logAction('Deleted Course', null, "Course: {$acronym}");
        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }

    public function deleteQuestion($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $question = Question::findOrFail($id);
        $title = $question->title;
        $owner = $question->user;
        
        AnalyticsEvent::whereJsonContains('meta_data->question_id', (int)$id)->delete();
        AnalyticsEvent::where('type', 'ai_flagged')
            ->whereJsonContains('meta_data->id', (int)$id)
            ->whereJsonContains('meta_data->model', 'Question')
            ->delete();

        $question->delete(); 
        $this->logAction('Deleted Question', $owner, "Title: {$title}");
        return redirect()->back()->with('success', 'Question deleted successfully.');
    }

    public function dismissReport($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Report dismissed.');
    }

    // --- SETTINGS METHODS ---
    public function generalSettings() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneralSettings(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $verification = $request->has('verification_required') ? '1' : '0';
        Setting::updateOrCreate(['key' => 'verification_required'], ['value' => $verification]);
        $aiModeration = $request->has('use_ai_moderation') ? '1' : '0';
        Setting::updateOrCreate(['key' => 'use_ai_moderation'], ['value' => $aiModeration]);
        if ($request->has('edit_time_limit')) {
            Setting::updateOrCreate(['key' => 'edit_time_limit'], ['value' => $request->edit_time_limit]);
        }
        $this->logAction('Updated General Settings', null, "AI: $aiModeration, Verify: $verification");
        return redirect()->back()->with('success', 'General settings updated.');
    }

    public function emailSettings() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmailSettings(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $keys = ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
        foreach ($keys as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
        }
        $this->logAction('Updated Email SMTP Settings');
        return redirect()->back()->with('success', 'Email configuration updated.');
    }

    // --- USER MANAGEMENT ---
    public function users(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $query = User::with(['course', 'departmentInfo'])->withCount(['questions', 'answers']);
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('teacher_number', 'like', "%{$search}%");
            });
        }
        if ($request->has('role')) {
            if ($request->role === 'admin') $query->where('is_admin', true);
            elseif ($request->role === 'teacher') $query->where('member_type', 'teacher');
            elseif ($request->role === 'student') $query->where('member_type', 'student');
            elseif ($request->role === 'banned') $query->where('is_banned', true);
        }
        $users = $query->latest()->paginate(10);
        $allDepartments = Department::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        return view('admin.users', compact('users', 'allDepartments', 'courses'));
    }

    public function toggleUserBan($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        if (Auth::id() == $id) return back()->with('error', 'You cannot ban yourself.');
        
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();
        $actionName = $user->is_banned ? 'Banned User' : 'Unbanned User';
        $detailText = $user->is_banned ? 'Access suspended indefinitely.' : 'Access restored.';
        $this->logAction($actionName, $user, $detailText);
        $status = $user->is_banned ? 'banned' : 'activated';
        return back()->with('success', "User has been {$status}.");
    }

    public function verifyUser($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();
        $this->logAction('Manually Verified User', $user, 'Marked email as verified.');
        return back()->with('success', 'User email verified manually.');
    }

    public function promoteToAdmin($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $user = User::findOrFail($id);
        $user->is_admin = true;
        $user->save();
        $this->logAction('Promoted to Admin', $user, 'Granted Administrator privileges.');
        return back()->with('success', 'User promoted to Administrator.');
    }

    public function deleteUser($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        if (Auth::id() == $id) return back()->with('error', 'You cannot delete yourself.');
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();
        $this->logAction('Deleted User', $userName, 'Account permanently removed.');
        return back()->with('success', 'User deleted successfully.');
    }

    public function updateUser(Request $request, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'student_number' => 'nullable|string|max:50|unique:users,student_number,' . $user->id,
            'teacher_number' => 'nullable|string|max:50|unique:users,teacher_number,' . $user->id,
            'course_id' => 'nullable|exists:courses,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user->update($request->only([
            'name', 'email', 'student_number', 'teacher_number', 'course_id', 'department_id'
        ]));
        $changes = $user->getChanges();
        unset($changes['updated_at']); 
        if (!empty($changes)) {
            $details = "Changed: " . implode(', ', array_keys($changes));
            $this->logAction('Updated Profile', $user, $details);
        }
        return back()->with('success', 'User information updated successfully.');
    }

    public function resetUserPassword(Request $request, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user = User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();
        $this->logAction('Forced Password Reset', $user, 'Admin forced a password reset.');
        return back()->with('success', 'Password has been reset successfully.');
    }

    private function logAction($action, $target = null, $details = null) {
        $targetName = 'N/A';
        if ($target instanceof User) { $targetName = $target->name; } 
        elseif (is_string($target)) { $targetName = $target; }

        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'target_user_name' => $targetName,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public function departments() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $departments = Department::all();
        return view('admin.departments', compact('departments'));
    }

    public function storeDepartment(Request $request) {
        $request->validate(['name' => 'required|unique:departments,name', 'acronym' => 'nullable|string|max:50']);
        Department::create($request->only(['name', 'acronym']));
        return back()->with('success', 'Department added successfully.');
    }

    public function deleteDepartment($id) {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department deleted.');
    }
}