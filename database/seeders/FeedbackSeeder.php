<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        Feedback::truncate();

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $feedbacks = [
            [
                'judul' => 'Absensi terlalu lambat',
                'topik' => 'Sistem Absensi',
                'pesan' => 'Proses absensi membutuhkan waktu cukup lama ketika banyak siswa melakukan scan secara bersamaan.',
            ],
            [
                'judul' => 'Scanner RFID sering gagal',
                'topik' => 'Device / Perangkat',
                'pesan' => 'Perangkat RFID terkadang tidak membaca kartu meskipun sudah ditempel beberapa kali.',
            ],
            [
                'judul' => 'Tampilan dashboard terlalu ramai',
                'topik' => 'Tampilan & Kemudahan Penggunaan',
                'pesan' => 'Mohon dibuat tampilan dashboard yang lebih sederhana agar lebih mudah dipahami.',
            ],
            [
                'judul' => 'Notifikasi terlambat diterima',
                'topik' => 'Notifikasi',
                'pesan' => 'Notifikasi ke orang tua sering muncul beberapa menit setelah siswa melakukan absensi.',
            ],
            [
                'judul' => 'Tambahkan mode gelap',
                'topik' => 'Tampilan & Kemudahan Penggunaan',
                'pesan' => 'Akan lebih nyaman jika aplikasi memiliki fitur dark mode.',
            ],
            [
                'judul' => 'Data absensi belum sinkron',
                'topik' => 'Sistem Absensi',
                'pesan' => 'Data absensi terkadang belum langsung muncul pada dashboard admin.',
            ],
            [
                'judul' => 'Speaker notifikasi kurang jelas',
                'topik' => 'Device / Perangkat',
                'pesan' => 'Suara speaker terlalu kecil ketika absensi berhasil dilakukan.',
            ],
            [
                'judul' => 'Perlu riwayat notifikasi',
                'topik' => 'Notifikasi',
                'pesan' => 'Sebaiknya tersedia halaman riwayat notifikasi yang pernah dikirim.',
            ],
            [
                'judul' => 'Tambah filter laporan',
                'topik' => 'Lainnya',
                'pesan' => 'Laporan absensi akan lebih mudah digunakan jika tersedia filter berdasarkan kelas.',
            ],
            [
                'judul' => 'Menu terlalu kecil',
                'topik' => 'Tampilan & Kemudahan Penggunaan',
                'pesan' => 'Ukuran tombol pada menu navigasi sebaiknya dibuat lebih besar.',
            ],
            [
                'judul' => 'RFID kadang terputus',
                'topik' => 'Device / Perangkat',
                'pesan' => 'Koneksi RFID beberapa kali terputus sehingga siswa harus mengulang scan.',
            ],
            [
                'judul' => 'Tambahkan export PDF',
                'topik' => 'Lainnya',
                'pesan' => 'Mohon ditambahkan fitur export laporan absensi ke format PDF.',
            ],
            [
                'judul' => 'Absensi otomatis logout',
                'topik' => 'Sistem Absensi',
                'pesan' => 'Kadang aplikasi logout sendiri setelah digunakan beberapa saat.',
            ],
            [
                'judul' => 'Notifikasi WhatsApp',
                'topik' => 'Notifikasi',
                'pesan' => 'Akan lebih baik jika tersedia pilihan notifikasi melalui WhatsApp.',
            ],
            [
                'judul' => 'Navigasi membingungkan',
                'topik' => 'Tampilan & Kemudahan Penggunaan',
                'pesan' => 'Beberapa menu sulit ditemukan oleh pengguna baru.',
            ],
        ];

        foreach ($feedbacks as $item) {
            Feedback::create([
                'user_id' => $users->random()->id,
                'judul' => $item['judul'],
                'topik' => $item['topik'],
                'pesan' => $item['pesan'],
                'status' => collect(['Baru', 'Ditinjau', 'Selesai'])->random(),
                'bukti' => null,
            ]);
        }
    }
}
