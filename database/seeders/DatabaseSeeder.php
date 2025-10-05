<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            LocationSeeder::class,    // <-- TAMBAHKAN INI DI SINI
            UserSeeder::class,        // Ini untuk user Admin
            DefaultUserSeeder::class, // Ini akan membuat 5 pengajar & 5 karyawan
            CourseTypeSeeder::class,
            CategorySeeder::class,
        ]);
    }
}