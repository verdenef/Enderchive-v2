<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserGame;
use App\Models\Game;
use App\Http\Requests\StoreUserGameRequest;
use App\Http\Requests\UpdateUserGameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GameMilestone;
use App\Models\UserGameProgress;
use App\Models\UserGameOwnership;

class UserGameController extends Controller
{
    /**
     * Display a listing of the user's games.
     * GET /api/user/games
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = UserGame::with(['game.genre', 'game.platforms', 'game.tags'])
            ->where('user_id', $user->user_id);
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }
        
        // Search by game title
        if ($request->has('search')) {
            $query->whereHas('game', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }
        
        $userGames = $query->paginate(20);

        // compute percent_complete and ownerships summary per item
        $items = collect($userGames->items())->map(function ($ug) use ($user) {
            $requiredCount = GameMilestone::where('game_id', $ug->game_id)->where('is_optional', false)->count();
            
            // Only count achievements that are for required milestones
            $achievedCount = UserGameProgress::where('user_id', $user->user_id)
                ->where('game_id', $ug->game_id)
                ->whereHas('milestone', function($q) {
                    $q->where('is_optional', false);
                })
                ->count();
            
            $percent = $requiredCount > 0 ? (int) floor(($achievedCount / $requiredCount) * 100) : null;

            $ownerships = UserGameOwnership::with(['platform', 'store', 'edition'])
                ->where('user_id', $user->user_id)
                ->where('game_id', $ug->game_id)
                ->get();

            $ug->percent_complete = $percent;
            $ug->ownerships = $ownerships;
            return $ug;
        });
        
        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $userGames->currentPage(),
                'last_page' => $userGames->lastPage(),
                'per_page' => $userGames->perPage(),
                'total' => $userGames->total(),
            ],
            'message' => 'User games retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created user game in storage.
     * POST /api/user/games
     */
    public function store(StoreUserGameRequest $request)
    {
        $user = Auth::user();
        $status = $request->status ?? 'Wishlist';
        
        // Check if user already has this game
        $existingUserGame = UserGame::where('user_id', $user->user_id)
            ->where('game_id', $request->game_id)
            ->first();
            
        if ($existingUserGame) {
            // If adding to library (non-Wishlist status) and game is currently in wishlist, update it
            if ($status !== 'Wishlist' && $existingUserGame->status === 'Wishlist') {
                $existingUserGame->update([
                    'status' => $status,
                    'rating' => $request->rating ?? $existingUserGame->rating,
                    'playtime_hours' => $request->playtime_hours ?? $existingUserGame->playtime_hours,
                ]);
                $existingUserGame->load(['game.genre', 'game.platforms', 'game.tags']);
                
                return response()->json([
                    'data' => $existingUserGame,
                    'message' => 'Game moved from wishlist to library successfully'
                ], 200);
            }
            
            // If game already exists with same or different status, return conflict
            return response()->json([
                'message' => 'Game already exists in your library'
            ], 409);
        }
        
        // If adding to library (non-Wishlist status), check if it's in wishlist and update it
        // This handles the case where the game might have been added to wishlist separately
        if ($status !== 'Wishlist') {
            $wishlistGame = UserGame::where('user_id', $user->user_id)
                ->where('game_id', $request->game_id)
                ->where('status', 'Wishlist')
                ->first();
                
            if ($wishlistGame) {
                // Update the wishlist entry to the new status instead of creating a new one
                $wishlistGame->update([
                    'status' => $status,
                    'rating' => $request->rating ?? null,
                    'playtime_hours' => $request->playtime_hours ?? 0,
                ]);
                $wishlistGame->load(['game.genre', 'game.platforms', 'game.tags']);
                
                return response()->json([
                    'data' => $wishlistGame,
                    'message' => 'Game moved from wishlist to library successfully'
                ], 200);
            }
        }
        
        // Create new user game entry
        $userGame = UserGame::create([
            'user_id' => $user->user_id,
            'game_id' => $request->game_id,
            'status' => $status,
            'rating' => $request->rating ?? null,
            'playtime_hours' => $request->playtime_hours ?? 0,
        ]);
        
        $userGame->load(['game.genre', 'game.platforms', 'game.tags']);
        
        return response()->json([
            'data' => $userGame,
            'message' => 'Game added to library successfully'
        ], 201);
    }

    /**
     * Display the specified user game.
     * GET /api/user/games/{userGame}
     */
    public function show($userGame)
    {
        $user = Auth::user();
        
        // Find the user game by ID
        $userGame = UserGame::find($userGame);
        
        if (!$userGame) {
            return response()->json([
                'message' => 'User game not found'
            ], 404);
        }
        
        // Ensure user can only view their own games
        if ($userGame->user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to user game'
            ], 403);
        }
        
        try {
            $userGame->load(['game.genre', 'game.platforms', 'game.tags', 'game.reviews']);
            
            return response()->json([
                'data' => $userGame,
                'message' => 'User game retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user game in storage.
     * PUT /api/user/games/{userGame}
     */
    public function update(UpdateUserGameRequest $request, $userGame)
    {
        $user = Auth::user();
        
        $userGame = UserGame::find($userGame);
        
        if (!$userGame) {
            return response()->json([
                'message' => 'User game not found'
            ], 404);
        }
        
        // Ensure user can only update their own games
        if ($userGame->user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to user game'
            ], 403);
        }
        
        try {
            $userGame->update($request->validated());
            $userGame->load(['game.genre', 'game.platforms', 'game.tags']);
            
            return response()->json([
                'data' => $userGame,
                'message' => 'User game updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set status/rating/dates for a game in the user's library.
     * POST /api/user/games/{game}/status
     */
    public function setStatus(Request $request, Game $game)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'status' => 'required|in:Wishlist,Playing,Completed,Abandoned',
            'rating' => 'nullable|integer|min:1|max:10',
        ]);

        $userGame = UserGame::firstOrCreate(
            [
                'user_id' => $user->user_id,
                'game_id' => $game->game_id,
            ],
            [
                'status' => 'Wishlist',
            ]
        );

        $userGame->update($validated);

        return response()->json(['data' => $userGame]);
    }

    /**
     * Remove the specified user game from storage.
     * DELETE /api/user/games/{userGame}
     */
    public function destroy($userGame)
    {
        $user = Auth::user();
        
        $userGame = UserGame::find($userGame);
        
        if (!$userGame) {
            return response()->json([
                'message' => 'User game not found'
            ], 404);
        }
        
        // Ensure user can only delete their own games
        if ($userGame->user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to user game'
            ], 403);
        }
        
        try {
            $userGame->delete();
            
            return response()->json([
                'message' => 'Game removed from library successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove game from library',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}