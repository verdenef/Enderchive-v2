<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Game;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for a specific game.
     * GET /api/games/{game}/reviews
     */
    public function index(Request $request, Game $game)
    {
        $query = Review::with(['user'])
            ->where('game_id', $game->game_id);
        
        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }
        
        // Sort by newest first
        $query->orderBy('created_at', 'desc');
        
        $reviews = $query->paginate(20);
        
        return response()->json([
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'average_rating' => Review::where('game_id', $game->game_id)->avg('rating'),
            ],
            'message' => 'Reviews retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created review in storage.
     * POST /api/games/{game}/reviews
     */
    public function store(StoreReviewRequest $request, Game $game)
    {
        $user = Auth::user();
        
        // Check if user already reviewed this game
        $existingReview = Review::where('user_id', $user->user_id)
            ->where('game_id', $game->game_id)
            ->first();
            
        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this game'
            ], 409);
        }
        
        $review = Review::create([
            'user_id' => $user->user_id,
            'game_id' => $game->game_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);
        
        $review->load(['user', 'game']);
        
        return response()->json([
            'data' => $review,
            'message' => 'Review created successfully'
        ], 201);
    }

    /**
     * Display the specified review.
     * GET /api/reviews/{review}
     */
    public function show(Review $review)
    {
        $review->load(['user', 'game.genre', 'game.platforms']);
        
        return response()->json([
            'data' => $review,
            'message' => 'Review retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified review in storage.
     * PUT /api/reviews/{review}
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $user = Auth::user();
        
        // Ensure user can only update their own reviews
        if ($review->user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to review'
            ], 403);
        }
        
        $review->update($request->validated());
        $review->load(['user', 'game']);
        
        return response()->json([
            'data' => $review,
            'message' => 'Review updated successfully'
        ], 200);
    }

    /**
     * Remove the specified review from storage.
     * DELETE /api/reviews/{review}
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();
        
        // Ensure user can only delete their own reviews
        if ($review->user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to review'
            ], 403);
        }
        
        $review->delete();
        
        return response()->json([
            'message' => 'Review deleted successfully'
        ], 200);
    }
}