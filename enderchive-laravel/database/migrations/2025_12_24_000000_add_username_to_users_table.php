<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add nullable username column (no unique constraint yet)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->nullable()->after('name');
            }
        });

        // 2) Backfill unique usernames for existing users
        $users = DB::table('users')
            ->select('user_id', 'email', 'name')
            ->whereNull('username')
            ->orWhere('username', '')
            ->get();

        foreach ($users as $user) {
            // Generate username from email or name
            $base = explode('@', $user->email)[0] ?: 'user' . $user->user_id;
            // Clean up the base (remove special chars, make lowercase)
            $base = strtolower(preg_replace('/[^a-z0-9]/', '', $base));
            // If base is empty, use user ID
            if (empty($base)) {
                $base = 'user' . $user->user_id;
            }
            
            $username = $base;
            $original = $base;
            $suffix = 1;

            // Ensure uniqueness
            while (
                DB::table('users')
                    ->where('username', $username)
                    ->where('user_id', '!=', $user->user_id)
                    ->exists()
            ) {
                $username = $original . $suffix;
                $suffix++;
            }

            DB::table('users')->where('user_id', $user->user_id)->update(['username' => $username]);
        }

        // 3) Add unique index on username (allows multiple NULLs but all existing are now unique)
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username', 'users_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }
        });
    }
};

