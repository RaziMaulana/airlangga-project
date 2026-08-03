<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccessController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']);

        $siswaList = Siswa::with('kelas:id,nama_kelas')
            ->orderBy('nama_siswa')
            ->get(['id_siswa', 'nama_siswa', 'id_kelas']);

        return view('admin.access', compact('kelasList', 'siswaList'));
    }

    public function data(Request $request)
    {
        $users = User::query()
            ->with(['guru.kelas:id,nama_kelas', 'siswa.kelas:id,nama_kelas', 'orangTua.siswaAkses:id_siswa,nama_siswa'])
            ->select(['id', 'name', 'email', 'role', 'email_verified_at'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($user, $index) {
                $kelas = [];

                if ($user->role === 'guru' && $user->guru) {
                    $kelas = $user->guru->kelas->pluck('nama_kelas')->all();
                } elseif ($user->role === 'siswa' && $user->siswa && $user->siswa->kelas) {
                    $kelas = [$user->siswa->kelas->nama_kelas];
                } elseif ($user->role === 'orangtua' && $user->orangTua) {
                    $kelas = $user->orangTua->siswaAkses->pluck('nama_siswa')->all();
                }

                return [
                    'no' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'kelas' => $kelas,
                ];
            });

        return response()->json([
            'data' => $users,
        ]);
    }

    public function stats()
    {
        $total = User::count();

        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalOrangTua = User::where('role', 'orangtua')->count();

        return response()->json([
            'total' => $total,
            'siswa' => $totalSiswa,
            'guru' => $totalGuru,
            'orang_tua' => $totalOrangTua,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:admin,guru,siswa,orangtua,staff'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        if ($request->input('role') === 'guru') {
            $rules['nip'] = ['required', 'string', 'max:30', 'unique:gurus,nip'];
            $rules['kelas_ids'] = ['nullable', 'array'];
            $rules['kelas_ids.*'] = ['exists:kelas,id'];
        }

        if ($request->input('role') === 'siswa') {
            $rules['id_siswa'] = ['required', 'string', 'max:20', 'unique:siswas,id_siswa'];
            $rules['kelas_id'] = ['required', 'exists:kelas,id'];
        }

        if ($request->input('role') === 'orangtua') {
            $rules['nama_ayah'] = ['nullable', 'string', 'max:100'];
            $rules['nama_ibu'] = ['nullable', 'string', 'max:100'];
            $rules['no_hp'] = ['nullable', 'string', 'max:20'];
            $rules['siswa_ids'] = ['required', 'array', 'min:1'];
            $rules['siswa_ids.*'] = ['exists:siswas,id_siswa'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        if ($validated['role'] === 'guru') {
            $guru = Guru::create([
                'user_id' => $user->id,
                'nama_guru' => $user->name,
                'nip' => $validated['nip'],
            ]);

            $guru->kelas()->sync($request->input('kelas_ids', []));
        }

        if ($validated['role'] === 'siswa') {
            Siswa::create([
                'id_siswa' => $validated['id_siswa'],
                'user_id' => $user->id,
                'nama_siswa' => $user->name,
                'id_kelas' => $validated['kelas_id'],
                'uid_tag' => null,
            ]);
        }

        if ($validated['role'] === 'orangtua') {
            $orangTua = OrangTua::create([
                'user_id' => $user->id,
                'nama_ayah' => $validated['nama_ayah'] ?? null,
                'nama_ibu' => $validated['nama_ibu'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            $orangTua->siswaAkses()->sync($validated['siswa_ids']);
        }

        return response()->json([
            'message' => 'Pengguna baru berhasil ditambahkan.',
            'user' => $user,
        ], 201);
    }

    public function edit(User $user)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'nip' => null,
            'kelas_ids' => [],
            'id_siswa' => null,
            'kelas_id' => null,
            'nama_ayah' => null,
            'nama_ibu' => null,
            'no_hp' => null,
            'siswa_ids' => [],
        ];

        if ($user->role === 'guru' && $user->guru) {
            $data['nip'] = $user->guru->nip;
            $data['kelas_ids'] = $user->guru->kelas()->pluck('kelas.id');
        }

        if ($user->role === 'siswa' && $user->siswa) {
            $data['id_siswa'] = $user->siswa->id_siswa;
            $data['kelas_id'] = $user->siswa->id_kelas;
        }

        if ($user->role === 'orangtua' && $user->orangTua) {
            $data['nama_ayah'] = $user->orangTua->nama_ayah;
            $data['nama_ibu'] = $user->orangTua->nama_ibu;
            $data['no_hp'] = $user->orangTua->no_hp;
            $data['siswa_ids'] = $user->orangTua->siswaAkses()->pluck('siswas.id_siswa');
        }

        return response()->json([
            'user' => $data,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,guru,siswa,orangtua,staff'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];

        if ($request->input('role') === 'guru') {
            $rules['nip'] = [
                'required',
                'string',
                'max:30',
                Rule::unique('gurus', 'nip')->ignore($user->guru?->id),
            ];
            $rules['kelas_ids'] = ['nullable', 'array'];
            $rules['kelas_ids.*'] = ['exists:kelas,id'];
        }

        if ($request->input('role') === 'siswa') {
            $rules['id_siswa'] = [
                'required',
                'string',
                'max:20',
                Rule::unique('siswas', 'id_siswa')->ignore($user->siswa?->id_siswa, 'id_siswa'),
            ];
            $rules['kelas_id'] = ['required', 'exists:kelas,id'];
        }

        if ($request->input('role') === 'orangtua') {
            $rules['nama_ayah'] = ['nullable', 'string', 'max:100'];
            $rules['nama_ibu'] = ['nullable', 'string', 'max:100'];
            $rules['no_hp'] = ['nullable', 'string', 'max:20'];
            $rules['siswa_ids'] = ['required', 'array', 'min:1'];
            $rules['siswa_ids.*'] = ['exists:siswas,id_siswa'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // ==== Guru ====
        if ($validated['role'] === 'guru') {
            $guru = $user->guru ?? new Guru(['user_id' => $user->id]);
            $guru->nama_guru = $user->name;
            $guru->nip = $validated['nip'];
            $guru->save();

            $guru->kelas()->sync($request->input('kelas_ids', []));
        } elseif ($user->guru) {
            $user->guru->delete();
        }

        // ==== Siswa ====
        if ($validated['role'] === 'siswa') {
            $siswa = $user->siswa ?? new Siswa(['user_id' => $user->id]);
            $siswa->id_siswa = $validated['id_siswa'];
            $siswa->nama_siswa = $user->name;
            $siswa->id_kelas = $validated['kelas_id'];
            $siswa->save();
        } elseif ($user->siswa) {
            $user->siswa->delete();
        }

        // ==== Orang Tua ====
        if ($validated['role'] === 'orangtua') {
            $orangTua = $user->orangTua ?? new OrangTua(['user_id' => $user->id]);
            $orangTua->nama_ayah = $validated['nama_ayah'] ?? null;
            $orangTua->nama_ibu = $validated['nama_ibu'] ?? null;
            $orangTua->no_hp = $validated['no_hp'] ?? null;
            $orangTua->save();

            $orangTua->siswaAkses()->sync($validated['siswa_ids']);
        } elseif ($user->orangTua) {
            $user->orangTua->delete();
        }

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'user' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
