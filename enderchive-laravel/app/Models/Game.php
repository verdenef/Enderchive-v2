<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'game_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'genre_id',
        'developer',
        'publisher',
        'release_date',
        'cover_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'date',
    ];

    /**
     * Get the genre that owns the game.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'genre_id');
    }

    /**
     * Get the platforms for the game.
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'game_platform', 'game_id', 'platform_id', 'game_id', 'platform_id');
    }

    /**
     * Get the users for the game.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_games', 'game_id', 'user_id', 'game_id', 'user_id');
    }

    /**
     * Get the user games for the game.
     */
    public function userGames()
    {
        return $this->hasMany(UserGame::class, 'game_id', 'game_id');
    }

    /**
     * Get the reviews for the game.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'game_id', 'game_id');
    }

    /**
     * Get the tags for the game.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'game_tag', 'game_id', 'tag_id', 'game_id', 'tag_id');
    }

    /**
     * Get milestones for the game.
     */
    public function milestones()
    {
        return $this->hasMany(GameMilestone::class, 'game_id', 'game_id');
    }

    /**
     * Get editions for the game.
     */
    public function editions()
    {
        return $this->hasMany(Edition::class, 'game_id', 'game_id');
    }

    /**
     * Get ownerships for the game.
     */
    public function ownerships()
    {
        return $this->hasMany(UserGameOwnership::class, 'game_id', 'game_id');
    }

    /**
     * Get progress records for the game.
     */
    public function progress()
    {
        return $this->hasMany(UserGameProgress::class, 'game_id', 'game_id');
    }
}
