<?php
// database/seeders/AirportSeeder.php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            [
                'code' => 'CGK',
                'name' => 'Soekarno-Hatta International Airport',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -6.1256,
                'longitude' => 106.6558
            ],
            [
                'code' => 'DPS',
                'name' => 'Ngurah Rai International Airport',
                'city' => 'Denpasar',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Makassar',
                'latitude' => -8.7467,
                'longitude' => 115.1667
            ],
            [
                'code' => 'SUB',
                'name' => 'Juanda International Airport',
                'city' => 'Surabaya',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.3798,
                'longitude' => 112.7869
            ],
            [
                'code' => 'UPG',
                'name' => 'Sultan Hasanuddin International Airport',
                'city' => 'Makassar',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Makassar',
                'latitude' => -5.0616,
                'longitude' => 119.5539
            ],
            [
                'code' => 'KNO',
                'name' => 'Kualanamu International Airport',
                'city' => 'Medan',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => 3.6422,
                'longitude' => 98.8853
            ],
            [
                'code' => 'BTH',
                'name' => 'Hang Nadim International Airport',
                'city' => 'Batam',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => 1.1210,
                'longitude' => 104.1186
            ],
            [
                'code' => 'PLM',
                'name' => 'Sultan Mahmud Badaruddin II International Airport',
                'city' => 'Palembang',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -2.8976,
                'longitude' => 104.6997
            ],
            [
                'code' => 'BDO',
                'name' => 'Husein Sastranegara International Airport',
                'city' => 'Bandung',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -6.9006,
                'longitude' => 107.5764
            ],
            [
                'code' => 'SRG',
                'name' => 'Achmad Yani International Airport',
                'city' => 'Semarang',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -6.9717,
                'longitude' => 110.3750
            ],
            [
                'code' => 'JOG',
                'name' => 'Adisucipto International Airport',
                'city' => 'Yogyakarta',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.7880,
                'longitude' => 110.4317
            ],
            [
                'code' => 'SOC',
                'name' => 'Adisumarmo International Airport',
                'city' => 'Solo',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.5161,
                'longitude' => 110.7569
            ],
            [
                'code' => 'MLG',
                'name' => 'Abdul Rachman Saleh Airport',
                'city' => 'Malang',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.9264,
                'longitude' => 112.7144
            ],
            [
                'code' => 'BPN',
                'name' => 'Sultan Aji Muhammad Sulaiman Airport',
                'city' => 'Balikpapan',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Makassar',
                'latitude' => -1.2683,
                'longitude' => 116.8944
            ],
            [
                'code' => 'PKU',
                'name' => 'Sultan Syarif Kasim II International Airport',
                'city' => 'Pekanbaru',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => 0.4608,
                'longitude' => 101.4450
            ],
            [
                'code' => 'PNK',
                'name' => 'Supadio International Airport',
                'city' => 'Pontianak',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -0.1505,
                'longitude' => 109.4039
            ],
        ];

        foreach ($airports as $airport) {
            Airport::create($airport);
        }
    }
}