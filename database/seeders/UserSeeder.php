<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X RPL 1']
        );

        $userSiswa = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@gmail.com',
            'role' => 'siswa',
            'password' => Hash::make('12345678'),
        ]);

        $siswa = Siswa::create([
            'id_siswa' => (string) Str::uuid(),
            'user_id' => $userSiswa->id,
            'nama_siswa' => 'Siswa',
            'id_kelas' => $kelas->id,
        ]);

        $userOrangTua = User::create([
            'name' => 'Orang Tua',
            'email' => 'orangtua@gmail.com',
            'role' => 'orangtua',
            'password' => Hash::make('12345678'),
        ]);

        $orangTua = OrangTua::create([
            'user_id' => $userOrangTua->id,
            'nama_ayah' => 'Ayah Siswa',
            'nama_ibu' => 'Ibu Siswa',
            'no_hp' => '081234567890',
        ]);

        $orangTua->siswaAkses()->attach($siswa->id_siswa);
    }
}
