<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth; // [FIX] Added Import
use App\Jobs\CheckContentSafety;

class Reply extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'answer_id', 'content', 'parent_id', 'status'];

    protected static function booted()
    {
        // 1. CREATING
        static::creating(function ($reply) {
            if (empty($reply->status)) {
                $reply->status = 'pending_review';
            }
        });

        // 3. UPDATING (Edit Logic)
        static::updating(function ($reply) {
            if ($reply->isDirty('content')) {
                $reply->status = 'pending_review';
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

    public function user() { return $this->belongsTo(User::class); }
    public function answer() { return $this->belongsTo(Answer::class); }
    public function parent() { return $this->belongsTo(Reply::class, 'parent_id'); }
    public function children() { return $this->hasMany(Reply::class, 'parent_id'); }
}