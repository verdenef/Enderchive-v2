<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenreController extends Controller
{
    /**
     * Display a listing of genres.
     * GET /api/genres
     */
    public function index(Request $request)
    {
        $query = Genre::withCount('games');
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $genres = $query->orderBy('name')->paginate(20);
        
        return response()->json([
            'data' => $genres->items(),
            'meta' => [
                'current_page' => $genres->currentPage(),
                'last_page' => $genres->lastPage(),
                'per_page' => $genres->perPage(),
                'total' => $genres->total(),
            ],
            'message' => 'Genres retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created genre in storage.
     * POST /api/genres
     */
    public function store(StoreGenreRequest $request)
    {
        $user = Auth::user();
        
        // Admin check - only admin can create genres
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to create genres'
            ], 403);
        }
        
        try {
            $genre = Genre::create($request->validated());
            
            return response()->json([
                'data' => $genre,
                'message' => 'Genre created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create genre',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified genre.
     * GET /api/genres/{genre}
     */
    public function show(Genre $genre)
    {
        $genre->load(['games.platforms', 'games.tags']);
        
        return response()->json([
            'data' => $genre,
            'message' => 'Genre retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified genre in storage.
     * PUT /api/genres/{genre}
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $user = Auth::user();
        
        // Admin check - only admin can update genres
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to update genres'
            ], 403);
        }
        
        $genre->update($request->validated());
        
        return response()->json([
            'data' => $genre,
            'message' => 'Genre updated successfully'
        ], 200);
    }

    /**
     * Remove the specified genre from storage.
     * DELETE /api/genres/{genre}
     */
    public function destroy(Genre $genre)
    {
        $user = Auth::user();
        
        // Admin check - only admin can delete genres
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to delete genres'
            ], 403);
        }
        
        // Check if genre has games
        if ($genre->games()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete genre with associated games'
            ], 400);
        }
        
        $genre->delete();
        
        return response()->json([
            'message' => 'Genre deleted successfully'
        ], 200);
    }
}