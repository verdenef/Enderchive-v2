<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/games
     */
    public function index(Request $request)
    {
        // Get query parameters for filtering
        $query = Game::with(['genre', 'platforms', 'tags', 'reviews']);
        
        // Filter by genre
        if ($request->has('genre_id')) {
            $query->where('genre_id', $request->genre_id);
        }
        
        // Filter by platform
        if ($request->has('platform_id')) {
            $query->whereHas('platforms', function($q) use ($request) {
                $q->where('platform_id', $request->platform_id);
            });
        }
        
        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Pagination
        $games = $query->paginate(15);
        
        return response()->json([
            'data' => $games->items(),
            'meta' => [
                'current_page' => $games->currentPage(),
                'last_page' => $games->lastPage(),
                'per_page' => $games->perPage(),
                'total' => $games->total(),
            ],
            'message' => 'Games retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/games
     */
    public function store(StoreGameRequest $request)
    {
        $user = Auth::user();
        
        // Admin check - only admin can create games
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to create games'
            ], 403);
        }
        
        try {
            $game = Game::create($request->validated());
            
            // Attach platforms if provided
            if ($request->has('platform_ids')) {
                $game->platforms()->attach($request->platform_ids);
            }
            
            // Attach tags if provided
            if ($request->has('tag_ids')) {
                $game->tags()->attach($request->tag_ids);
            }
            
            $game->load(['genre', 'platforms', 'tags']);
            
            return response()->json([
                'data' => $game,
                'message' => 'Game created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/games/{game}
     */
    public function show($game)
    {
        $gameModel = Game::where('game_id', $game)
            ->with(['genre', 'platforms', 'tags', 'reviews.user'])
            ->firstOrFail();
        
        return response()->json([
            'data' => $gameModel,
            'message' => 'Game retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/games/{game}
     */
    public function update(UpdateGameRequest $request, $game)
    {
        $user = Auth::user();
        
        // Admin check - only admin can update games
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to update games'
            ], 403);
        }
        
        $gameModel = Game::where('game_id', $game)->firstOrFail();
        $gameModel->update($request->validated());
        
        // Update platforms if provided
        if ($request->has('platform_ids')) {
            $gameModel->platforms()->sync($request->platform_ids);
        }
        
        // Update tags if provided
        if ($request->has('tag_ids')) {
            $gameModel->tags()->sync($request->tag_ids);
        }
        
        $gameModel->load(['genre', 'platforms', 'tags']);
        
        return response()->json([
            'data' => $gameModel,
            'message' => 'Game updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/games/{game}
     */
    public function destroy($game)
    {
        $user = Auth::user();
        
        // Admin check - only admin can delete games
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to delete games'
            ], 403);
        }
        
        $gameModel = Game::where('game_id', $game)->firstOrFail();
        $gameModel->delete();
        
        return response()->json([
            'message' => 'Game deleted successfully'
        ], 200);
    }
}