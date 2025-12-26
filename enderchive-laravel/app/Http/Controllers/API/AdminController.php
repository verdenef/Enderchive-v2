<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;
use App\Models\UserGame;
use App\Models\Review;

class AdminController extends Controller
{
    /**
     * Check if user is admin
     */
    protected function checkAdmin(Request $request)
    {
        // Use web guard explicitly for session-based auth
        $user = Auth::guard('web')->user();
        
        \Log::info('AdminController checkAdmin', [
            'user_id' => $user?->user_id,
            'email' => $user?->email,
            'is_authenticated' => Auth::guard('web')->check(),
            'has_session' => $request->hasSession(),
            'session_id' => $request->session()?->getId(),
            'session_user_id' => $request->session()->get('login_web_' . sha1('Illuminate\Auth\SessionGuard')),
        ]);
        
        if (!$user || $user->email !== 'admin@example.com') {
            abort(403, 'Admin access required');
        }
    }

    /**
     * Get admin dashboard summary statistics
     * GET /api/admin/summary
     */
    public function summary(Request $request)
    {
        $this->checkAdmin($request);
        
        return response()->json([
            'data' => [
                'total_users' => User::count(),
                'total_user_games' => UserGame::count(),
                'distinct_games' => UserGame::query()
                    ->whereNotIn('status', ['wishlist', 'Wishlist'])
                    ->distinct('game_id')
                    ->count('game_id'),
                'total_reviews' => Review::count(),
            ],
        ]);
    }

    /**
     * Get list of all users with stats
     * GET /api/admin/users
     */
    public function users(Request $request)
    {
        $this->checkAdmin($request);
        
        $users = User::withCount([
            'userGames as library_entries',
            'userGames as completed_games' => function ($query) {
                $query->where('status', 'Completed');
            },
            'userGames as wishlist_games' => function ($query) {
                $query->where('status', 'Wishlist');
            },
            'reviews as reviews_count'
        ])->orderBy('user_id')->get();
        
        // Format users to include username
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->user_id,
                'name' => $user->name,
                'username' => $user->username ?? $user->name,
                'email' => $user->email,
                'library_entries' => $user->library_entries ?? 0,
                'completed_games' => $user->completed_games ?? 0,
                'wishlist_games' => $user->wishlist_games ?? 0,
                'reviews_count' => $user->reviews_count ?? 0,
            ];
        });
        
        return response()->json([
            'data' => $formattedUsers,
        ]);
    }

    /**
     * Get recent activity (users, library updates, reviews)
     * GET /api/admin/activity
     */
    public function activity(Request $request)
    {
        $this->checkAdmin($request);
        
        $recentUsers = User::query()
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user->name,
                    'action' => 'Registered',
                    'context' => null,
                    'type' => 'user',
                    'created_at' => $user->created_at,
                ];
            });

        $recentLibraries = UserGame::query()
            ->with('user')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function ($entry) {
                return [
                    'user' => $entry->user->name ?? 'Unknown',
                    'action' => 'Updated library',
                    'context' => $entry->game_id,
                    'type' => 'library',
                    'status' => $entry->status,
                    'created_at' => $entry->updated_at ?? $entry->created_at,
                ];
            });

        $recentReviews = Review::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($review) {
                return [
                    'user' => $review->user->name ?? 'Unknown',
                    'action' => 'Wrote review',
                    'context' => $review->game_id,
                    'type' => 'review',
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                ];
            });

        $activities = collect()
            ->concat($recentUsers)
            ->concat($recentLibraries)
            ->concat($recentReviews)
            ->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->map(function ($activity) {
                return [
                    'user' => $activity['user'],
                    'action' => $activity['action'],
                    'context' => $activity['context'],
                    'type' => $activity['type'],
                    'status' => $activity['status'] ?? null,
                    'rating' => $activity['rating'] ?? null,
                    'time_ago' => $activity['created_at'] ? \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() : null,
                ];
            });

        return response()->json([
            'data' => $activities,
        ]);
    }
}

