<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $kelasRpl1 = Kelas::where('nama_kelas', 'X RPL 1')->first();
        $kelasRpl2 = Kelas::where('nama_kelas', 'X RPL 2')->first();
        $kelasRpl3 = Kelas::where('nama_kelas', 'X RPL 3')->first();

        $userGuru = User::where('email', 'guru@gmail.com')->first();

        $guru1 = Guru::create([
            'user_id' => $userGuru?->id,
            'nama_guru' => 'Budi Santoso, S.Kom',
            'nip' => '198501152010011001',
        ]);
        if ($kelasRpl1) {
            $guru1->kelas()->attach($kelasRpl1->id);
        }

        $guru2 = Guru::create([
            'nama_guru' => 'Siti Aminah, S.Pd',
            'nip' => '198803222014022002',
        ]);
        if ($kelasRpl2) {
            $guru2->kelas()->attach($kelasRpl2->id);
        }

        $guru3 = Guru::create([
            'nama_guru' => 'Ahmad Dahlan, M.T',
            'nip' => '199007102018011003',
        ]);
        // Guru pengajar beberapa kelas sekaligus
        $guru3->kelas()->attach(array_filter([
            $kelasRpl2?->id,
            $kelasRpl3?->id,
        ]));
    }
}
