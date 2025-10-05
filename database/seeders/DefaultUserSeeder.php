<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 5 akun Pengajar baru menggunakan Factory, lalu memberikan role
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('pengajar');
        });

        // Membuat 5 akun Karyawan baru menggunakan Factory, lalu memberikan role
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('karyawan');
        });
    }
}