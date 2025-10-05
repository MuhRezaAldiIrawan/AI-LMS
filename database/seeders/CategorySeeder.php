<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate(
            ['name' => 'Teknologi Informasi'],
            ['slug' => Str::slug('Teknologi Informasi')]
        );

        Category::firstOrCreate(
            ['name' => 'Pengembangan Diri'],
            ['slug' => Str::slug('Pengembangan Diri')]
        );

        Category::firstOrCreate(
            ['name' => 'Bisnis & Keuangan'],
            ['slug' => Str::slug('Bisnis & Keuangan')]
        );
    }
}