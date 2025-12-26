<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $games = Game::with(['genre', 'platforms'])
                ->limit(8)
                ->get()
                ->map(function ($game) {
                    return [
                        'game_id' => $game->game_id,
                        'title' => $game->title,
                        'description' => $game->description,
                        'cover_image' => $game->cover_image,
                        'release_date' => $game->release_date,
                        'developer' => $game->developer,
                        'publisher' => $game->publisher,
                        'genre' => $game->genre ? [
                            'genre_id' => $game->genre->genre_id,
                            'name' => $game->genre->name,
                        ] : null,
                        'platforms' => $game->platforms->map(function ($platform) {
                            return [
                                'platform_id' => $platform->platform_id,
                                'name' => $platform->name,
                            ];
                        }),
                    ];
                });
            
            $userGames = [];
            if (Auth::check()) {
                $userGames = Auth::user()
                    ->userGames()
                    ->with(['game'])
                    ->get()
                    ->map(function ($userGame) {
                        return [
                            'user_game_id' => $userGame->user_game_id,
                            'game_id' => $userGame->game_id,
                            'status' => $userGame->status,
                            'playtime_hours' => $userGame->playtime_hours,
                            'game' => $userGame->game ? [
                                'game_id' => $userGame->game->game_id,
                                'title' => $userGame->game->title,
                            ] : null,
                        ];
                    });
            }
            
            return Inertia::render('Home', [
                'games' => $games,
                'userGames' => $userGames,
            ]);
        } catch (\Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return Inertia::render('Home', [
                'games' => [],
                'userGames' => [],
                'error' => 'Failed to load games',
            ]);
        }
    }
}

