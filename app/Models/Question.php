<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\CheckContentSafety; // [Import Job]

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
            CheckContentSafety::dispatch($question);
        });

        // 3. UPDATING (Edit Logic: Check Title or Content)
        static::updating(function ($question) {
            if ($question->isDirty('title') || $question->isDirty('content')) {
                $question->status = 'pending_review';
            }
        });

        // 4. UPDATED (Edit Logic)
        static::updated(function ($question) {
            if ($question->wasChanged('title') || $question->wasChanged('content')) {
                CheckContentSafety::dispatch($question);
            }
        });

        // Global Scope
        static::addGlobalScope('published', function (Builder $builder) {
            if (!request()->is('admin*')) { 
                $builder->where('status', 'published');
            }
        });
    }

    public function answers() { return $this->hasMany(Answer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function images() { return $this->hasMany(QuestionImage::class); }
}