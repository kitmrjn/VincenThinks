<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'reason',
        'details' // Assuming 'details' might exist based on migrations, if not, fillable protects against mass assignment errors anyway.
    ];

    /**
     * Relationship: The user who submitted the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The question that was reported.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}