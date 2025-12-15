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
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $courses = Course::all()->groupBy('type');
        return view('auth.register', compact('courses'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // UPDATED: Name now strictly allows only letters, spaces, and dots.
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'member_type' => ['required', 'string', 'in:student,teacher'],
            
            // Student Number (Keep the regex we added previously)
            'student_number' => [
                'nullable', 
                'required_if:member_type,student', 
                'string', 
                'max:20', 
                'unique:users',
                'regex:/^AY\d{4}-\d{5}$/' 
            ],
            'course_id' => ['nullable', 'required_if:member_type,student', 'exists:courses,id'],
        ], [
            // Custom Error Messages
            'name.regex' => 'The name can only contain letters, spaces, and dots (e.g., John A. Doe).',
            'student_number.regex' => 'The student number must follow the format AYYYYY-XXXXX (e.g., AY2023-01234).',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'member_type' => $request->member_type,
            'student_number' => $request->member_type === 'student' ? $request->student_number : null,
            'course_id' => $request->member_type === 'student' ? $request->course_id : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}