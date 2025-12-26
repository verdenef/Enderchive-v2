<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_game_stats', function (Blueprint $table) {
            $table->id('user_game_stat_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games', 'game_id')->cascadeOnDelete();
            $table->foreignId('user_game_id')->constrained('user_games', 'user_game_id')->cascadeOnDelete();
            $table->decimal('hours_played', 5, 1)->nullable();
            $table->tinyInteger('percent_override')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_game_stats');
    }
};


