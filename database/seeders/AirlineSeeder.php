<?php
// database/seeders/AirlineSeeder.php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            ['code' => 'GA', 'name' => 'Garuda Indonesia', 'country' => 'Indonesia'],
            ['code' => 'SJ', 'name' => 'Sriwijaya Air', 'country' => 'Indonesia'],
            ['code' => 'QZ', 'name' => 'AirAsia Indonesia', 'country' => 'Indonesia'],
            ['code' => 'JT', 'name' => 'Lion Air', 'country' => 'Indonesia'],
            ['code' => 'ID', 'name' => 'Batik Air', 'country' => 'Indonesia'],
            ['code' => 'IU', 'name' => 'Super Air Jet', 'country' => 'Indonesia'],
            ['code' => 'QG', 'name' => 'Citilink', 'country' => 'Indonesia'],
            ['code' => 'IN', 'name' => 'NAM Air', 'country' => 'Indonesia'],
        ];

        foreach ($airlines as $airline) {
            Airline::create($airline);
        }
    }
}