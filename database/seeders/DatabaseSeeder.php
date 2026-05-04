<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin, frontdesk, and user accounts
        $this->call(\Database\Seeders\DefaultUsersSeeder::class);

        // Call other seeders
        $this->call(\Database\Seeders\KayakSeeder::class);
    }
}
