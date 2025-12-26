<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'username_changed_at',
        'bio',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Get the user's games.
     */
    public function userGames()
    {
        return $this->hasMany(UserGame::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's friends (where this user is the initiator).
     */
    public function friends()
    {
        return $this->hasMany(Friend::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's friend requests (where this user is the target).
     */
    public function friendRequests()
    {
        return $this->hasMany(Friend::class, 'friend_user_id', 'user_id');
    }

    /**
     * Get the games through user_games relationship.
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'user_games', 'user_id', 'game_id', 'user_id', 'game_id');
    }
}
