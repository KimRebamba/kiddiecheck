<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'profile_path' => '/storage/public/admin.jpg',
        ]);

        // Family account
        User::create([
            'name' => 'Family One',
            'email' => 'family1@example.com',
            'password' => Hash::make('password'),
            'role' => 'family',
            'status' => 'active',
            'profile_path' => '/storage/public/family1.jpg',
        ]);

        // Teacher account
        User::create([
            'name' => 'Teacher One',
            'email' => 'teacher1@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
            'profile_path' => '/storage/public/teacher1.jpg',
        ]);
    }
}
