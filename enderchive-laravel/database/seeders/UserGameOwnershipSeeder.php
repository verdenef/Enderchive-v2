<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGame;
use App\Models\UserGameOwnership;
use App\Models\Platform;
use App\Models\Store;
use App\Models\Edition;

class UserGameOwnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGames = UserGame::all();
        $platforms = Platform::all();
        $stores = Store::all();

        foreach ($userGames as $userGame) {
            // 80% chance of having ownership record
            if (rand(1, 100) <= 80) {
                $numOwnerships = rand(1, 2); // User can own same game on 1-2 platforms
                
                for ($i = 0; $i < $numOwnerships; $i++) {
                    $platform = $platforms->random();
                    
                    // Get editions for this game (if any)
                    $editions = Edition::where('game_id', $userGame->game_id)->get();
                    $edition = $editions->isNotEmpty() && rand(1, 100) <= 40 
                        ? $editions->random() 
                        : null;

                    UserGameOwnership::firstOrCreate(
                        [
                            'user_id' => $userGame->user_id,
                            'game_id' => $userGame->game_id,
                            'platform_id' => $platform->platform_id,
                            'edition_id' => $edition ? $edition->edition_id : null,
                        ],
                        [
                            'store_id' => $stores->isNotEmpty() && rand(1, 100) <= 70 
                                ? $stores->random()->store_id 
                                : null,
                            'ownership_type' => $this->getRandomOwnershipType(),
                            'media_type' => rand(1, 100) <= 80 ? 'Digital' : 'Physical',
                            'account_identifier' => rand(1, 100) <= 50 ? 'user_' . rand(1000, 9999) : null,
                            'purchase_date' => rand(1, 100) <= 60 
                                ? now()->subDays(rand(1, 730)) 
                                : null,
                            'purchase_price' => rand(1, 100) <= 50 
                                ? round(rand(500, 9999) / 100, 2) 
                                : null,
                            'purchase_currency' => rand(1, 100) <= 40 
                                ? ['USD', 'EUR', 'GBP', 'JPY'][array_rand(['USD', 'EUR', 'GBP', 'JPY'])] 
                                : null,
                        ]
                    );
                }
            }
        }
    }

    private function getRandomOwnershipType(): string
    {
        $types = ['Owned', 'Subscription', 'Borrowed', 'Gifted'];
        $weights = [80, 10, 5, 5]; // Most likely to be Owned
        
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($types as $index => $type) {
            $cumulative += $weights[$index];
            if ($rand <= $cumulative) {
                return $type;
            }
        }
        
        return 'Owned';
    }
}

