<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kayak;

class KayakSeeder extends Seeder
{
    public function run(): void
    {
        Kayak::create([
            'type' => 'Single Kayak',
            'price_per_hour' => 300,
            'total_quantity' => 2,
        ]);

        Kayak::create([
            'type' => 'Double Kayak',
            'price_per_hour' => 500,
            'total_quantity' => 4,
        ]);
    }
}


