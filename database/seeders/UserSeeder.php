<?php
// File: database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari atau buat user baru
        $admin = User::firstOrCreate(
            // Kondisi untuk mencari user
            ['email' => 'admin@bosowa.com'],
            // Data yang akan dibuat jika user tidak ditemukan
            [
                'name' => 'Admin Bosowa',
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang aman
            ]
        );

        // Berikan role 'admin' ke user tersebut
        $admin->assignRole('admin');
    }
}
