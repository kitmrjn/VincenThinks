<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController; 
use App\Notifications\NewActivity;
use Illuminate\Support\Facades\Auth;

// --- PUBLIC ROUTES ---
Route::get('/', [ForumController::class, 'index'])->name('home');
Route::get('/question/{id}', [ForumController::class, 'show'])->name('question.show');
Route::get('/user/{id}', [UserProfileController::class, 'show'])->name('user.profile');

// --- ADMIN ROUTES ---
Route::middleware(['auth'])->group(function () {
    // Dashboard & Core
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::delete('/admin/question/{id}/delete', [AdminController::class, 'deleteQuestion'])->name('admin.delete_question');
    Route::delete('/admin/report/{id}/dismiss', [AdminController::class, 'dismissReport'])->name('admin.dismiss_report');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/ban', [AdminController::class, 'toggleUserBan'])->name('admin.users.ban');
    Route::post('/admin/users/{id}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
    Route::post('/admin/users/{id}/promote', [AdminController::class, 'promoteToAdmin'])->name('admin.users.promote');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit_logs');

    // User Update & Password Reset
    Route::post('/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.reset_password');

    // Categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/category', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/admin/category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');

    // Courses & Strands Management
    Route::get('/admin/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::post('/admin/course', [AdminController::class, 'storeCourse'])->name('admin.course.store');
    Route::delete('/admin/course/{id}', [AdminController::class, 'deleteCourse'])->name('admin.course.delete');

    // Departments Management
    Route::get('/admin/departments', [AdminController::class, 'departments'])->name('admin.departments');
    Route::post('/admin/department', [AdminController::class, 'storeDepartment'])->name('admin.department.store');
    Route::delete('/admin/department/{id}', [AdminController::class, 'deleteDepartment'])->name('admin.department.delete');

    // Settings
    Route::prefix('admin/settings')->name('admin.settings.')->group(function() {
        Route::get('/general', [AdminController::class, 'generalSettings'])->name('general');
        Route::post('/general', [AdminController::class, 'updateGeneralSettings'])->name('general.update');
        Route::get('/email', [AdminController::class, 'emailSettings'])->name('email');
        Route::post('/email', [AdminController::class, 'updateEmailSettings'])->name('email.update');
    });
});

// --- AUTHENTICATED USER ROUTES ---
// REMOVED 'verified' middleware so the admin toggle can control access instead
Route::middleware(['auth'])->group(function () {
    
    // Profile Editing
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posting & Interacting
    Route::post('/post-question', [ForumController::class, 'storeQuestion'])->name('question.store');
    Route::post('/question/{id}/answer', [ForumController::class, 'storeAnswer'])->name('answer.store');
    Route::post('/question/{id}/report', [ForumController::class, 'reportQuestion'])->name('question.report');
    Route::post('/answer/{id}/rate', [ForumController::class, 'rateAnswer'])->name('answer.rate');
    Route::post('/answer/{id}/reply', [ForumController::class, 'storeReply'])->name('reply.store');
    Route::post('/answer/{id}/best', [ForumController::class, 'markAsBest'])->name('answer.best');

    // Editing & Deleting
    Route::delete('/question/{id}', [ForumController::class, 'destroyQuestion'])->name('question.destroy');
    Route::delete('/answer/{id}', [ForumController::class, 'destroyAnswer'])->name('answer.destroy');
    Route::delete('/reply/{id}', [\App\Http\Controllers\ForumController::class, 'destroyReply'])->name('reply.destroy');
    
    Route::get('/question/{id}/edit', [App\Http\Controllers\ForumController::class, 'editQuestion'])->name('question.edit');
    Route::put('/question/{id}', [App\Http\Controllers\ForumController::class, 'updateQuestion'])->name('question.update');
    Route::get('/answer/{id}/edit', [App\Http\Controllers\ForumController::class, 'editAnswer'])->name('answer.edit');
    Route::put('/answer/{id}', [App\Http\Controllers\ForumController::class, 'updateAnswer'])->name('answer.update');
    Route::get('/reply/{id}/edit', [App\Http\Controllers\ForumController::class, 'editReply'])->name('reply.edit');
    Route::put('/reply/{id}', [App\Http\Controllers\ForumController::class, 'updateReply'])->name('reply.update');

    // Profile & Notifications
    Route::post('/user/avatar', [App\Http\Controllers\UserProfileController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\ForumController::class, 'markNotification'])->name('notifications.read_one');
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

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