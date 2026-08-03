<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;

class FeedbackFormController extends Controller
{
    public function index()
    {
        return view('feedback-form');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'topik' => 'required|string',
            'pesan' => 'required|string',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // maks 2MB
        ], [
            'judul.required' => 'Judul tidak boleh kosong.',
            'topik.required' => 'Silakan pilih topik.',
            'pesan.required' => 'Isi saran tidak boleh kosong.',
            'bukti.image' => 'File yang diunggah harus berupa gambar.',
            'bukti.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'bukti.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $buktiPath = null;

        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('feedback-bukti', 'public');
        }

        // Simpan ke database
        Feedback::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'topik' => $request->topik,
            'pesan' => $request->pesan,
            'bukti' => $buktiPath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Saran Anda berhasil dikirim! Terima kasih atas masukannya.'
        ]);
    }
}
