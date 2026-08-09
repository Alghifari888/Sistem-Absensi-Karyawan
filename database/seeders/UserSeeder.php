<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat User Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin1234!'),
            'role' => 'admin',
        ]);

        // Membuat User Atasan
        User::create([
            'name' => 'Atasan User',
            'email' => 'atasan@gmail.com',
            'password' => Hash::make('Atasan1234!'),
            'role' => 'atasan',
        ]);

        // Membuat User Karyawan
        User::create([
            'name' => 'Karyawan User',
            'email' => 'karyawan@gmail.com',
            'password' => Hash::make('Karyawan1234!'),
            'role' => 'karyawan',
        ]);
    }
}