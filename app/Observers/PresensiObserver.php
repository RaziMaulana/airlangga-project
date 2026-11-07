<?php

namespace App\Observers;

use App\Models\Presensi;
use App\Models\siswa as Siswa;
use App\Mail\NotifikasiAbsensiAnak;
use Illuminate\Support\Facades\Mail;

class PresensiObserver
{
    public function created(Presensi $presensi): void
    {
        // Hanya jalankan jika statusnya 'IN' (Hadir)
        if ($presensi->status === 'IN') {
            $siswa = Siswa::with(['orangTua.user'])
                ->where('uid_tag', $presensi->uid_tag)
                ->first();

            if ($siswa && $siswa->orangTua) {
                foreach ($siswa->orangTua as $orangTua) {
                    if ($orangTua->user && $orangTua->user->email) {
                        Mail::to($orangTua->user->email)->queue(new NotifikasiAbsensiAnak($presensi, $siswa));
                    }
                }
            }
        }
    }
}
