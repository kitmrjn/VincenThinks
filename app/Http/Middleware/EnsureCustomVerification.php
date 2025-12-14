<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting; // Import Setting model

class EnsureCustomVerification
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. If user is not logged in, standard auth handles it
        if (!$user) {
            return $next($request);
        }

        // 2. ADMIN BYPASS: Always allow Admins (Fixes your issue)
        if ($user->is_admin) {
            return $next($request);
        }

        // 3. SETTINGS CHECK: Check if verification is disabled in Admin Panel
        // If the 'verification_required' setting is '0' or missing, we can optionally skip check
        // For safety, let's assume if it's strictly '0', we skip.
        $isRequired = Setting::where('key', 'verification_required')->value('value');
        if ($isRequired === '0') {
            return $next($request);
        }

        // 4. STANDARD CHECK: If not admin and required, check verification
        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        // 5. Fail: Redirect to verification notice
        return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route('verification.notice');
    }
}