<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Fetch departments from database
        $departments = Department::orderBy('name')->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'departments' => $departments, 
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(\App\Http\Requests\ProfileUpdateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Check if the user actually changed their email address
        $emailChanged = $request->user()->isDirty('email');

        if ($emailChanged) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // [NEW] If the email was changed, generate a new OTP and redirect to the OTP page
        if ($emailChanged) {
            $request->user()->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
        }

        return \Illuminate\Support\Facades\Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}