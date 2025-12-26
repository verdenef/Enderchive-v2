<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Edition;
use Illuminate\Http\Request;

class EditionController extends Controller
{
    public function index(Game $game)
    {
        $editions = Edition::where('game_id', $game->game_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $editions,
            'message' => 'Editions retrieved successfully'
        ], 200);
    }
}

