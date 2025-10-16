<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'nik' => 'ADMIN001',
                'join_date' => now(),
                'position' => 'Administrator',
                'division' => 'IT',
                'location_id' => Location::first()?->id ?? 1,
            ]
        );
        $admin->assignRole('admin');

        // Membuat default pengajar user
        $pengajar = User::firstOrCreate(
            ['email' => 'pengajar@lms.com'],
            [
                'name' => 'Pengajar Demo',
                'password' => bcrypt('password'),
                'nik' => 'PGJ001',
                'join_date' => now(),
                'position' => 'Instructor',
                'division' => 'Education',
                'location_id' => Location::first()?->id ?? 1,
            ]
        );
        $pengajar->assignRole('pengajar');

        // Membuat default karyawan user
        $karyawan = User::firstOrCreate(
            ['email' => 'karyawan@lms.com'],
            [
                'name' => 'Karyawan Demo',
                'password' => bcrypt('password'),
                'nik' => 'KRY001',
                'join_date' => now(),
                'position' => 'Employee',
                'division' => 'General',
                'location_id' => Location::first()?->id ?? 1,
            ]
        );
        $karyawan->assignRole('karyawan');

        // Membuat 5 akun Pengajar baru menggunakan Factory, lalu memberikan role
        // User::factory()->count(5)->create()->each(function ($user) {
        //     $user->assignRole('pengajar');
        // });

        // Membuat 5 akun Karyawan baru menggunakan Factory, lalu memberikan role
        // User::factory()->count(5)->create()->each(function ($user) {
        //     $user->assignRole('karyawan');
        // });
    }
}
