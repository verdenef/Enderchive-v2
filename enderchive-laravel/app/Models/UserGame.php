<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGame extends Model
{
    use HasFactory;
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_game_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'game_id',
        'status',
        'rating',
        'playtime_hours',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'playtime_hours' => 'integer',
    ];

    /**
     * Get the user that owns the user game.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the game that owns the user game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }
    
    /**
     * Get ownerships for this user/game pair.
     */
    public function ownerships()
    {
        return $this->hasMany(UserGameOwnership::class, 'game_id', 'game_id')
            ->whereColumn('user_game_ownerships.user_id', 'user_games.user_id');
    }

    /**
     * Get progress records for this user/game.
     */
    public function progress()
    {
        return $this->hasMany(UserGameProgress::class, 'user_game_id', 'user_game_id');
    }

    /**
     * Get stats for this user/game.
     */
    public function stats()
    {
        return $this->hasOne(UserGameStat::class, 'user_game_id', 'user_game_id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'user_game_id';
    }
}
