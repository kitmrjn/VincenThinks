<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\BannedWord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    // --- DASHBOARD (COMMAND CENTER) ---
    public function index() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $pendingReports = Report::count(); 
        $newUsersToday = User::whereDate('created_at', today())->count();
        $totalQuestions = Question::count();
        $recentLogs = AuditLog::with('admin')->latest()->take(5)->get();

        return view('admin.dashboard', compact('pendingReports', 'newUsersToday', 'totalQuestions', 'recentLogs'));
    }

    // --- BANNED WORDS MANAGEMENT ---
    public function bannedWords() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        
        $words = BannedWord::latest()->get();
        return view('admin.banned-words', compact('words'));
    }

    public function storeBannedWord(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $request->validate([
            'word' => 'required|string|unique:banned_words,word|max:50'
        ]);

        BannedWord::create(['word' => strtolower($request->word)]);
        
        // Clear cache so the new word applies immediately
        Cache::forget('banned_words_list');

        $this->logAction('Added Banned Word', null, "Word: {$request->word}");

        return back()->with('success', 'Word added to blocklist.');
    }

    public function deleteBannedWord($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $word = BannedWord::findOrFail($id);
        $wordText = $word->word;
        $word->delete();

        // Clear cache
        Cache::forget('banned_words_list');

        $this->logAction('Deleted Banned Word', null, "Word: {$wordText}");

        return back()->with('success', 'Word removed from blocklist.');
    }

    // --- MODERATION QUEUE ---
    public function moderation() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        // Use withoutGlobalScope to fetch hidden content
        $flaggedQuestions = Question::withoutGlobalScope('published')
            ->where('status', 'pending_review')
            ->with('user')
            ->latest()
            ->get();

        $flaggedAnswers = Answer::withoutGlobalScope('published')
            ->where('status', 'pending_review')
            ->with('user', 'question')
            ->latest()
            ->get();

        return view('admin.moderation', compact('flaggedQuestions', 'flaggedAnswers'));
    }

    public function approveContent(Request $request, $type, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        if ($type === 'question') {
            $item = Question::withoutGlobalScope('published')->findOrFail($id);
        } else {
            $item = Answer::withoutGlobalScope('published')->findOrFail($id);
        }

        $item->status = 'published';
        $item->save();

        $this->logAction('Approved Content', $item->user, ucfirst($type) . " ID {$id} marked as published.");

        return back()->with('success', 'Content approved and published.');
    }

    public function deleteFlaggedContent(Request $request, $type, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        if ($type === 'question') {
            $item = Question::withoutGlobalScope('published')->findOrFail($id);
        } else {
            $item = Answer::withoutGlobalScope('published')->findOrFail($id);
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

    // --- ANALYTICS (STATS) ---
    public function analytics() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $pendingReportCount = Report::count(); 

        $stats = [
            'total_users' => User::count(),
            'total_questions' => Question::count(),
            'total_solved' => Question::whereNotNull('best_answer_id')->count(),
            'total_departments' => Department::count(),
            'total_courses' => Course::count(),
            'total_categories' => Category::count(),
            'pending_reports' => $pendingReportCount,
        ];

        $growthQuery = Question::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $distQuery = Category::withCount('questions')->get();

        $solvedCount = $stats['total_solved'];
        $unsolvedCount = $stats['total_questions'] - $solvedCount;

        $charts = [
            'growth' => ['labels' => $growthQuery->pluck('date'), 'data' => $growthQuery->pluck('count')],
            'distribution' => ['labels' => $distQuery->pluck('name'), 'data' => $distQuery->pluck('questions_count')],
            'resolution' => ['labels' => ['Solved', 'Unsolved'], 'data' => [$solvedCount, $unsolvedCount]]
        ];

        $trendingQuestions = Question::with('category')->orderBy('views', 'desc')->take(5)->get();
        $topContributors = User::withCount('answers')->orderBy('answers_count', 'desc')->take(5)->get();

        return view('admin.analytics', compact('stats', 'charts', 'trendingQuestions', 'topContributors'));
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

    // --- CONTENT MANAGEMENT ---
    public function deleteQuestion($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $question = Question::findOrFail($id);
        $title = $question->title;
        $owner = $question->user;
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
        if ($request->has('edit_time_limit')) {
            Setting::updateOrCreate(['key' => 'edit_time_limit'], ['value' => $request->edit_time_limit]);
        }

        $this->logAction('Updated General Settings');

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
        ], [
            'student_number.unique' => 'This Student Number is already assigned to another user.',
            'teacher_number.unique' => 'This Teacher Number is already assigned to another user.',
            'email.unique' => 'This email address is already in use by another account.',
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

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        $this->logAction('Forced Password Reset', $user, 'Admin forced a password reset.');

        return back()->with('success', 'Password has been reset successfully.');
    }

    private function logAction($action, $target = null, $details = null) 
    {
        $targetName = 'N/A';

        if ($target instanceof User) {
            $targetName = $target->name;
        } 
        elseif (is_string($target)) {
            $targetName = $target;
        }

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
        $request->validate([
            'name' => 'required|unique:departments,name', 
            'acronym' => 'nullable|string|max:50'
        ]);

        Department::create($request->only(['name', 'acronym']));

        return back()->with('success', 'Department added successfully.');
    }

    public function deleteDepartment($id) {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department deleted.');
    }
}