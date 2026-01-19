<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; // [FIX] Added Import
use App\Jobs\CheckContentSafety;

class Question extends Model {
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'title', 'content', 'category_id', 
        'image', 'best_answer_id', 'views', 'status'
    ];

    protected static function booted()
    {
        // 1. CREATING
        static::creating(function ($question) {
            if (empty($question->status)) {
                $question->status = 'pending_review';
            }
        });

        // 2. CREATED
        static::created(function ($question) {
             // Dispatch safety check
             // CheckContentSafety::dispatchSync($question);
        });

        // 3. UPDATING
        static::updating(function ($question) {
            if ($question->isDirty('title') || $question->isDirty('content')) {
                $question->status = 'pending_review';
            }
        });

        // 4. UPDATED
        static::updated(function ($question) {
            if ($question->wasChanged('title') || $question->wasChanged('content')) {
                CheckContentSafety::dispatch($question);
            }
        });

        // Global Scope
        static::addGlobalScope('published', function (Builder $builder) {
            // [FIX] Allow Admins to see EVERYTHING, regardless of the URL
            if (Auth::check() && Auth::user()->is_admin) {
                return;
            }

            // Everyone else (Guest/Student) -> Apply filters
            $builder->where(function($query) {
                $query->where('status', 'published');
                
                if (Auth::check()) {
                    $query->orWhere('user_id', Auth::id());
                }
            });
        });
    }

    public function answers() { return $this->hasMany(Answer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function images() { return $this->hasMany(QuestionImage::class); }
}