<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class EnsureCustomVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the global setting 'verification_required' is enabled (value '1')
        $verificationRequired = Setting::where('key', 'verification_required')->value('value') === '1';

        // 2. If required, check if the user is verified
        if ($verificationRequired && Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email verification required.'], 403);
            }

            return redirect()->back()->with('error', 'Action blocked: You must verify your email address to post.');
        }

        return $next($request);
    }
}