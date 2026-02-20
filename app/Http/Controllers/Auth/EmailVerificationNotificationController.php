<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification (Resend OTP).
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false));
        }

        // Generates the new 6-digit OTP and emails it
        $request->user()->sendEmailVerificationNotification();

        // [UPDATED] Instead of refreshing the feed/profile, send them directly to the OTP input page!
        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }
}