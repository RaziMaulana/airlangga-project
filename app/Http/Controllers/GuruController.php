<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Ambil data guru & daftar kelas_id yang sah diampu guru yang sedang login.
     */
    private function guruDanKelasnya()
    {
        $guru = auth()->user()->guru;
        abort_if(!$guru, 403, 'Akun Anda belum terdaftar sebagai guru.');

        return [$guru, $guru->kelas()->pluck('kelas.id')];
    }

    /**
     * Dashboard Guru — mengarahkan langsung ke tampilan absensi
     */
    public function index()
    {
        return $this->absensi();
    }

    /**
     * Halaman Absensi Kelas — guru bisa switch antar kelas yang diampunya.
     */
    public function absensi()
    {
        [$guru,] = $this->guruDanKelasnya();

        // Gunakan select() agar Eloquent melakukan mapping 'id' dengan benar
        $kelasList = $guru->kelas()
            ->select('kelas.id', 'nama_kelas')
            ->orderBy('nama_kelas')
            ->get();

        return view('guru.dashboard', compact('kelasList'));
    }

    /**
     * Data presensi (dipanggil AJAX oleh DataTable)
     */
    public function absensiData(Request $request)
    {
        [, $kelasIdsGuru] = $this->guruDanKelasnya();

        $kelasId = $request->input('kelas_id');

        // Keamanan: guru cuma boleh lihat kelas yang memang diampunya
        if ($kelasId && !$kelasIdsGuru->contains((int) $kelasId)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $query = Presensi::query()
            ->with(['siswa:uid_tag,nama_siswa', 'kelas:id,nama_kelas'])
            ->whereIn('id_kelas', $kelasIdsGuru);

        if ($kelasId) {
            $query->where('id_kelas', $kelasId);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->input('tanggal'));
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
                    'kelas' => $p->kelas->nama_kelas ?? '-',
                    'waktu' => $p->waktu,
                    'tanggal' => Carbon::parse($p->tanggal)->format('d/m/Y'),
                    'status' => $p->status,
                ];
            });

        return response()->json(['data' => $presensis]);
    }

    /**
     * Statistik top card
     */
    public function absensiStats(Request $request)
    {
        [, $kelasIdsGuru] = $this->guruDanKelasnya();

        $kelasId = $request->input('kelas_id');
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        if ($kelasId && !$kelasIdsGuru->contains((int) $kelasId)) {
            abort(403);
        }

        $kelasScope = $kelasId ? [(int) $kelasId] : $kelasIdsGuru->all();

        $totalSiswa = Siswa::whereIn('id_kelas', $kelasScope)->count();

        $hadir = Presensi::whereIn('id_kelas', $kelasScope)
            ->where('tanggal', $tanggal)
            ->where('status', 'IN')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $pulang = Presensi::whereIn('id_kelas', $kelasScope)
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
