<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Services\ContentFilter;
use Illuminate\Support\Facades\Log;

class Question extends Model {
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'title', 
        'content', 
        'category_id', 
        'image',
        'best_answer_id',
        'views',
        'status'
    ];

    protected static function booted()
    {
        // 1. Intercept Creation: Run Content Filter
        static::creating(function ($question) {
            $textToCheck = $question->title . "\n" . $question->content;
            
            if (ContentFilter::check($textToCheck)) {
                $question->status = 'pending_review';
            } else {
                $question->status = 'published';
            }
        });

        // 2. Global Scope: Filter non-published questions for non-admins
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