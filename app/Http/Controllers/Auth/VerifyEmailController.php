<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified using OTP.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6']
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false) . '?verified=1');
        }

        // Check if OTP matches and is not expired
        if ($user->email_verification_code !== $request->otp || now()->greaterThan($user->email_verification_code_expires_at)) {
            return back()->withErrors(['otp' => 'The verification code is invalid or has expired.']);
        }

        // OTP is correct! Verify email and clear the code
        if ($user->markEmailAsVerified()) {
            $user->email_verification_code = null;
            $user->email_verification_code_expires_at = null;
            $user->save();
            event(new Verified($user));
        }

        return redirect()->intended(route('home', absolute: false) . '?verified=1');
    }
}