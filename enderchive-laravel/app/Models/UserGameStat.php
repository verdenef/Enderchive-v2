<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameStat extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_game_stat_id';

    protected $fillable = [
        'user_id',
        'game_id',
        'user_game_id',
        'hours_played',
        'percent_override',
    ];

    protected $casts = [
        'hours_played' => 'decimal:1',
        'percent_override' => 'integer',
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
}


