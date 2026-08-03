<div id="tambahUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 overflow-y-auto">
    {{-- Container utama modal --}}
    <div
        class="my-auto w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl transition-all">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 plus-jakarta-sans-bold">
                    Pengguna Baru
                </p>
                <h2 class="mt-1 text-xl text-gray-900 plus-jakarta-sans-bold">
                    Tambah Pengguna
                </h2>
            </div>

            <button type="button" onclick="closeTambahUserModal()"
                class="flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="formTambahUser" method="POST" action="{{ route('access.store') }}" class="p-6 sm:p-8">
            @csrf

            {{-- Form Grid 2 Kolom --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Nama Lengkap
                    </label>

                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Contoh: Andi Rahman"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Email
                    </label>

                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        placeholder="nama@airlangga.sch.id"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="sm:col-span-2">
                    <label for="role" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Role
                    </label>

                    <select id="role" name="role" required
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                            Pilih role
                        </option>

                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>
                            Guru
                        </option>

                        <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>
                            Siswa
                        </option>

                        <option value="orangtua" {{ old('role') == 'orangtua' ? 'selected' : '' }}>
                            Orang Tua
                        </option>

                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>
                            Staff
                        </option>
                    </select>

                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIP & Kelas (Guru) --}}
                <div id="guruFields" class="hidden sm:col-span-2">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="nip"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                NIP
                            </label>

                            <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
                                placeholder="Nomor Induk Pegawai"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                            @error('nip')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                Kelas yang Diampu
                            </label>

                            <div
                                class="max-h-36 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3">
                                @forelse($kelasList as $kelas)
                                    <label
                                        class="flex items-center gap-2 text-sm text-gray-700 plus-jakarta-sans-reguler">
                                        <input type="checkbox" name="kelas_ids[]" value="{{ $kelas->id }}"
                                            class="rounded border-gray-300 text-black focus:ring-black">
                                        {{ $kelas->nama_kelas }}
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-400 plus-jakarta-sans-reguler">Belum ada data kelas.</p>
                                @endforelse
                            </div>

                            @error('kelas_ids')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ID Siswa & Kelas (Siswa) --}}
                <div id="siswaFields" class="hidden sm:col-span-2">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="id_siswa"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                NIS / ID Siswa
                            </label>

                            <input id="id_siswa" type="text" name="id_siswa" value="{{ old('id_siswa') }}"
                                placeholder="Contoh: 2025001"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                            @error('id_siswa')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kelas_id"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                Kelas
                            </label>

                            <select id="kelas_id" name="kelas_id"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                                <option value="" disabled selected>Pilih kelas</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>

                            @error('kelas_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Orang Tua & Anak (Orang Tua) --}}
                <div id="orangtuaFields" class="hidden sm:col-span-2">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="nama_ayah"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                Nama Ayah <span class="font-normal text-gray-400">(opsional)</span>
                            </label>
                            <input id="nama_ayah" type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                placeholder="Nama ayah"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                            @error('nama_ayah')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_ibu"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                Nama Ibu <span class="font-normal text-gray-400">(opsional)</span>
                            </label>
                            <input id="nama_ibu" type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                                placeholder="Nama ibu"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                            @error('nama_ibu')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="no_hp"
                                class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                No. HP <span class="font-normal text-gray-400">(opsional)</span>
                            </label>
                            <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                            @error('no_hp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                                Anak yang Bisa Dipantau
                            </label>
                            <div
                                class="max-h-36 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3">
                                @forelse ($siswaList as $siswa)
                                    <label
                                        class="flex items-center gap-2 text-sm text-gray-700 plus-jakarta-sans-reguler">
                                        <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id_siswa }}"
                                            class="rounded border-gray-300 text-black focus:ring-black">
                                        {{ $siswa->nama_siswa }} @if ($siswa->kelas)
                                            <span class="text-gray-400">({{ $siswa->kelas->nama_kelas }})</span>
                                        @endif
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-400 plus-jakarta-sans-reguler">Belum ada data siswa.
                                    </p>
                                @endforelse
                            </div>
                            @error('siswa_ids')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Password
                    </label>

                    <input id="password" type="password" name="password" required placeholder="Minimal 8 karakter"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation"
                        class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Konfirmasi Password
                    </label>

                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                </div>

            </div>

            {{-- Footer --}}
            <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">
                <button type="button" onclick="closeTambahUserModal()"
                    class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 plus-jakarta-sans-reguler">
                    Batal
                </button>

                <button type="submit"
                    class="rounded-xl bg-black px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 plus-jakarta-sans-bold">
                    Simpan Pengguna
                </button>
            </div>

        </form>

    </div>
</div>
