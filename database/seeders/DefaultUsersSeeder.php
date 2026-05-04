<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@cabanas.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'usertype' => 'admin',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Frontdesk User
        User::firstOrCreate(
            ['email' => 'frontdesk@cabanas.com'],
            [
                'name' => 'Frontdesk User',
                'password' => Hash::make('frontdesk123'),
                'usertype' => 'frontdesk',
                'role' => 'frontdesk',
                'email_verified_at' => now(),
            ]
        );

        // Create Regular User
        User::firstOrCreate(
            ['email' => 'user@cabanas.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('user123'),
                'usertype' => 'user',
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Default users created successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Admin:     admin@cabanas.com / admin123');
        $this->command->info('Frontdesk: frontdesk@cabanas.com / frontdesk123');
        $this->command->info('User:      user@cabanas.com / user123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
