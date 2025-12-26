<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that allows API routes to use session-based authentication
 * when requests come from the same origin (for Inertia.js apps)
 */
class AuthenticateSessionApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start session if not already started
        if (!$request->hasSession()) {
            $request->setLaravelSession(
                app('session.store')
            );
        }

        // Ensure session is started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Check if user is authenticated via web guard (session-based)
        if (!Auth::guard('web')->check()) {
            \Log::info('AuthenticateSessionApi: User not authenticated', [
                'has_session' => $request->hasSession(),
                'session_id' => $request->session()?->getId(),
                'session_data' => $request->session()->all(),
            ]);
            
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return $next($request);
    }
}

