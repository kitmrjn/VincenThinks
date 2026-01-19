<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; // [FIX] Added Import
use App\Jobs\CheckContentSafety;

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

        // 3. UPDATING: If content changes, set back to pending
        static::updating(function ($answer) {
            if ($answer->isDirty('content')) {
                $answer->status = 'pending_review';
            }
        });

        // Global Scope: Hide pending answers unless Admin OR Owner
        static::addGlobalScope('published', function (Builder $builder) {
            if (!request()->is('admin*')) {
                // [FIX] Allow Published OR Own Content
                $builder->where(function($query) {
                    $query->where('status', 'published');
                    
                    if (Auth::check()) {
                        $query->orWhere('user_id', Auth::id());
                    }
                });
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function ratings() { return $this->hasMany(Rating::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function replies() { return $this->hasMany(Reply::class); }
}