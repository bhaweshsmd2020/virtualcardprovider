<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'country_id' => 1,
            'state_id' => 1,
            'name' => 'San Francisco',
            'postal_code' => 94111,
            'status' => 'active',
        ]);
    }
}
