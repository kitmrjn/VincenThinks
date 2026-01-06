<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            
            // Student Rules
            'course_id' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'student'), 
                'exists:courses,id'
            ],
            'student_number' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'student'), 
                'string', 'max:20', 
                Rule::unique(User::class)->ignore($user->id),
                'regex:/^AY\d{4}-\d{5}$/'
            ],

            // Teacher Rules (MATCHED TO DATABASE)
            'department_id' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'teacher'), 
                'exists:departments,id' // Must exist in database
            ],
            'teacher_number' => [
                'nullable', 
                Rule::requiredIf($user->member_type === 'teacher'), 
                'string', 'max:20', 
                Rule::unique(User::class)->ignore($user->id),
                'regex:/^AY\d{4}-\d{5}$/' 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name can only contain letters, spaces, and dots.',
            'student_number.regex' => 'The student number must follow the format AYxxxx-xxxxx.',
            'teacher_number.regex' => 'The teacher number must follow the format AYxxxx-xxxxx.',
        ];
    }
}