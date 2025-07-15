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
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat User Atasan
        User::create([
            'name' => 'Atasan User',
            'email' => 'atasan@example.com',
            'password' => Hash::make('password'),
            'role' => 'atasan',
        ]);

        // Membuat User Karyawan
        User::create([
            'name' => 'Karyawan User',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);
    }
}