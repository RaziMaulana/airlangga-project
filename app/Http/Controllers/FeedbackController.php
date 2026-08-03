<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->get();

        // Label topik
        $labelTopik = [
            'absensi' => 'Sistem Absensi',
            'device' => 'Device / Perangkat',
            'tampilan' => 'Tampilan & Kemudahan Penggunaan',
            'notifikasi' => 'Notifikasi',
            'lainnya' => 'Lainnya',
        ];

        // Hitung jumlah feedback per topik
        $topik = $feedbacks
            ->groupBy('topik')
            ->map(fn($items) => $items->count())
            ->sortDesc();

        // Default jika belum ada feedback
        $namaTopik = 'Belum ada';
        $jumlahTopik = 0;

        if ($topik->isNotEmpty()) {
            $key = $topik->keys()->first();

            $namaTopik = $labelTopik[$key] ?? ucfirst($key);
            $jumlahTopik = $topik->first();
        }

        return view('admin.feedback', compact(
            'feedbacks',
            'namaTopik',
            'jumlahTopik'
        ));
    }
}
