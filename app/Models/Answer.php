<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'content'];

    // Relationship to User (Author of the answer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to Ratings
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // --- ADD THIS MISSING FUNCTION ---
    // Relationship to Question (The question this answer belongs to)
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}