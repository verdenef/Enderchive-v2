<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameOwnership extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_game_ownership_id';

    protected $fillable = [
        'user_id',
        'game_id',
        'platform_id',
        'store_id',
        'edition_id',
        'ownership_type',
        'media_type',
        'account_identifier',
        'purchase_date',
        'purchase_price',
        'purchase_currency',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'platform_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    public function edition()
    {
        return $this->belongsTo(Edition::class, 'edition_id', 'edition_id');
    }
}


