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
        Schema::dropIfExists('user_game_ownerships');
        Schema::create('user_game_ownerships', function (Blueprint $table) {
            $table->id('user_game_ownership_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games', 'game_id')->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained('platforms', 'platform_id')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores', 'store_id')->nullOnDelete();
            $table->foreignId('edition_id')->nullable()->constrained('editions', 'edition_id')->nullOnDelete();
            $table->enum('ownership_type', ['Owned','Subscription','Borrowed','Gifted'])->default('Owned');
            $table->enum('media_type', ['Digital','Physical'])->default('Digital');
            $table->string('account_identifier')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->char('purchase_currency', 3)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'game_id', 'platform_id', 'edition_id'], 'ugo_user_game_platform_edition_unique');
            $table->index(['user_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_game_ownerships');
    }
};


