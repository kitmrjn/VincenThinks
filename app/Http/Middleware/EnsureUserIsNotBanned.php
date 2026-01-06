<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_banned) {
            // Force Logout
            Auth::guard('web')->logout();

            // Invalidate Session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect with Error Message
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ]);
        }

        return $next($request);
    }
}