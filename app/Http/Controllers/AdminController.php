<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // 1. DASHBOARD (Reports View)
    public function index() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $reports = Report::with(['question', 'question.user'])->latest()->get();
        // We still fetch category count just for the sidebar stats if needed
        $categoryCount = Category::count(); 

        return view('admin.dashboard', compact('reports', 'categoryCount'));
    }

    // 2. CATEGORIES PAGE (New Separate Page)
    public function categories() {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }

        $categories = Category::withCount('questions')->get();

        return view('admin.categories', compact('categories'));
    }

    // 3. Store Category
    public function storeCategory(Request $request) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $request->validate(['name' => 'required|unique:categories,name']);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        // Redirect back to the categories page
        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    // 4. Delete Category
    public function deleteCategory($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        Category::destroy($id);
        return redirect()->route('admin.categories')->with('success', 'Category deleted.');
    }

    // 5. Delete Question
    public function deleteQuestion($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $question = Question::findOrFail($id);
        $question->delete(); 
        return redirect()->back()->with('success', 'Question deleted successfully.');
    }

    // 6. Dismiss Report
    public function dismissReport($id) {
        if (!Auth::check() || !Auth::user()->is_admin) { abort(403); }
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Report dismissed.');
    }
}