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
        // Example: create one test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ✅ Call other seeders here
            $this->call(\Database\Seeders\KayakSeeder::class);
            // You can add more seeders here if needed
    }
}
