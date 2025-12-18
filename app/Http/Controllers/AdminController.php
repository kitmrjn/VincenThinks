<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // --- DASHBOARD & CORE ---
    public function index() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $reports = Report::with(['question', 'question.user'])->latest()->get();
        $categoryCount = Category::count(); 
        return view('admin.dashboard', compact('reports', 'categoryCount'));
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
        $request->validate(['name' => 'required|unique:categories,name']);
        $category = Category::create(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        
        $this->logAction('Created Category', null, "Category Name: {$category->name}");
        
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

        // Eager load 'course' and 'department' for the table and modals
        $query = User::with(['course', 'department'])->withCount(['questions', 'answers']);

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
        
        // --- NEW: Fetch these for the Edit Modals ---
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
        // --- NEW: Better description ---
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

        // --- NEW: Better description ---
        $this->logAction('Manually Verified User', $user, 'Marked email as verified.');

        return back()->with('success', 'User email verified manually.');
    }

    public function promoteToAdmin($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $user = User::findOrFail($id);
        $user->is_admin = true;
        $user->save();

        // --- NEW: Better description ---
        $this->logAction('Promoted to Admin', $user, 'Granted Administrator privileges.');

        return back()->with('success', 'User promoted to Administrator.');
    }

    public function deleteUser($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        if (Auth::id() == $id) return back()->with('error', 'You cannot delete yourself.');
        
        $user = User::findOrFail($id);
        $userName = $user->name; // 1. Capture the name BEFORE deleting
        $user->delete();

        // 2. Pass $userName directly as the 2nd argument so it shows in the main column
        $this->logAction('Deleted User', $userName, 'Account permanently removed.');

        return back()->with('success', 'User deleted successfully.');
    }

    public function updateUser(Request $request, $id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Email must be unique, but ignore this current user's ID
            'email' => 'required|email|unique:users,email,' . $user->id,
            
            // Student Number must be unique, but ignore this current user's ID
            'student_number' => 'nullable|string|max:50|unique:users,student_number,' . $user->id,
            
            // Teacher Number must be unique, but ignore this current user's ID
            'teacher_number' => 'nullable|string|max:50|unique:users,teacher_number,' . $user->id,
            
            'course_id' => 'nullable|exists:courses,id',
            'department_id' => 'nullable|exists:departments,id',
        ], [
            // Custom error messages to make it clear why it failed
            'student_number.unique' => 'This Student Number is already assigned to another user.',
            'teacher_number.unique' => 'This Teacher Number is already assigned to another user.',
            'email.unique' => 'This email address is already in use by another account.',
        ]);

        // Update the user using only the validated fields
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

        // --- NEW: Better description ---
        $this->logAction('Forced Password Reset', $user, 'Admin forced a password reset.');

        return back()->with('success', 'Password has been reset successfully.');
    }

    private function logAction($action, $target = null, $details = null) 
    {
        // Default to N/A
        $targetName = 'N/A';

        // Check if $target is a User Object (Database Model)
        if ($target instanceof User) {
            $targetName = $target->name;
        } 
        // Check if $target is just a simple String (Text) - For deleted users
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
            'acronym' => 'nullable|string|max:10'
        ]);

        // Use only() instead of all() to ignore the security _token
        Department::create($request->only(['name', 'acronym']));

        return back()->with('success', 'Department added successfully.');
    }

    public function deleteDepartment($id) {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department deleted.');
    }
}