<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'admin12345',
            'role' => 'Admin',
        ]);

        // Chủ trọ
        User::create([
            'name' => 'Landlord',
            'email' => 'Landlord@example.com',
            'password' => Hash::make('password'),
            'role' => 'Landlord',
        ]);

        // 3 Nhân viên
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => 'Staff ' . $i,
                'email' => 'Staff' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'Staff',
            ]);
        }

        // 3 Quản lý
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => 'Manager ' . $i,
                'email' => 'Manager' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'Manager',
            ]);
        }

        // 3 User thường
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => 'Renter ' . $i,
                'email' => 'Renter' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'Renter',
            ]);
        }
    }
}

