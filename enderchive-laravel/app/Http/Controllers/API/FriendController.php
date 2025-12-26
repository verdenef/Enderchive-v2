<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use App\Http\Requests\StoreFriendRequest;
use App\Http\Requests\UpdateFriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FriendController extends Controller
{
    /**
     * Display a listing of the user's friends and friend requests.
     * GET /api/friends
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Friend::with(['user', 'friendUser']);
        
        // Filter by status first (before user filter)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by sent/received
        if ($request->has('type')) {
            if ($request->type === 'sent') {
                // User sent the request (show only requests where this user is the sender)
                $query->where('user_id', $user->user_id);
            } elseif ($request->type === 'received') {
                // User received the request (show only requests where this user is the recipient)
                $query->where('friend_user_id', $user->user_id);
            }
        } else {
            // If no type filter, show all relationships involving this user
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->user_id)
                  ->orWhere('friend_user_id', $user->user_id);
            });
        }
        
        $friends = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Transform the data to show more useful information
        $transformedData = collect($friends->items())->map(function($friend) use ($user) {
            try {
                // Determine which user is the "friend" (the other user)
                $friendUser = $friend->user_id === $user->user_id 
                    ? $friend->friendUser 
                    : $friend->user;
                
                // Format created_at safely
                $createdAt = null;
                if ($friend->created_at) {
                    try {
                        // Try toIso8601String first (Laravel 9+)
                        if (method_exists($friend->created_at, 'toIso8601String')) {
                            $createdAt = $friend->created_at->toIso8601String();
                        } else {
                            // Fallback to toDateTimeString or format
                            $createdAt = $friend->created_at->toDateTimeString();
                        }
                    } catch (\Exception $e) {
                        $createdAt = $friend->created_at->format('Y-m-d\TH:i:s\Z');
                    }
                }
                
                return [
                    'friend_id' => $friend->friend_id,
                    'user_id' => $friend->user_id,
                    'friend_user_id' => $friend->friend_user_id,
                    'status' => $friend->status,
                    'created_at' => $createdAt,
                    'friendUser' => $friendUser ? [
                        'user_id' => $friendUser->user_id,
                        'name' => $friendUser->name ?? null,
                        'username' => $friendUser->username ?? null,
                    ] : null,
                    'user' => $friend->user ? [
                        'user_id' => $friend->user->user_id,
                        'name' => $friend->user->name ?? null,
                        'username' => $friend->user->username ?? null,
                    ] : null,
                ];
            } catch (\Exception $e) {
                \Log::error('Error transforming friend data', [
                    'friend_id' => $friend->friend_id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }
        })->filter(); // Remove null entries
        
        return response()->json([
            'data' => $transformedData,
            'meta' => [
                'current_page' => $friends->currentPage(),
                'last_page' => $friends->lastPage(),
                'per_page' => $friends->perPage(),
                'total' => $friends->total(),
            ],
            'message' => 'Friends retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created friend request in storage.
     * POST /api/friends
     */
    public function store(StoreFriendRequest $request)
    {
        $user = Auth::user();
        
        // Check if friend relationship already exists (any status)
        $existingFriend = Friend::where(function($q) use ($user, $request) {
            $q->where('user_id', $user->user_id)
              ->where('friend_user_id', $request->friend_user_id);
        })->orWhere(function($q) use ($user, $request) {
            $q->where('user_id', $request->friend_user_id)
              ->where('friend_user_id', $user->user_id);
        })->first();
        
        if ($existingFriend) {
            // Provide more specific error messages based on status
            if ($existingFriend->status === 'Accepted') {
                return response()->json([
                    'message' => 'You are already friends with this user',
                    'relationship_status' => 'accepted',
                    'friend_id' => $existingFriend->friend_id
                ], 409);
            } elseif ($existingFriend->status === 'Pending') {
                // Determine if it's incoming or outgoing
                if ($existingFriend->user_id === $user->user_id) {
                    return response()->json([
                        'message' => 'You have already sent a friend request to this user',
                        'relationship_status' => 'pending_outgoing',
                        'friend_id' => $existingFriend->friend_id
                    ], 409);
                } else {
                    return response()->json([
                        'message' => 'This user has already sent you a friend request. Please check your incoming requests.',
                        'relationship_status' => 'pending_incoming',
                        'friend_id' => $existingFriend->friend_id
                    ], 409);
                }
            } else {
                return response()->json([
                    'message' => 'Friend request already exists',
                    'relationship_status' => $existingFriend->status,
                    'friend_id' => $existingFriend->friend_id
                ], 409);
            }
        }
        
        // Check if trying to add self as friend
        if ($user->user_id == $request->friend_user_id) {
            return response()->json([
                'message' => 'Cannot add yourself as a friend'
            ], 400);
        }
        
        $friend = Friend::create([
            'user_id' => $user->user_id,
            'friend_user_id' => $request->friend_user_id,
            'status' => 'Pending',
        ]);
        
        $friend->load(['user', 'friendUser']);
        
        return response()->json([
            'data' => $friend,
            'message' => 'Friend request sent successfully'
        ], 201);
    }

    /**
     * Display the specified friend relationship.
     * GET /api/friends/{friend}
     */
    public function show($friend)
    {
        $user = Auth::user();
        
        // Find the friend relationship by ID
        $friendRelation = Friend::find($friend);
        
        if (!$friendRelation) {
            return response()->json([
                'message' => 'Friend relationship not found'
            ], 404);
        }
        
        // Ensure user can only view their own friend relationships
        if ($friendRelation->user_id !== $user->user_id && $friendRelation->friend_user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to friend relationship'
            ], 403);
        }
        
        $friendRelation->load(['user', 'friendUser']);
        
        return response()->json([
            'data' => $friendRelation,
            'message' => 'Friend relationship retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified friend relationship in storage.
     * PUT /api/friends/{friend}
     */
    public function update(UpdateFriendRequest $request, $friend)
    {
        $user = Auth::user();
        
        $friendRelation = Friend::find($friend);
        
        if (!$friendRelation) {
            return response()->json([
                'message' => 'Friend relationship not found'
            ], 404);
        }
        
        // Check if user is trying to accept their own friend request
        if ($friendRelation->user_id === $user->user_id) {
            return response()->json([
                'message' => 'Cannot accept your own friend request'
            ], 400);
        }
        
        // Ensure user can only update friend requests sent to them
        if ($friendRelation->friend_user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to friend relationship'
            ], 403);
        }
        
        // Only allow updating status to 'Accepted' or 'Blocked'
        if (!in_array($request->status, ['Accepted', 'Blocked'])) {
            return response()->json([
                'message' => 'Invalid status update'
            ], 400);
        }
        
        $friendRelation->update(['status' => $request->status]);
        $friendRelation->load(['user', 'friendUser']);
        
        return response()->json([
            'data' => $friendRelation,
            'message' => 'Friend request updated successfully'
        ], 200);
    }

    /**
     * Remove the specified friend relationship from storage.
     * DELETE /api/friends/{friend}
     */
    public function destroy($friend)
    {
        $user = Auth::user();
        
        $friendRelation = Friend::find($friend);
        
        if (!$friendRelation) {
            return response()->json([
                'message' => 'Friend relationship not found'
            ], 404);
        }
        
        // Ensure user can only delete their own friend relationships
        if ($friendRelation->user_id !== $user->user_id && $friendRelation->friend_user_id !== $user->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to friend relationship'
            ], 403);
        }
        
        $friendRelation->delete();
        
        return response()->json([
            'message' => 'Friend relationship removed successfully'
        ], 200);
    }

    /**
     * Get friend requests separated into incoming and sent
     * GET /api/friends/requests
     */
    public function requests(Request $request)
    {
        $user = Auth::user();

        $incoming = Friend::with('user')
            ->where('friend_user_id', $user->user_id)
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Friend $relation) {
                return [
                    'id' => $relation->friend_id,
                    'user_id' => $relation->user_id,
                    'username' => $relation->user?->username ?? $relation->user?->name ?? 'Unknown',
                    'name' => $relation->user?->name ?? 'Unknown',
                    'created_at' => $relation->created_at?->toIso8601String(),
                ];
            });

        $sent = Friend::with('friendUser')
            ->where('user_id', $user->user_id)
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Friend $relation) {
                return [
                    'id' => $relation->friend_id,
                    'user_id' => $relation->friend_user_id,
                    'username' => $relation->friendUser?->username ?? $relation->friendUser?->name ?? 'Unknown',
                    'name' => $relation->friendUser?->name ?? 'Unknown',
                    'created_at' => $relation->created_at?->toIso8601String(),
                ];
            });

        // Debug: Also return all relationships (for debugging)
        $allRelationships = Friend::where(function($q) use ($user) {
            $q->where('user_id', $user->user_id)
              ->orWhere('friend_user_id', $user->user_id);
        })->get()->map(function($rel) {
            return [
                'friend_id' => $rel->friend_id,
                'user_id' => $rel->user_id,
                'friend_user_id' => $rel->friend_user_id,
                'status' => $rel->status,
            ];
        });

        return response()->json([
            'incoming' => $incoming,
            'sent' => $sent,
            'debug_all_relationships' => $allRelationships, // Remove this after debugging
        ]);
    }

    /**
     * Search for potential friends by username or display name.
     * GET /api/friends/search?q=query
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'q' => ['required', 'string', 'max:255'],
        ]);

        $q = $data['q'];

        $candidates = User::query()
            ->where(function ($builder) use ($q) {
                $builder
                    ->where('username', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->where('user_id', '!=', $user->user_id)
            ->orderBy('username')
            ->limit(20)
            ->get();

        $results = $candidates->map(function (User $candidate) use ($user) {
            $relation = Friend::where(function ($builder) use ($user, $candidate) {
                    $builder->where('user_id', $user->user_id)
                        ->where('friend_user_id', $candidate->user_id);
                })
                ->orWhere(function ($builder) use ($user, $candidate) {
                    $builder->where('user_id', $candidate->user_id)
                        ->where('friend_user_id', $user->user_id);
                })
                ->first();

            $status = 'none';
            if ($relation) {
                if ($relation->status === 'Accepted') {
                    $status = 'friend';
                } elseif ($relation->status === 'Pending') {
                    $status = $relation->user_id === $user->user_id ? 'pending_outgoing' : 'pending_incoming';
                }
            }

            return [
                'user_id' => $candidate->user_id,
                'username' => $candidate->username ?? 'unknown',
                'name' => $candidate->name ?? 'Unknown',
                'status' => $status,
            ];
        });

        return response()->json([
            'data' => $results,
            'message' => 'Search completed successfully'
        ], 200);
    }
}