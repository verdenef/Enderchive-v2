<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameMilestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index($game)
    {
        $gameModel = \App\Models\Game::where('game_id', $game)->firstOrFail();
        $milestones = GameMilestone::where('game_id', $gameModel->game_id)
            ->orderByRaw('COALESCE(sequence, 999999) ASC')
            ->get();

        return response()->json(['data' => $milestones]);
    }

    public function store(Request $request, $game)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:0',
            'sequence' => 'nullable|integer|min:0',
            'is_optional' => 'boolean',
        ]);

        $gameModel = \App\Models\Game::where('game_id', $game)->firstOrFail();
        
        // Check for duplicate code for this game
        $existing = GameMilestone::where('game_id', $gameModel->game_id)
            ->where('code', $validated['code'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'An achievement with this code already exists for this game.'
            ], 409);
        }

        $validated['game_id'] = $gameModel->game_id;

        $milestone = GameMilestone::create($validated);
        $milestone->load('game');

        return response()->json([
            'data' => $milestone,
            'message' => 'Achievement created successfully!'
        ], 201);
    }
}


