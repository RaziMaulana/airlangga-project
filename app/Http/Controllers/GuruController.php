<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuruController extends Controller
{
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

        // Perbaikan: tambahkan 'Hadir' ke dalam daftar filter yang diizinkan
        if ($request->filled('status') && in_array($request->input('status'), ['Hadir', 'Sakit', 'Izin'])) {
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
                    'id' => $p->id,
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
            ->where('status', 'Hadir')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $sakit = Presensi::whereIn('id_kelas', $kelasScope)
            ->where('tanggal', $tanggal)
            ->where('status', 'Sakit')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $izin = Presensi::whereIn('id_kelas', $kelasScope)
            ->where('tanggal', $tanggal)
            ->where('status', 'Izin')
            ->distinct('uid_tag')
            ->count('uid_tag');

        // PERBAIKAN TYPO DI SINI (-)
        $belumHadir = max($totalSiswa - ($hadir + $sakit + $izin), 0);

        return response()->json([
            'total_siswa' => $totalSiswa,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'belum_hadir' => $belumHadir,
        ]);
    }

    public function updatePresensi(Request $request, $id)
    {
        [, $kelasIdsGuru] = $this->guruDanKelasnya();

        $presensi = Presensi::findOrFail($id);

        if (!$kelasIdsGuru->contains((int) $presensi->id_kelas)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin',
        ]);

        $presensi->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status kehadiran berhasil diperbarui.'
        ]);
    }
}
