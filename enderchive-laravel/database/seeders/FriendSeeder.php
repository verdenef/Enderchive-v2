<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        if ($users->count() < 2) {
            return;
        }

        $userIds = $users->pluck('user_id')->toArray();
        $created = 0;
        $maxAttempts = 200; // Prevent infinite loop
        $attempts = 0;
        
        while ($created < 100 && $attempts < $maxAttempts) {
            $attempts++;
            $user1 = $userIds[array_rand($userIds)];
            $user2 = $userIds[array_rand($userIds)];

            if ($user1 !== $user2) {
                $friend = \App\Models\Friend::firstOrCreate(
                    [
                        'user_id' => $user1,
                        'friend_user_id' => $user2,
                    ],
                    [
                        'status' => ['Pending', 'Accepted', 'Blocked'][array_rand(['Pending', 'Accepted', 'Blocked'])],
                    ]
                );
                
                if ($friend->wasRecentlyCreated) {
                    $created++;
                }
            }
        }
    }
}


