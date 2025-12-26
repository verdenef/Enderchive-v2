<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Http\Requests\StorePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    /**
     * Display a listing of platforms.
     * GET /api/platforms
     */
    public function index(Request $request)
    {
        $query = Platform::withCount('games');
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $platforms = $query->orderBy('name')->paginate(20);
        
        return response()->json([
            'data' => $platforms->items(),
            'meta' => [
                'current_page' => $platforms->currentPage(),
                'last_page' => $platforms->lastPage(),
                'per_page' => $platforms->perPage(),
                'total' => $platforms->total(),
            ],
            'message' => 'Platforms retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created platform in storage.
     * POST /api/platforms
     */
    public function store(StorePlatformRequest $request)
    {
        $user = Auth::user();
        
        // Admin check - only admin can create platforms
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to create platforms'
            ], 403);
        }
        
        try {
            $platform = Platform::create($request->validated());
            
            return response()->json([
                'data' => $platform,
                'message' => 'Platform created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create platform',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified platform.
     * GET /api/platforms/{platform}
     */
    public function show(Platform $platform)
    {
        $platform->load(['games.genre', 'games.platforms', 'games.tags']);
        
        return response()->json([
            'data' => $platform,
            'message' => 'Platform retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified platform in storage.
     * PUT /api/platforms/{platform}
     */
    public function update(UpdatePlatformRequest $request, Platform $platform)
    {
        $user = Auth::user();
        
        // Admin check - only admin can update platforms
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to update platforms'
            ], 403);
        }
        
        $platform->update($request->validated());
        
        return response()->json([
            'data' => $platform,
            'message' => 'Platform updated successfully'
        ], 200);
    }

    /**
     * Remove the specified platform from storage.
     * DELETE /api/platforms/{platform}
     */
    public function destroy(Platform $platform)
    {
        $user = Auth::user();
        
        // Admin check - only admin can delete platforms
        if ($user->email !== 'admin@example.com') {
            return response()->json([
                'message' => 'Admin access required to delete platforms'
            ], 403);
        }
        
        // Check if platform has games
        if ($platform->games()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete platform with associated games'
            ], 400);
        }
        
        $platform->delete();
        
        return response()->json([
            'message' => 'Platform deleted successfully'
        ], 200);
    }
}