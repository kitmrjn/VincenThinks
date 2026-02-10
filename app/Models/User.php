<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Question;
use App\Models\Answer;
use App\Notifications\CustomVerifyEmail; // [NEW] Import your custom notification

class User extends Authenticatable implements MustVerifyEmail // [UPDATED] Added "implements MustVerifyEmail"
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_number',
        'teacher_number',
        'course_id',
        'member_type', // 'student' or 'teacher'
        'department_id', // Foreign key for department
        'is_admin',
        'avatar', // path to avatar image
        'is_banned'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Relationship: A user has many questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relationship: A user has many answers
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Relationship: A user belongs to a course (if student)
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relationship: A user belongs to a department
     */
    public function departmentInfo()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * [NEW] Send the custom email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}