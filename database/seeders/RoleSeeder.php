<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // <-- IMPORT MODEL ROLE

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan firstOrCreate untuk menghindari duplikasi data jika seeder dijalankan lebih dari sekali
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'karyawan', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pengajar', 'guard_name' => 'web']);
    }
}