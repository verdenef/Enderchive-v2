<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::with(['genre', 'platforms', 'tags']);
        
        if ($request->has('q') && $request->q) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        
        $games = $query->limit(20)->get();
        
        return Inertia::render('Search', [
            'games' => $games,
            'searchQuery' => $request->q ?? '',
        ]);
    }
}

