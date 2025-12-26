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
        Schema::create('user_games', function (Blueprint $table) {
            $table->id('user_game_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games', 'game_id')->cascadeOnDelete();
            $table->enum('status',['Wishlist','Playing','Completed','Abandoned'])->default('Wishlist');
            $table->tinyInteger('rating')->nullable(); // 1-10
            $table->integer('playtime_hours')->default(0);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_games');
    }
};
