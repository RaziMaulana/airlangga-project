<x-mail::message>
    # Halo, Orang Tua / Wali

    Kami menginformasikan bahwa anak Anda telah tercatat melakukan absensi hadir di sekolah.

    **Detail Kehadiran:**
    - **Nama Anak:** {{ $siswa->nama_siswa }}
    - **Tanggal:** {{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d F Y') }}
    - **Jam Masuk:** {{ $presensi->waktu }}
    - **Status:** Hadir (IN)

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>
