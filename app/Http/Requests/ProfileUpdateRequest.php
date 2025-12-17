<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the current user to check their role
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            
            // Allow Students to update their Course & Number
            'course_id' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'student'), 
                'exists:courses,id'
            ],
            'student_number' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'student'), 
                'string', 
                'max:20', 
                Rule::unique(User::class)->ignore($user->id),
                'regex:/^AY\d{4}-\d{5}$/'
            ],

            // Allow Teachers to update their Department & Number
            'department' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'teacher'), 
                'string', 
                'max:50'
            ],
            'teacher_number' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'teacher'), 
                'string', 
                'max:20', 
                Rule::unique(User::class)->ignore($user->id),
                'regex:/^AY\d{4}-\d{5}$/' // Matches student format as requested
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name can only contain letters, spaces, and dots.',
            'student_number.regex' => 'The student number must follow the format AYYYYY-XXXXX.',
            'teacher_number.regex' => 'The teacher number must follow the format AYYYYY-XXXXX.',
        ];
    }
}