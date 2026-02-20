<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Department;
use App\Services\IdVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $courses = Course::all()->groupBy('type');
        $departments = Department::orderBy('name')->get(); 
        
        return view('auth.register', compact('courses', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'member_type' => ['required', 'string', 'in:student,teacher'],
            
            'id_document' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],

            'student_number' => ['nullable', 'required_if:member_type,student', 'string', 'max:20', 'unique:users', 'regex:/^AY\d{4}-\d{5}$/'],
            'course_id' => ['nullable', 'required_if:member_type,student', 'exists:courses,id'],

            'teacher_number' => ['nullable', 'required_if:member_type,teacher', 'string', 'max:20', 'unique:users', 'regex:/^AY\d{4}-\d{5}$/'],
            'department_id' => ['nullable', 'required_if:member_type,teacher', 'exists:departments,id'],

        ], [
            'name.regex' => 'The name can only contain letters, spaces, and dots.',
            'student_number.regex' => 'The student number must follow the format AY2023-00123.',
            'teacher_number.regex' => 'The teacher number must follow the format AY2023-00123.',
            'department_id.required_if' => 'Please select your specific Department or Faculty.',
            'department_id.exists' => 'The selected department is invalid.',
            'id_document.required' => 'Please upload a photo of your school ID or registration form.',
        ]);

        // 1. Temporary File Storage
        $filePath = $request->file('id_document')->store('temp_ids', 'local');
        $absolutePath = Storage::disk('local')->path($filePath);
        $idNumberToVerify = $request->member_type === 'student' ? $request->student_number : $request->teacher_number;

        // 2. Run AI Verification FIRST
        $verificationResult = IdVerificationService::verifyDocument(
            $absolutePath, 
            $request->name, 
            $idNumberToVerify
        );

        // 3. Delete file immediately to secure PII
        Storage::disk('local')->delete($filePath);

        // 4. THE HARD FAIL Check
        if (!$verificationResult['success']) {
            return back()->withInput()->withErrors([
                'id_document' => 'ID Verification Failed: ' . $verificationResult['message']
            ]);
        }

        // 5. Success! Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'member_type' => $request->member_type,
            'student_number' => $request->member_type === 'student' ? $request->student_number : null,
            'course_id' => $request->member_type === 'student' ? $request->course_id : null,
            'teacher_number' => $request->member_type === 'teacher' ? $request->teacher_number : null,
            'department_id' => $request->member_type === 'teacher' ? $request->department_id : null,
            'is_id_verified' => true, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}