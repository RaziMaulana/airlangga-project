<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Presensi;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $uid = $request->input('uid');
        $deviceId = $request->input('device_id');

        if (!$uid) {
            return response()->json(['error' => 'Parameter UID tidak ditemukan.'], 400);
        }

        // 1. Verifikasi Siswa & Kelas
        $siswa = Siswa::with('kelas')->where('uid_tag', $uid)->first();

        if (!$siswa || !$siswa->kelas) {
            return response()->json([
                'status'  => 'REJECTED',
                'name'    => 'Unknown',
                'message' => 'Tidak Terdaftar'
            ], 200);
        }

        $namaSiswa = $siswa->nama_siswa;
        $kelasSiswa = $siswa->kelas->nama_kelas;
        $idKelas = $siswa->id_kelas;

        // Normalisasi teks untuk membandingkan device_id dengan nama_kelas
        $deviceClean = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$deviceId));
        $kelasClean  = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$kelasSiswa));

        if ($deviceClean !== $kelasClean) {
            return response()->json([
                'status'  => 'REJECTED',
                'name'    => $namaSiswa,
                'message' => 'Salah Kelas'
            ], 200);
        }

        // 2. Logika Jendela Waktu Absen (WITA / UTC+8)
        $now = Carbon::now('Asia/Makassar'); 
        $currentHour = (int)$now->format('H');
        $currentMinute = (int)$now->format('i');
        $totalMenit = ($currentHour * 60) + $currentMinute;

        $tanggalHariIni = $now->format('Y-m-d');
        $waktuString = $now->format('H:i');

        $responStatus = "";
        $dbStatus = "";
        $hitungDatabase = false;

        $m_06_00 = 6 * 60;
        $m_07_15 = (7 * 60) + 15;
        $m_14_30 = (14 * 60) + 30;
        $m_14_31 = (14 * 60) + 31;
        $m_19_00 = 19 * 60;

        if ($totalMenit >= $m_06_00 && $totalMenit <= $m_14_30) {
            $responStatus = ($totalMenit < $m_07_15) ? "MASUK" : "TERLAMBAT";
            $dbStatus = "IN";
            $hitungDatabase = true;
        } elseif ($totalMenit >= $m_14_31 && $totalMenit < $m_19_00) {
            $responStatus = "KELUAR";
            $dbStatus = "OUT";
            $hitungDatabase = true;
        }

        if (!$hitungDatabase) {
            return response()->json([
                'status' => 'DILUAR_JAM',
                'name'   => $namaSiswa
            ], 200);
        }

        // 3. Cek Data Presensi Hari Ini
        $presensi = Presensi::where('uid_tag', $uid)
            ->where('status', $dbStatus)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        if ($presensi) {
            if ($dbStatus === 'IN') {
                return response()->json([
                    'status' => 'SUDAH_ABSEN',
                    'name'   => $namaSiswa
                ], 200);
            } elseif ($dbStatus === 'OUT') {
                $presensi->update(['waktu' => $waktuString]);
                return response()->json([
                    'status' => $responStatus,
                    'name'   => $namaSiswa
                ], 200);
            }
        } else {
            Presensi::create([
                'uid_tag'  => $uid,
                'id_kelas' => $idKelas,
                'status'   => $dbStatus,
                'waktu'    => $waktuString,
                'tanggal'  => $tanggalHariIni
            ]);

            return response()->json([
                'status' => $responStatus,
                'name'   => $namaSiswa
            ], 200);
        }
    }
}