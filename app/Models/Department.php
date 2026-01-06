<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Question;

class Department extends Model
{
    protected $fillable = [
        'name',
        'acronym',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: Get all questions asked by users in this department.
     * Structure: Department -> has many Users -> has many Questions
     */
    public function questions()
    {
        return $this->hasManyThrough(Question::class, User::class);
    }
}