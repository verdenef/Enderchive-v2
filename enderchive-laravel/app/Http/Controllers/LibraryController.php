<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userGames = $user->userGames()->with(['game.genre', 'game.platforms'])->get();
        
        return Inertia::render('Library', [
            'userGames' => $userGames,
        ]);
    }
}

