<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     * GET /api/tags
     */
    public function index(Request $request)
    {
        $query = Tag::withCount('games');
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $tags = $query->orderBy('name')->paginate(20);
        
        return response()->json([
            'data' => $tags->items(),
            'meta' => [
                'current_page' => $tags->currentPage(),
                'last_page' => $tags->lastPage(),
                'per_page' => $tags->perPage(),
                'total' => $tags->total(),
            ],
            'message' => 'Tags retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created tag in storage.
     * POST /api/tags
     */
    public function store(StoreTagRequest $request)
    {
        $user = Auth::user();
        
        // Admin check - only admin can create tags
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to create tags'
            ], 403);
        }
        
        try {
            $tag = Tag::create($request->validated());
            
            return response()->json([
                'data' => $tag,
                'message' => 'Tag created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tag.
     * GET /api/tags/{tag}
     */
    public function show(Tag $tag)
    {
        $tag->load(['games.genre', 'games.platforms']);
        
        return response()->json([
            'data' => $tag,
            'message' => 'Tag retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified tag in storage.
     * PUT /api/tags/{tag}
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $user = Auth::user();
        
        // Admin check - only admin can update tags
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to update tags'
            ], 403);
        }
        
        $tag->update($request->validated());
        
        return response()->json([
            'data' => $tag,
            'message' => 'Tag updated successfully'
        ], 200);
    }

    /**
     * Remove the specified tag from storage.
     * DELETE /api/tags/{tag}
     */
    public function destroy(Tag $tag)
    {
        $user = Auth::user();
        
        // Admin check - only admin can delete tags
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to delete tags'
            ], 403);
        }
        
        // Check if tag has games
        if ($tag->games()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete tag with associated games'
            ], 400);
        }
        
        $tag->delete();
        
        return response()->json([
            'message' => 'Tag deleted successfully'
        ], 200);
    }
}