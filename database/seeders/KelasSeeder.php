<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $daftarKelas = [
            'X RPL 1',
            'X RPL 2',
            'X RPL 3',
            'X RPL 4',
            'X RPL 5',
            'X RPL 6',
            'X RPL 7',
            'XI RPL 1',
            'XI RPL 2',
            'XI RPL 3',
            'XI RPL 4',
            'XI RPL 5',
            'XI RPL 6',
            'XI RPL 7',
            'XII RPL 1',
            'XII RPL 2',
            'XII RPL 3',
            'XII RPL 4',
            'XII RPL 5',
            'XII RPL 6',
            'XII RPL 7',
        ];

        foreach ($daftarKelas as $namaKelas) {
            Kelas::firstOrCreate(['nama_kelas' => $namaKelas]);
        }
    }
}
