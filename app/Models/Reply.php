<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 
use App\Jobs\CheckContentSafety; // [Import Job]

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

        // 2. CREATED
        static::created(function ($reply) {
            CheckContentSafety::dispatch($reply);
        });

        // 3. UPDATING (Edit Logic)
        static::updating(function ($reply) {
            if ($reply->isDirty('content')) {
                $reply->status = 'pending_review';
            }
        });

        // 4. UPDATED (Edit Logic)
        static::updated(function ($reply) {
            if ($reply->wasChanged('content')) {
                CheckContentSafety::dispatch($reply);
            }
        });

        // Global Scope
        static::addGlobalScope('published', function (Builder $builder) {
            if (!request()->is('admin*')) { 
                $builder->where('status', 'published');
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function answer() { return $this->belongsTo(Answer::class); }
    public function parent() { return $this->belongsTo(Reply::class, 'parent_id'); }
    public function children() { return $this->hasMany(Reply::class, 'parent_id'); }
}