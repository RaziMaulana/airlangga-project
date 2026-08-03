<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tingkat = ['X', 'XI', 'XII'];
        $dataKelas = [];

        foreach ($tingkat as $t) {
            for ($i = 1; $i <= 7; $i++) { // 3 tingkat x 7 kelas = 21 kelas
                $dataKelas[] = [
                    'nama_kelas' => "{$t} RPL {$i}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Kelas::insert($dataKelas);
    }
}
