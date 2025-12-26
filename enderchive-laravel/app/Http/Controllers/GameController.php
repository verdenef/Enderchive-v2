<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function show($id)
    {
        $game = Game::with(['genre', 'platforms', 'tags', 'reviews.user', 'milestones'])
            ->findOrFail($id);
        
        $user = Auth::user();
        $userGame = null;
        
        if ($user) {
            $userGame = $user->userGames()
                ->where('game_id', $id)
                ->with(['game', 'progress.milestone', 'stats'])
                ->first();
        }
        
        return Inertia::render('Games/Show', [
            'game' => $game,
            'userGame' => $userGame,
            'reviews' => $game->reviews,
            'milestones' => $game->milestones,
        ]);
    }
}

