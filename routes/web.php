<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserProfileController;
use App\Notifications\NewActivity;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ImageUploadController;

Route::get('/user/{id}', [UserProfileController::class, 'show'])->name('user.profile');

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::delete('/admin/question/{id}/delete', [AdminController::class, 'deleteQuestion'])->name('admin.delete_question');
    Route::delete('/admin/report/{id}/dismiss', [AdminController::class, 'dismissReport'])->name('admin.dismiss_report');
    Route::post('/admin/category', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/admin/category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/category', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::delete('/admin/category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');
    Route::delete('/admin/question/{id}/delete', [AdminController::class, 'deleteQuestion'])->name('admin.delete_question');
    Route::delete('/admin/report/{id}/dismiss', [AdminController::class, 'dismissReport'])->name('admin.dismiss_report');
});

// Public pages
Route::get('/', [ForumController::class, 'index'])->name('home');
Route::get('/question/{id}', [ForumController::class, 'show'])->name('question.show');

// Pages that require Login (auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/post-question', [ForumController::class, 'storeQuestion'])->name('question.store');
    Route::post('/question/{id}/answer', [ForumController::class, 'storeAnswer'])->name('answer.store');
    Route::post('/question/{id}/report', [ForumController::class, 'reportQuestion'])->name('question.report');
    Route::post('/answer/{id}/rate', [ForumController::class, 'rateAnswer'])->name('answer.rate');
    Route::delete('/question/{id}', [ForumController::class, 'destroyQuestion'])->name('question.destroy');
    Route::delete('/answer/{id}', [ForumController::class, 'destroyAnswer'])->name('answer.destroy');
    Route::post('/answer/{id}/reply', [ForumController::class, 'storeReply'])->name('reply.store');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\ForumController::class, 'markNotification'])->name('notifications.read_one');
    Route::delete('/reply/{id}', [\App\Http\Controllers\ForumController::class, 'destroyReply'])->name('reply.destroy');
    // EDIT ROUTES
    Route::get('/question/{id}/edit', [App\Http\Controllers\ForumController::class, 'editQuestion'])->name('question.edit');
    Route::put('/question/{id}', [App\Http\Controllers\ForumController::class, 'updateQuestion'])->name('question.update');
    Route::get('/answer/{id}/edit', [App\Http\Controllers\ForumController::class, 'editAnswer'])->name('answer.edit');
    Route::put('/answer/{id}', [App\Http\Controllers\ForumController::class, 'updateAnswer'])->name('answer.update');
    Route::get('/reply/{id}/edit', [App\Http\Controllers\ForumController::class, 'editReply'])->name('reply.edit');
    Route::put('/reply/{id}', [App\Http\Controllers\ForumController::class, 'updateReply'])->name('reply.update');
    // User Profile Routes
    Route::get('/user/{id}', [App\Http\Controllers\UserProfileController::class, 'show'])->name('user.profile');
    Route::post('/user/avatar', [App\Http\Controllers\UserProfileController::class, 'updateAvatar'])->name('user.avatar.update');
    // Best Answer Route
    Route::post('/answer/{id}/best', [App\Http\Controllers\ForumController::class, 'markAsBest'])->name('answer.best');
    // The endpoint for the editor to upload images
    // SAFEST DIRECT UPLOAD ROUTE
    Route::post('/upload-editor-image', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'image' => 'required|image|max:5120', // 5MB Max
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads', 'public');
                return response()->json(['url' => asset('storage/' . $path)]);
            }

            return response()->json(['message' => 'No image found'], 400);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    })->name('editor.image.upload')->middleware('auth');
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
            })->name('notifications.read');
        });

        require __DIR__.'/auth.php';

    Route::get('/debug-notification', function () {
        $user = Auth::user();
        
        // Force save a notification to the database
        $user->notify(new NewActivity(
            'System Test Notification',
            '/',
            'info'
        ));

        return "Done! Check your database table 'notifications' now.";
    })->middleware('auth');
