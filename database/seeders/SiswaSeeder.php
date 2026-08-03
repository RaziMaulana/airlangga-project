<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $kelasIds = Kelas::pluck('id')->toArray();
        $totalSiswa = 675;
        $dataSiswa = [];
        $usedNames = [];

        for ($i = 1; $i <= $totalSiswa; $i++) {
            // Membagi siswa secara merata ke 21 kelas
            $idKelas = $kelasIds[($i - 1) % count($kelasIds)];

            // Memastikan nama siswa unik sesuai constraint migration
            do {
                $nama = $faker->name();
            } while (isset($usedNames[$nama]));
            $usedNames[$nama] = true;

            $dataSiswa[] = [
                'id_siswa' => sprintf('SISWA-%04d', $i),
                'uid_tag' => sprintf('TAG-%05d', $i), // Format: TAG-00001 s/d TAG-00675
                'nama_siswa' => $nama,
                'id_kelas' => $idKelas,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert sekaligus per 100 baris agar proses cepat
        foreach (array_chunk($dataSiswa, 100) as $chunk) {
            siswa::insert($chunk);
        }
    }
}
