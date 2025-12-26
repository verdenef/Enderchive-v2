<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\UserGameOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameOwnershipController extends Controller
{
    public function index(Game $game)
    {
        $user = Auth::user();

        $ownerships = UserGameOwnership::with(['platform', 'store', 'edition'])
            ->where('user_id', $user->user_id)
            ->where('game_id', $game->game_id)
            ->get();

        return response()->json(['data' => $ownerships], 200);
    }

    public function store(Request $request, Game $game)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,platform_id',
            'store_id' => 'nullable|exists:stores,store_id',
            'edition_id' => [
                'nullable',
                'exists:editions,edition_id',
                function ($attribute, $value, $fail) use ($game) {
                    if ($value && !\App\Models\Edition::where('edition_id', $value)
                        ->where('game_id', $game->game_id)
                        ->exists()) {
                        $fail('The edition must belong to the specified game.');
                    }
                },
            ],
            'ownership_type' => 'nullable|in:Owned,Subscription,Borrowed,Gifted',
            'media_type' => 'nullable|in:Digital,Physical',
            'account_identifier' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'purchase_currency' => 'nullable|string|size:3',
        ]);

        $ownership = UserGameOwnership::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'game_id' => $game->game_id,
                'platform_id' => $validated['platform_id'],
                'edition_id' => $validated['edition_id'] ?? null,
            ],
            [
                'store_id' => $validated['store_id'] ?? null,
                'ownership_type' => $validated['ownership_type'] ?? 'Owned',
                'media_type' => $validated['media_type'] ?? 'Digital',
                'account_identifier' => $validated['account_identifier'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'purchase_currency' => $validated['purchase_currency'] ?? null,
            ]
        );

        $ownership->load(['platform', 'store', 'edition']);

        return response()->json(['data' => $ownership], 201);
    }

    public function destroy(Game $game, $ownership)
    {
        $user = Auth::user();

        $ownership = UserGameOwnership::find($ownership);

        if (!$ownership || $ownership->user_id !== $user->user_id || $ownership->game_id !== $game->game_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ownership->delete();

        return response()->json(['message' => 'Ownership deleted successfully'], 200);
    }
}


