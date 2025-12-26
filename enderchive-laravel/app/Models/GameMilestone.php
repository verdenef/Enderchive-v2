<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMilestone extends Model
{
    use HasFactory;

    protected $primaryKey = 'milestone_id';

    protected $fillable = [
        'game_id',
        'code',
        'name',
        'description',
        'points',
        'sequence',
        'is_optional',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'points' => 'integer',
        'sequence' => 'integer',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function getRouteKeyName()
    {
        return 'milestone_id';
    }
}


