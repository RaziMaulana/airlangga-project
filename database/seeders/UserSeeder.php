<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
        ]);

        User::create([
            'name' => 'Guru',
            'email' => 'guru@gmail.com',
            'role' => 'guru',
            'password' => Hash::make('12345678'),
        ]);

        User::create([
            'name' => 'Siswa',
            'email' => 'siswa@gmail.com',
            'role' => 'siswa',
            'password' => Hash::make('12345678'),
        ]);

        User::create([
            'name' => 'Orang Tua',
            'email' => 'orangtua@gmail.com',
            'role' => 'orangtua',
            'password' => Hash::make('12345678'),
        ]);
    }
}
