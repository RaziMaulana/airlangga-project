<?php

namespace Database\Seeders;

use App\Models\Presensi;
use App\Models\siswa;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $siswas = siswa::select('uid_tag', 'id_kelas')->get();
        $dataPresensi = [];

        // Generate data presensi untuk 14 hari terakhir sampai hari ini
        $jumlahHari = 14;
        $today = Carbon::today();

        for ($i = $jumlahHari - 1; $i >= 0; $i--) {
            $tanggalObj = $today->copy()->subDays($i);

            // Lewati hari Sabtu dan Minggu (hari libur)
            if ($tanggalObj->isWeekend()) {
                continue;
            }

            $tanggalString = $tanggalObj->toDateString();

            foreach ($siswas as $siswa) {
                // Simulasi 95% siswa hadir (5% tidak tap / alfa)
                if (rand(1, 100) > 95) {
                    continue;
                }

                // Jam tap acak antara 06:30 - 07:30
                $menitTap = rand(30, 90);
                $waktuTap = date('H:i:s', mktime(6, $menitTap, rand(0, 59)));

                // Lewat jam 07:00 (menit > 60) dianggap terlambat (OUT)
                $status = ($menitTap > 60) ? 'OUT' : 'IN';

                $dataPresensi[] = [
                    'uid_tag' => $siswa->uid_tag,
                    'id_kelas' => $siswa->id_kelas,
                    'waktu' => $waktuTap,
                    'tanggal' => $tanggalString,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert sekaligus secara bertahap (per 500 baris) agar cepat
        foreach (array_chunk($dataPresensi, 500) as $chunk) {
            Presensi::insert($chunk);
        }
    }
}
