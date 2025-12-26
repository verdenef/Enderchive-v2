<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameMilestone;
use App\Models\UserGame;
use App\Models\UserGameProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameProgressController extends Controller
{
    public function store(Request $request, $game, $milestone)
    {
        $user = Auth::user();

        $gameModel = Game::where('game_id', $game)->firstOrFail();
        $milestoneModel = GameMilestone::where('milestone_id', $milestone)->firstOrFail();

        // Validate milestone belongs to game
        if ($milestoneModel->game_id !== $gameModel->game_id) {
            return response()->json(['message' => 'Milestone does not belong to the specified game'], 422);
        }

        $userGame = UserGame::where('user_id', $user->user_id)
            ->where('game_id', $gameModel->game_id)
            ->first();

        if (!$userGame) {
            return response()->json(['message' => 'Game is not in user library'], 404);
        }

        // Check if already completed
        $existing = UserGameProgress::where('user_id', $user->user_id)
            ->where('game_id', $gameModel->game_id)
            ->where('milestone_id', $milestoneModel->milestone_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already completed this achievement.',
                'data' => $existing
            ], 409);
        }

        $payload = $request->validate([
            'notes' => 'nullable|string|max:500',
            'evidence_url' => 'nullable|url|max:500',
            'achieved_at' => 'nullable|date',
        ]);

        $progress = UserGameProgress::create([
            'user_id' => $user->user_id,
            'game_id' => $gameModel->game_id,
            'user_game_id' => $userGame->user_game_id,
            'milestone_id' => $milestoneModel->milestone_id,
            'achieved_at' => $payload['achieved_at'] ?? now(),
            'notes' => $payload['notes'] ?? null,
            'evidence_url' => $payload['evidence_url'] ?? null,
        ]);

        $progress->load('milestone');

        // Auto-complete status rule: if all required milestones achieved -> Completed
        $requiredCount = GameMilestone::where('game_id', $gameModel->game_id)->where('is_optional', false)->count();
        if ($requiredCount > 0) {
            // Only count achievements that are for required milestones
            $achievedCount = UserGameProgress::where('user_id', $user->user_id)
                ->where('game_id', $gameModel->game_id)
                ->whereHas('milestone', function($q) {
                    $q->where('is_optional', false);
                })
                ->count();
            if ($achievedCount >= $requiredCount && $userGame->status !== 'Completed') {
                $userGame->update(['status' => 'Completed']);
            }
        }

        return response()->json([
            'data' => $progress,
            'message' => 'Achievement completed!'
        ], 201);
    }

    public function destroy($game, $milestone)
    {
        $user = Auth::user();

        $gameModel = Game::where('game_id', $game)->firstOrFail();
        $milestoneModel = GameMilestone::where('milestone_id', $milestone)->firstOrFail();

        if ($milestoneModel->game_id !== $gameModel->game_id) {
            return response()->json(['message' => 'Milestone does not belong to the specified game'], 422);
        }

        $userGame = UserGame::where('user_id', $user->user_id)
            ->where('game_id', $gameModel->game_id)
            ->first();

        if (!$userGame) {
            return response()->json(['message' => 'Game is not in user library'], 404);
        }

        $deleted = UserGameProgress::where('user_id', $user->user_id)
            ->where('game_id', $gameModel->game_id)
            ->where('milestone_id', $milestoneModel->milestone_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Progress not found'], 404);
        }

        // Re-check completion status after unmarking
        $requiredCount = GameMilestone::where('game_id', $gameModel->game_id)->where('is_optional', false)->count();
        if ($requiredCount > 0) {
            $achievedCount = UserGameProgress::where('user_id', $user->user_id)
                ->where('game_id', $gameModel->game_id)
                ->whereHas('milestone', function($q) {
                    $q->where('is_optional', false);
                })
                ->count();
            // If no longer complete, revert to Playing (unless manually set to something else)
            if ($achievedCount < $requiredCount && $userGame->status === 'Completed') {
                $userGame->update(['status' => 'Playing']);
            }
        }

        return response()->json(['message' => 'Progress unmarked']);
    }
}


