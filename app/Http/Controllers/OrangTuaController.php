<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    /**
     * Ambil data OrangTua & daftar anak (siswa) yang boleh dipantau akun ini.
     */
    private function orangTuaSaya()
    {
        $orangTua = auth()->user()->orangTua;
        abort_if(!$orangTua, 403, 'Akun Anda belum terdaftar sebagai orang tua.');

        return $orangTua;
    }

    public function index()
    {
        $orangTua = $this->orangTuaSaya();

        $anakList = $orangTua->siswaAkses()
            ->with('kelas:id,nama_kelas')
            ->orderBy('nama_siswa')
            ->get(['siswas.id_siswa', 'siswas.nama_siswa', 'siswas.id_kelas', 'siswas.uid_tag']);

        abort_if($anakList->isEmpty(), 403, 'Belum ada anak yang terhubung ke akun Anda. Hubungi admin.');

        return view('orang-tua.dashboard', compact('anakList'));
    }

    /**
     * Pastikan id_siswa yang diminta memang anak dari orang tua yang login.
     */
    private function anakValid(string $idSiswa)
    {
        $orangTua = $this->orangTuaSaya();

        $anak = $orangTua->siswaAkses()->where('siswas.id_siswa', $idSiswa)->first();
        abort_if(!$anak, 403, 'Anda tidak memiliki akses ke data siswa ini.');

        return $anak;
    }

    /**
     * Riwayat kehadiran per hari untuk anak terpilih (jam masuk = tap IN pertama, jam pulang = tap OUT terakhir).
     */
    public function absensiData(Request $request)
    {
        $idSiswa = $request->input('id_siswa');
        abort_if(!$idSiswa, 422, 'Anak belum dipilih.');

        $anak = $this->anakValid($idSiswa);

        if (!$anak->uid_tag) {
            return response()->json(['data' => []]);
        }

        $rows = Presensi::where('uid_tag', $anak->uid_tag)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $perHari = $rows->groupBy('tanggal')->map(function ($items, $tanggal) {
            $in = $items->where('status', 'IN')->sortBy('waktu')->first();
            $out = $items->where('status', 'OUT')->sortByDesc('waktu')->first();

            return [
                'tanggal_raw' => $tanggal,
                'jam_masuk' => $in->waktu ?? null,
                'jam_pulang' => $out->waktu ?? null,
                'status' => $out ? 'Hadir' : ($in ? 'Belum Pulang' : 'Alpa'),
            ];
        })->values();

        if ($request->filled('bulan')) {
            $bulan = (int) $request->input('bulan');
            $perHari = $perHari->filter(fn($r) => Carbon::parse($r['tanggal_raw'])->month === $bulan)->values();
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $perHari = $perHari->where('status', $request->input('status'))->values();
        }

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $perHari = $perHari->filter(function ($r) use ($search) {
                return str_contains(strtolower(Carbon::parse($r['tanggal_raw'])->translatedFormat('d F Y')), $search);
            })->values();
        }

        $data = $perHari->sortByDesc('tanggal_raw')->values()->map(function ($r, $i) {
            return [
                'no' => $i + 1,
                'tanggal' => Carbon::parse($r['tanggal_raw'])->translatedFormat('d F Y'),
                'jam_masuk' => $r['jam_masuk'] ?? '-',
                'jam_pulang' => $r['jam_pulang'] ?? '-',
                'status' => $r['status'],
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Status hari ini + ringkasan bulan berjalan untuk anak terpilih.
     */
    public function absensiStats(Request $request)
    {
        $idSiswa = $request->input('id_siswa');
        abort_if(!$idSiswa, 422, 'Anak belum dipilih.');

        $anak = $this->anakValid($idSiswa);
        $bulan = (int) $request->input('bulan', now()->month);

        if (!$anak->uid_tag) {
            return response()->json([
                'jam_masuk_hari_ini' => null,
                'status_hari_ini' => 'Belum Ada Data',
                'total_hadir' => 0,
                'belum_pulang' => 0,
            ]);
        }

        $hariIni = now()->format('Y-m-d');
        $presensiHariIni = Presensi::where('uid_tag', $anak->uid_tag)->where('tanggal', $hariIni)->get();
        $inHariIni = $presensiHariIni->where('status', 'IN')->sortBy('waktu')->first();

        $rows = Presensi::where('uid_tag', $anak->uid_tag)->get();
        $perHari = $rows->groupBy('tanggal')->filter(function ($items, $tanggal) use ($bulan) {
            return Carbon::parse($tanggal)->month === $bulan;
        });

        return response()->json([
            'jam_masuk_hari_ini' => $inHariIni->waktu ?? null,
            'status_hari_ini' => $inHariIni ? 'Hadir' : 'Belum Hadir',
            'total_hadir' => $perHari->count(),
            'belum_pulang' => $perHari->filter(fn($items) => $items->where('status', 'OUT')->isEmpty())->count(),
        ]);
    }
}
