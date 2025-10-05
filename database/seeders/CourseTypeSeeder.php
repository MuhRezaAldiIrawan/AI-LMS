<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseType;

class CourseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseType::firstOrCreate(['name' => 'Umum']);
        CourseType::firstOrCreate(['name' => 'Training']);
        CourseType::firstOrCreate(['name' => 'Mandatory']);
    }
}