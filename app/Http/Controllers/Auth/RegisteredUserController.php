<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $courses = Course::all()->groupBy('type');
        return view('auth.register', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'member_type' => ['required', 'string', 'in:student,teacher'],
            
            // Student Validation
            'student_number' => [
                'nullable', 
                'required_if:member_type,student', 
                'string', 
                'max:20', 
                'unique:users',
                'regex:/^AY\d{4}-\d{5}$/' 
            ],
            'course_id' => ['nullable', 'required_if:member_type,student', 'exists:courses,id'],

            // --- UPDATED: Teacher Validation (Same AY format) ---
            'teacher_number' => [
                'nullable', 
                'required_if:member_type,teacher', 
                'string', 
                'max:20', 
                'unique:users',
                // Regex matches Student format: AY + Year + 5 Digits
                'regex:/^AY\d{4}-\d{5}$/' 
            ],
            'department' => ['nullable', 'required_if:member_type,teacher', 'string', 'max:50'],

        ], [
            'name.regex' => 'The name can only contain letters, spaces, and dots.',
            'student_number.regex' => 'The student number must follow the format AYYYYY-XXXXX.',
            
            // Updated Error Message
            'teacher_number.regex' => 'The teacher number must follow the format AYYYYY-XXXXX (e.g., AY2023-00123).',
            
            'department.required_if' => 'Please select your specific Department or Faculty.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'member_type' => $request->member_type,
            
            // Student Data
            'student_number' => $request->member_type === 'student' ? $request->student_number : null,
            'course_id' => $request->member_type === 'student' ? $request->course_id : null,
            
            // Teacher Data
            'teacher_number' => $request->member_type === 'teacher' ? $request->teacher_number : null,
            'department' => $request->member_type === 'teacher' ? $request->department : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}