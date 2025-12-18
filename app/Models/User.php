<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; 
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar', 
        'member_type', 
        'student_number',
        'course_id',
        'department_id', 
        'teacher_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function questions(){ return $this->hasMany(Question::class);}
    public function answers(){ return $this->hasMany(Answer::class);}
    public function course() { return $this->belongsTo(Course::class); }

    // --- FIXED: Renamed to departmentInfo to avoid conflict with 'department' column ---
    public function departmentInfo() { return $this->belongsTo(Department::class, 'department_id'); }
}