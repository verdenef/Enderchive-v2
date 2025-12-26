<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        // Log for debugging
        \Log::info('Admin route accessed', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'is_authenticated' => Auth::check(),
        ]);

        if (!Auth::check()) {
            \Log::warning('Admin access denied: Not authenticated');
            return redirect('/login');
        }

        $user = Auth::user();
        
        if ($user->email !== 'admin@example.com') {
            \Log::warning('Admin access denied: Wrong email', [
                'expected' => 'admin@example.com',
                'actual' => $user->email,
            ]);
            return redirect('/home')->with('error', 'Admin access required');
        }

        \Log::info('Admin access granted', ['email' => $user->email]);
        
        return Inertia::render('Admin');
    }
}

