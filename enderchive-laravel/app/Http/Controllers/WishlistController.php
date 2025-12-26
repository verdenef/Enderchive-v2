<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $wishlistGames = $user->userGames()
            ->where('status', 'Wishlist')
            ->with(['game.genre', 'game.platforms'])
            ->get();
        
        return Inertia::render('Wishlist', [
            'wishlistGames' => $wishlistGames,
        ]);
    }
}

