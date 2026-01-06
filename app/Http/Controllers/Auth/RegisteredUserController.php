<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Department; // Added Department Model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $courses = Course::all()->groupBy('type');
        // Fetch all departments to populate the dropdown in register.blade.php
        $departments = Department::orderBy('name')->get(); 
        
        return view('auth.register', compact('courses', 'departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

            // Teacher Validation
            'teacher_number' => [
                'nullable', 
                'required_if:member_type,teacher', 
                'string', 
                'max:20', 
                'unique:users',
                'regex:/^AY\d{4}-\d{5}$/' 
            ],
            // Use department_id and check if it exists in the departments table
            'department_id' => ['nullable', 'required_if:member_type,teacher', 'exists:departments,id'],

        ], [
            'name.regex' => 'The name can only contain letters, spaces, and dots.',
            'student_number.regex' => 'The student number must follow the format AY2023-00123.',
            'teacher_number.regex' => 'The teacher number must follow the format AY2023-00123.',
            'department_id.required_if' => 'Please select your specific Department or Faculty.',
            'department_id.exists' => 'The selected department is invalid.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'member_type' => $request->member_type,
            
            // Student Data
            'student_number' => $request->member_type === 'student' ? $request->student_number : null,
            'course_id' => $request->member_type === 'student' ? $request->course_id : null,
            
            // Teacher Data (Updated to use department_id)
            'teacher_number' => $request->member_type === 'teacher' ? $request->teacher_number : null,
            'department_id' => $request->member_type === 'teacher' ? $request->department_id : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}