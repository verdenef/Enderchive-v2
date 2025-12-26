<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            'Steam',
            'Epic Games Store',
            'GOG',
            'PlayStation Store',
            'Xbox Store',
            'Nintendo eShop',
        ];

        foreach ($stores as $name) {
            Store::firstOrCreate(['name' => $name]);
        }
    }
}


