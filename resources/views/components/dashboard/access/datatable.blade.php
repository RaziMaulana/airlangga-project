<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
            Daftar pengguna
        </p>
        <h2 class="mt-1 text-xl font-semibold text-black plus-jakarta-sans-bold">
            Semua Akun
        </h2>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        {{-- Filter Role --}}
        <div class="relative">
            <select id="filterRole"
                class="w-full appearance-none rounded-full border border-black/10 bg-[#F5F5F4] px-4 py-2.5 pr-9 text-sm text-black/70 focus:border-black focus:outline-none plus-jakarta-sans-reguler sm:w-44 [appearance:none] [-webkit-appearance:none] [-moz-appearance:none]">
                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
                <option value="orangtua">Orang Tua</option>
                <option value="staff">Staff</option>
            </select>
            <i
                class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-black/30"></i>
        </div>

        {{-- Search --}}
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-xs text-black/30"></i>
            <input type="text" id="searchInput" placeholder="Cari nama atau email..."
                class="w-full rounded-full border border-black/10 bg-[#F5F5F4] py-2.5 pl-10 pr-4 text-sm text-black placeholder:text-black/30 focus:border-black focus:outline-none plus-jakarta-sans-reguler sm:w-64">
        </div>
    </div>
</div>

<div class="overflow-x-auto">
    <table id="accessTable" class="min-w-full text-left text-sm text-black">
        <thead>
            <tr class="border-b border-black/10">
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    No
                </th>
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    Nama
                </th>
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    Email
                </th>
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    Role
                </th>
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    Kelas
                </th>
                <th
                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                    Aksi
                </th>
            </tr>
        </thead>
        {{-- diisi otomatis oleh DataTables lewat AJAX --}}
        <tbody></tbody>
    </table>
</div>
