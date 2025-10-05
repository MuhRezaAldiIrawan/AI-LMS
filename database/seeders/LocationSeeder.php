<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location; // <-- Import model Location

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::firstOrCreate(['name' => 'Menara Bosowa']);
        Location::firstOrCreate(['name' => 'KIMA Makassar']);
        Location::firstOrCreate(['name' => 'Semen Bosowa Maros']);
        Location::firstOrCreate(['name' => 'Kantor Pusat Jakarta']);
    }
}