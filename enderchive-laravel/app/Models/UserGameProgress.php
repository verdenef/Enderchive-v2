<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameProgress extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_game_progress_id';

    protected $fillable = [
        'user_id',
        'game_id',
        'user_game_id',
        'milestone_id',
        'achieved_at',
        'notes',
        'evidence_url',
    ];

    protected $casts = [
        'achieved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function userGame()
    {
        return $this->belongsTo(UserGame::class, 'user_game_id', 'user_game_id');
    }

    public function milestone()
    {
        return $this->belongsTo(GameMilestone::class, 'milestone_id', 'milestone_id');
    }
}


