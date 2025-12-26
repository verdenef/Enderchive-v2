<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Get suggested users (random users excluding current user and friends)
     * GET /api/users/suggested
     */
    public function suggested(Request $request)
    {
        $user = Auth::user();
        
        // Get current user's friend IDs
        $friendIds = Friend::where(function($q) use ($user) {
            $q->where('user_id', $user->user_id)
              ->orWhere('friend_user_id', $user->user_id);
        })
        ->where('status', 'Accepted')
        ->get()
        ->map(function($friend) use ($user) {
            return $friend->user_id === $user->user_id 
                ? $friend->friend_user_id 
                : $friend->user_id;
        })
        ->toArray();
        
        // Add current user ID to exclude list
        $excludeIds = array_merge($friendIds, [$user->user_id]);
        
        // Get random users excluding current user and friends
        $suggestedUsers = User::whereNotIn('user_id', $excludeIds)
            ->inRandomOrder()
            ->limit(12)
            ->get(['user_id', 'name', 'username', 'email'])
            ->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'username' => $user->username ?? $user->name,
                    'email' => $user->email,
                ];
            });
        
        return response()->json([
            'data' => $suggestedUsers,
            'message' => 'Suggested users retrieved successfully'
        ], 200);
    }

    /**
     * Get user profile with stats by user ID
     * GET /api/users/{userId}/profile
     */
    public function profile(Request $request, $userId)
    {
        $viewer = Auth::user();
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        
        // Check if viewer is friend (for privacy settings)
        $isFriend = Friend::where(function($q) use ($viewer, $userId) {
            $q->where('user_id', $viewer->user_id)
              ->where('friend_user_id', $userId)
              ->where('status', 'Accepted');
        })->orWhere(function($q) use ($viewer, $userId) {
            $q->where('user_id', $userId)
              ->where('friend_user_id', $viewer->user_id)
              ->where('status', 'Accepted');
        })->exists();
        
        // Get user's games
        $userGames = \App\Models\UserGame::where('user_id', $userId)
            ->with(['game.genre', 'game.platforms'])
            ->get();
        
        // Filter out wishlist games
        $libraryGames = $userGames->filter(fn($g) => strtolower($g->status ?? '') !== 'wishlist');
        
        // Calculate stats
        $gamesOwned = $libraryGames->count();
        $gamesCompleted = $libraryGames->filter(fn($g) => strtolower($g->status ?? '') === 'completed')->count();
        $totalPlaytime = $libraryGames->sum('playtime_hours') ?? 0;
        
        // Get reviews count
        $reviewsWritten = \App\Models\Review::where('user_id', $userId)->count();
        
        // Get favorite genres
        $genreCounts = [];
        $libraryGames->each(function($ug) use (&$genreCounts) {
            $genre = $ug->game->genre ?? null;
            if ($genre) {
                $genreName = $genre->name ?? 'Unknown';
                $genreCounts[$genreName] = ($genreCounts[$genreName] ?? 0) + 1;
            }
        });
        $favoriteGenres = collect($genreCounts)
            ->sortDesc()
            ->take(5)
            ->keys()
            ->toArray();
        
        // Get recent activity (placeholder for now)
        $recentActivity = [];
        
        return response()->json([
            'data' => [
                'user_id' => $targetUser->user_id,
                'name' => $targetUser->name,
                'username' => $targetUser->username ?? $targetUser->name,
                'email' => $targetUser->email,
                'avatar' => $targetUser->avatar_url ?? null,
                'bio' => null, // Add bio field if exists
                'status' => 'Offline', // Add online status if exists
                'memberSince' => $targetUser->created_at ? $targetUser->created_at->format('M Y') : null,
                'stats' => [
                    'gamesOwned' => $gamesOwned,
                    'gamesCompleted' => $gamesCompleted,
                    'totalPlaytime' => $totalPlaytime,
                    'reviewsWritten' => $reviewsWritten
                ],
                'favoriteGenres' => $favoriteGenres,
                'recentActivity' => $recentActivity,
                'socialLinks' => [], // Add social links if exists
                'isFriend' => $isFriend
            ],
            'message' => 'User profile retrieved successfully'
        ], 200);
    }

    /**
     * Update authenticated user's profile
     * PUT /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
                'bio' => 'nullable|string|max:1000',
            ]);
            
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            // Bio field may not exist in users table
            // If it doesn't exist, this will be ignored
            if (isset($validated['bio']) && Schema::hasColumn('users', 'bio')) {
                $user->bio = $validated['bio'];
            }
            
            $user->save();
            
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user_info' => [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'bio' => $user->bio ?? null,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update Profile Error: ' . $e->getMessage());
            
            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Failed to update profile',
            ], 500);
        }
    }

    /**
     * Change authenticated user's password
     * PUT /api/user/password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|same:new_password',
            ]);
            
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'response_code' => 401,
                    'status' => 'error',
                    'message' => 'Current password is incorrect',
                ], 401);
            }
            
            // Update password
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Password changed successfully',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Change Password Error: ' . $e->getMessage());
            
            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Failed to change password',
            ], 500);
        }
    }

    /**
     * Update authenticated user's username
     * PUT /api/user/username
     */
    public function updateUsername(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user has already changed username
            if ($user->username_changed_at) {
                return response()->json([
                    'response_code' => 403,
                    'status' => 'error',
                    'message' => 'You can only change your username once.',
                ], 403);
            }
            
            $validated = $request->validate([
                'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->user_id . ',user_id',
            ]);
            
            $user->username = $validated['username'];
            $user->username_changed_at = now();
            $user->save();
            
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Username updated successfully',
                'user_info' => [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update Username Error: ' . $e->getMessage());
            
            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Failed to update username',
            ], 500);
        }
    }
}

