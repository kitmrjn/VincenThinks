<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'reason'];

    // This function tells Laravel that a Report belongs to a Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}