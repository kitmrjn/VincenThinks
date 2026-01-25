<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController; 
use Illuminate\Support\Facades\Auth;

// ===========================
// LANDING PAGE & FEED LOGIC
// ===========================

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('feed');
    }
    return view('welcome');
})->name('landing');

Route::get('/feed', [ForumController::class, 'index'])->name('feed');
Route::redirect('/home', '/feed')->name('home');

// ===========================
// PUBLIC ROUTES
// ===========================
Route::get('/question/{id}', [ForumController::class, 'show'])->name('question.show');
Route::get('/user/{id}', [UserProfileController::class, 'show'])->name('user.profile');

// ===========================
// AUTHENTICATED ROUTES
// ===========================
Route::middleware(['auth'])->group(function () {

    // ADMIN ROUTES
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    
    // [NEW] Real-Time Polling Route
    Route::get('/admin/analytics/events', [AdminController::class, 'fetchEvents'])->name('admin.analytics.events');
    
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    
    // Moderation
    Route::get('/admin/moderation', [AdminController::class, 'moderation'])->name('admin.moderation');
    Route::post('/admin/moderation/{type}/{id}/approve', [AdminController::class, 'approveContent'])->name('admin.moderation.approve');
    Route::delete('/admin/moderation/{type}/{id}/delete', [AdminController::class, 'deleteFlaggedContent'])->name('admin.moderation.delete');

    // Banned Words
    Route::get('/admin/banned-words', [AdminController::class, 'bannedWords'])->name('admin.banned_words');
    Route::post('/admin/banned-words', [AdminController::class, 'storeBannedWord'])->name('admin.banned_words.store');
    Route::delete('/admin/banned-words/{id}', [AdminController::class, 'deleteBannedWord'])->name('admin.banned_words.delete');

    // Core Actions
    Route::delete('/admin/question/{id}/delete', [AdminController::class, 'deleteQuestion'])->name('admin.delete_question');
    Route::delete('/admin/report/{id}/dismiss', [AdminController::class, 'dismissReport'])->name('admin.dismiss_report');
    Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit_logs');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/ban', [AdminController::class, 'toggleUserBan'])->name('admin.users.ban');
    Route::post('/admin/users/{id}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
    Route::post('/admin/users/{id}/promote', [AdminController::class, 'promoteToAdmin'])->name('admin.users.promote');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::match(['post', 'patch'], '/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.reset_password');

    // Content Management
    Route::get('/admin/departments', [AdminController::class, 'departments'])->name('admin.departments');
    Route::post('/admin/department', [AdminController::class, 'storeDepartment'])->name('admin.department.store');
    Route::delete('/admin/department/{id}', [AdminController::class, 'deleteDepartment'])->name('admin.department.delete');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/category', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/admin/category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');

    Route::get('/admin/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::post('/admin/course', [AdminController::class, 'storeCourse'])->name('admin.course.store');
    Route::delete('/admin/course/{id}', [AdminController::class, 'deleteCourse'])->name('admin.course.delete');

    // Settings
    Route::prefix('admin/settings')->name('admin.settings.')->group(function() {
        Route::get('/general', [AdminController::class, 'generalSettings'])->name('general');
        Route::post('/general', [AdminController::class, 'updateGeneralSettings'])->name('general.update');
        Route::get('/email', [AdminController::class, 'emailSettings'])->name('email');
        Route::post('/email', [AdminController::class, 'updateEmailSettings'])->name('email.update');
    });

    // ===========================
    // USER ROUTES
    // ===========================
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/user/avatar', [UserProfileController::class, 'updateAvatar'])->name('user.avatar.update');

    // Questions & Answers
    Route::middleware(['verified.custom'])->group(function () {
        Route::post('/post-question', [ForumController::class, 'storeQuestion'])->name('question.store');
        Route::post('/question/{id}/answer', [ForumController::class, 'storeAnswer'])->name('answer.store');
        Route::post('/answer/{id}/reply', [ForumController::class, 'storeReply'])->name('reply.store');
    });

    Route::post('/question/{id}/report', [ForumController::class, 'reportQuestion'])->name('question.report');
    Route::post('/answer/{id}/rate', [ForumController::class, 'rateAnswer'])->name('answer.rate');
    Route::post('/answer/{id}/best', [ForumController::class, 'markAsBest'])->name('answer.best');

    Route::get('/notifications/{id}/read', [ForumController::class, 'markNotification'])->name('notifications.read_one');
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    // Editing
    Route::get('/question/{id}/edit', [ForumController::class, 'editQuestion'])->name('question.edit');
    Route::put('/question/{id}', [ForumController::class, 'updateQuestion'])->name('question.update');
    Route::delete('/question/{id}', [ForumController::class, 'destroyQuestion'])->name('question.destroy');

    Route::get('/answer/{id}/edit', [ForumController::class, 'editAnswer'])->name('answer.edit');
    Route::put('/answer/{id}', [ForumController::class, 'updateAnswer'])->name('answer.update');
    Route::delete('/answer/{id}', [ForumController::class, 'destroyAnswer'])->name('answer.destroy');

    Route::get('/reply/{id}/edit', [ForumController::class, 'editReply'])->name('reply.edit');
    Route::put('/reply/{id}', [ForumController::class, 'updateReply'])->name('reply.update');
    Route::delete('/reply/{id}', [ForumController::class, 'destroyReply'])->name('reply.destroy');

    // Image Upload
    Route::post('/upload-editor-image', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate(['image' => 'required|image|max:5120']);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads', 'public');
                return response()->json(['url' => asset('storage/' . $path)]);
            }
            return response()->json(['message' => 'No image found'], 400);
        } catch (\Exception $e) { return response()->json(['message' => $e->getMessage()], 500); }
    })->name('editor.image.upload');
});

require __DIR__.'/auth.php';