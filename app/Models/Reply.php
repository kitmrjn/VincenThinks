<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Needed for Global Scope
use App\Services\ContentFilter;           // Needed for AI

class Reply extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'answer_id', 'content', 'parent_id', 'status'];

    protected static function booted()
    {
        // 1. Intercept Creation: Run Content Filter
        static::creating(function ($reply) {
            if (ContentFilter::check($reply->content)) {
                $reply->status = 'pending_review';
            } else {
                $reply->status = 'published';
            }
        });

        // 2. Global Scope: Only show published replies (unless Admin)
        static::addGlobalScope('published', function (Builder $builder) {
            if (!request()->is('admin*')) { 
                $builder->where('status', 'published');
            }
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function answer() {
        return $this->belongsTo(Answer::class);
    }

    public function parent() {
        return $this->belongsTo(Reply::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Reply::class, 'parent_id');
    }
}