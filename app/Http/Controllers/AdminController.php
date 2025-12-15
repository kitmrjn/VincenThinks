<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Course; 
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

    // --- CATEGORIES ---
    public function categories() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $categories = Category::withCount('questions')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $request->validate(['name' => 'required|unique:categories,name']);
        Category::create(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    public function deleteCategory($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        Category::destroy($id);
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
            // UPDATED: Allow 'Other' in the validation list
            'type' => 'required|in:College,SHS,JHS,Other',
            // UPDATED: Require 'other_type' input if type is 'Other'
            'other_type' => 'required_if:type,Other|nullable|string|max:50',
        ]);

        // Logic: If type is 'Other', use the text input value. Otherwise use the dropdown value.
        $finalType = $request->type === 'Other' ? $request->other_type : $request->type;

        Course::create([
            'name' => $request->name,
            'acronym' => strtoupper($request->acronym),
            'type' => $finalType
        ]);

        return redirect()->route('admin.courses')->with('success', 'Course/Strand added successfully!');
    }

    public function deleteCourse($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $course = Course::findOrFail($id);
        $course->delete(); 
        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }

    // --- CONTENT MANAGEMENT ---
    public function deleteQuestion($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $question = Question::findOrFail($id);
        $question->delete(); 
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
        return redirect()->back()->with('success', 'Email configuration updated.');
    }
}