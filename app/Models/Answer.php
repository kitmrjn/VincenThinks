<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Services\ContentFilter;
use Illuminate\Support\Facades\Log;

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
        static::creating(function ($answer) {
            if (ContentFilter::check($answer->content)) {
                $answer->status = 'pending_review';
            } else {
                $answer->status = 'published';
            }
        });

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