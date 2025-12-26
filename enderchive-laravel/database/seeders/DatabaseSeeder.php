<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlatformSeeder::class,
            StoreSeeder::class,
            GenreSeeder::class,
            TagSeeder::class,
            UserSeeder::class,
            GameSeeder::class,
            EditionSeeder::class,
            GameMilestoneSeeder::class,
            UserGameSeeder::class,
            UserGameOwnershipSeeder::class,
            UserGameStatSeeder::class,
            UserGameProgressSeeder::class,
            ReviewSeeder::class,
            FriendSeeder::class,
        ]);
    }
}
