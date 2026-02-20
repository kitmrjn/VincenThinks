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
        'is_id_verified',
        'email_verification_code',
        'email_verification_code_expires_at',
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
            'is_id_verified' => 'boolean',
            'email_verification_code_expires_at' => 'datetime',
        ];
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function departmentInfo()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Generate a 6-digit OTP for email verification.
     */
    public function generateOtp()
    {
        $this->email_verification_code = sprintf("%06d", mt_rand(100000, 999999));
        $this->email_verification_code_expires_at = now()->addMinutes(10);
        $this->save();
    }
}