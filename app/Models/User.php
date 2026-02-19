<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Question;
use App\Models\Answer;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'student_number',
        'teacher_number',
        'course_id',
        'member_type', 
        'department_id', 
        'is_admin',
        'avatar', 
        'is_banned',
        'is_id_verified' // [NEW] Added for AI verification tracking
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
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'is_id_verified' => 'boolean', // [NEW]
        ];
    }

    public function questions() { return $this->hasMany(Question::class); }
    public function answers() { return $this->hasMany(Answer::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function departmentInfo() { return $this->belongsTo(Department::class, 'department_id'); }
    
    public function sendEmailVerificationNotification() {
        $this->notify(new CustomVerifyEmail);
    }
}