<div id="editUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl ring-1 ring-black/5 max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 plus-jakarta-sans-bold">
                    Perbarui Data
                </p>
                <h2 class="mt-1 text-xl text-gray-900 plus-jakarta-sans-bold">
                    Edit Pengguna
                </h2>
            </div>

            <button type="button" onclick="closeEditUserModal()"
                class="flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formEditUser" class="p-6">
            @csrf
            @method('PUT')

            <input type="hidden" id="editUserId" name="id">

            {{-- Nama --}}
            <div>
                <label for="edit_name" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                    Nama Lengkap
                </label>

                <input id="edit_name" type="text" name="name" required placeholder="Contoh: Andi Rahman"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
            </div>

            {{-- Email --}}
            <div class="mt-5">
                <label for="edit_email" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                    Email
                </label>

                <input id="edit_email" type="email" name="email" required placeholder="nama@airlangga.sch.id"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
            </div>

            {{-- Role --}}
            <div class="mt-5">
                <label for="edit_role" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                    Role
                </label>

                <select id="edit_role" name="role" required
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">

                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                    <option value="orangtua">Orang Tua</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            {{-- NIP & Kelas — hanya muncul jika role = guru --}}
            <div id="edit_guruFields" class="hidden">

                <div class="mt-5">
                    <label for="edit_nip" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        NIP
                    </label>

                    <input id="edit_nip" type="text" name="nip" placeholder="Nomor Induk Pegawai"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                </div>

                <div class="mt-5">
                    <label class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Kelas yang Diampu
                    </label>

                    <div id="edit_kelasWrapper"
                        class="max-h-40 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3">
                        @forelse($kelasList as $kelas)
                            <label class="flex items-center gap-2 text-sm text-gray-700 plus-jakarta-sans-reguler">
                                <input type="checkbox" name="kelas_ids[]" value="{{ $kelas->id }}"
                                    class="rounded border-gray-300 text-black focus:ring-black">
                                {{ $kelas->nama_kelas }}
                            </label>
                        @empty
                            <p class="text-sm text-gray-400 plus-jakarta-sans-reguler">Belum ada data kelas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ID Siswa & Kelas — hanya muncul jika role = siswa --}}
            <div id="edit_siswaFields" class="hidden">

                <div class="mt-5">
                    <label for="edit_id_siswa"
                        class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        ID Siswa / NISN
                    </label>

                    <input id="edit_id_siswa" type="text" name="id_siswa" placeholder="Contoh: 1234567890"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                </div>

                <div class="mt-5">
                    <label for="edit_kelas_id"
                        class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Kelas
                    </label>

                    <select id="edit_kelas_id" name="kelas_id"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                        <option value="">Pilih kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Data orang tua — hanya muncul jika role = orangtua --}}
            <div id="edit_orangtuaFields" class="hidden">

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="edit_nama_ayah"
                            class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                            Nama Ayah
                        </label>
                        <input id="edit_nama_ayah" type="text" name="nama_ayah" placeholder="Nama ayah"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                    </div>

                    <div>
                        <label for="edit_nama_ibu"
                            class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                            Nama Ibu
                        </label>
                        <input id="edit_nama_ibu" type="text" name="nama_ibu" placeholder="Nama ibu"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                    </div>
                </div>

                <div class="mt-5">
                    <label for="edit_no_hp" class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        No. HP
                    </label>
                    <input id="edit_no_hp" type="text" name="no_hp" placeholder="08xxxxxxxxxx"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
                </div>

                <div class="mt-5">
                    <label class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                        Siswa yang Diakses
                    </label>

                    <div id="edit_siswaAksesWrapper"
                        class="max-h-40 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3">
                        @forelse($siswaList as $siswa)
                            <label class="flex items-center gap-2 text-sm text-gray-700 plus-jakarta-sans-reguler">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id_siswa }}"
                                    class="rounded border-gray-300 text-black focus:ring-black">
                                {{ $siswa->nama_siswa }}
                                @if ($siswa->kelas)
                                    <span class="text-gray-400">({{ $siswa->kelas->nama_kelas }})</span>
                                @endif
                            </label>
                        @empty
                            <p class="text-sm text-gray-400 plus-jakarta-sans-reguler">Belum ada data siswa.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div class="mt-5">
                <label for="edit_password"
                    class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                    Password Baru
                    <span class="font-normal text-gray-400">(Opsional)</span>
                </label>

                <input id="edit_password" type="password" name="password"
                    placeholder="Kosongkan jika tidak ingin diubah"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
            </div>

            {{-- Konfirmasi Password --}}
            <div class="mt-5">
                <label for="edit_password_confirmation"
                    class="mb-2 block text-sm font-medium text-gray-700 plus-jakarta-sans-bold">
                    Konfirmasi Password Baru
                </label>

                <input id="edit_password_confirmation" type="password" name="password_confirmation"
                    placeholder="Ulangi password baru"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 transition duration-200 focus:border-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 plus-jakarta-sans-reguler">
            </div>

            {{-- Footer --}}
            <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">

                <button type="button" onclick="closeEditUserModal()"
                    class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 plus-jakarta-sans-reguler">
                    Batal
                </button>

                <button type="submit"
                    class="rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 plus-jakarta-sans-bold">
                    Simpan Perubahan
                </button>

            </div>
        </form>
    </div>
</div>
