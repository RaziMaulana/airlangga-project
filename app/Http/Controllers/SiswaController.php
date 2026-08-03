<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    private function siswaSaya()
    {
        $siswa = auth()->user()->siswa;
        abort_if(!$siswa, 403, 'Akun Anda belum terdaftar sebagai siswa.');

        return $siswa;
    }

    public function index()
    {
        $siswa = $this->siswaSaya();
        $namaKelas = $siswa->kelas->nama_kelas ?? '-';

        return view('siswa.dashboard', compact('namaKelas'));
    }

    public function absensi()
    {
        $siswa = $this->siswaSaya();
        $namaKelas = $siswa->kelas->nama_kelas ?? '-';

        return view('siswa.absensi', compact('namaKelas'));
    }

    /**
     * Data presensi seluruh kelas milik siswa ini (bukan cuma dirinya sendiri).
     */
    public function absensiData(Request $request)
    {
        $siswa = $this->siswaSaya();

        if (!$siswa->id_kelas) {
            return response()->json(['data' => []]);
        }

        $query = Presensi::with('siswa:uid_tag,nama_siswa')
            ->where('id_kelas', $siswa->id_kelas);

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->input('tanggal'));
        }

        if ($request->filled('bulan')) {
            $bulan = str_pad($request->input('bulan'), 2, '0', STR_PAD_LEFT);
            $query->whereRaw("to_char(tanggal::date, 'MM') = ?", [$bulan]);
        }

        if ($request->filled('status') && in_array($request->input('status'), ['IN', 'OUT'])) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%");
            });
        }

        $presensis = $query->orderByDesc('tanggal')->orderByDesc('waktu')
            ->get()
            ->values()
            ->map(function ($p, $i) {
                return [
                    'no' => $i + 1,
                    'nama' => $p->siswa->nama_siswa ?? $p->uid_tag,
                    'tanggal' => Carbon::parse($p->tanggal)->translatedFormat('d F Y'),
                    'waktu' => $p->waktu,
                    'status' => $p->status,
                ];
            });

        return response()->json(['data' => $presensis]);
    }

    /**
     * Statistik ringkas untuk kelas ini (hari ini).
     */
    public function absensiStats(Request $request)
    {
        $siswa = $this->siswaSaya();

        if (!$siswa->id_kelas) {
            return response()->json(['total_siswa' => 0, 'hadir' => 0, 'pulang' => 0, 'belum_hadir' => 0]);
        }

        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        $totalSiswa = Siswa::where('id_kelas', $siswa->id_kelas)->count();

        $hadir = Presensi::where('id_kelas', $siswa->id_kelas)
            ->where('tanggal', $tanggal)
            ->where('status', 'IN')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $pulang = Presensi::where('id_kelas', $siswa->id_kelas)
            ->where('tanggal', $tanggal)
            ->where('status', 'OUT')
            ->distinct('uid_tag')
            ->count('uid_tag');

        return response()->json([
            'total_siswa' => $totalSiswa,
            'hadir' => $hadir,
            'pulang' => $pulang,
            'belum_hadir' => max($totalSiswa - $hadir, 0),
        ]);
    }
}
