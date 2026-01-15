<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\CheckContentSafety; // [Import Job]

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'question_id', 
        'content', 
        'status'
    ];

    protected static function booted()
    {
        // 1. CREATING: Set default status
        static::creating(function ($answer) {
            if (empty($answer->status)) {
                $answer->status = 'pending_review';
            }
        });

        // 2. CREATED: Dispatch Job
        static::created(function ($answer) {
            // [FIX] Use dispatchSync to wait for the AI check
            CheckContentSafety::dispatchSync($answer);
        });

        // 3. UPDATING: If content changes, set back to pending
        static::updating(function ($answer) {
            if ($answer->isDirty('content')) {
                $answer->status = 'pending_review';
            }
        });

        // 4. UPDATED: Dispatch Job if content changed
        static::updated(function ($answer) {
            if ($answer->wasChanged('content')) {
                // [FIX] Sync here too for edits
                CheckContentSafety::dispatchSync($answer);
            }
        });

        // Global Scope: Hide pending answers unless Admin
        static::addGlobalScope('published', function (Builder $builder) {
            if (!request()->is('admin*')) {
                $builder->where('status', 'published');
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function ratings() { return $this->hasMany(Rating::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function replies() { return $this->hasMany(Reply::class); }
}