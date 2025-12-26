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
        Schema::create('game_milestones', function (Blueprint $table) {
            $table->id('milestone_id');
            $table->foreignId('game_id')->constrained('games', 'game_id')->cascadeOnDelete();
            $table->string('code'); // unique per game
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points')->nullable();
            $table->integer('sequence')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->timestamps();
            $table->unique(['game_id', 'code']);
            $table->index(['game_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_milestones');
    }
};


