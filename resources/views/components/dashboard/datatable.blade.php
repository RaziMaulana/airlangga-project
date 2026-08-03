<div class="rounded-2xl border border-black/10 bg-white p-5 lg:p-8 shadow-sm">

    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                Presensi
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-black plus-jakarta-sans-bold">
                Data Kehadiran
            </h2>
        </div>

        <span id="studentCount"
            class="inline-flex w-fit items-center rounded-full border border-black/10 bg-black/5 px-4 py-1.5 text-xs font-semibold text-black plus-jakarta-sans-reguler">
            Total: {{ $presensis->total() }} siswa
        </span>
    </div>

    <!-- Form Filter Datatable -->
    <form id="filterForm" class="mb-6" onsubmit="event.preventDefault();">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

            <!-- Filter Status -->
            <div class="relative w-full lg:w-44">
                <select id="filterStatus" name="status"
                    class="w-full appearance-none rounded-xl border border-black/10 bg-white py-2.5 pl-4 pr-10 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 cursor-pointer shadow-sm">
                    <option value=""
                        {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Semua Status
                    </option>
                    <option value="IN" {{ request('status') === 'IN' ? 'selected' : '' }}>Masuk (IN)</option>
                    <option value="OUT" {{ request('status') === 'OUT' ? 'selected' : '' }}>Keluar (OUT)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-black/40">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Tanggal Datatable (Single/Range) -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center w-full lg:w-auto">
                <div class="relative w-full sm:w-36">
                    <select id="filterDateMode" name="date_mode"
                        class="w-full appearance-none rounded-xl border border-black/10 bg-white py-2.5 pl-4 pr-9 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 cursor-pointer shadow-sm">
                        <option value="single" {{ request('date_mode', 'single') === 'single' ? 'selected' : '' }}>Satu
                            Hari</option>
                        <option value="range" {{ request('date_mode') === 'range' ? 'selected' : '' }}>Rentang</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-black/40">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Single Date -->
                <div id="singleDateWrapper"
                    class="relative w-full sm:w-auto {{ request('date_mode') === 'range' ? 'hidden' : '' }}">
                    <input type="date" id="filterDate" name="tanggal"
                        value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 sm:w-auto cursor-pointer shadow-sm">
                </div>

                <!-- Range Date -->
                <div id="rangeDateWrapper"
                    class="flex-col gap-2 sm:flex-row sm:items-center w-full sm:w-auto {{ request('date_mode') === 'range' ? 'flex' : 'hidden' }}">
                    <input type="date" id="filterStartDate" name="start_date"
                        value="{{ request('start_date', $tanggal->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 sm:w-auto cursor-pointer shadow-sm">
                    <span class="text-xs text-black/40 text-center hidden sm:inline">-</span>
                    <input type="date" id="filterEndDate" name="end_date"
                        value="{{ request('end_date', $tanggal->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 sm:w-auto cursor-pointer shadow-sm">
                </div>
            </div>

            <!-- Search Input -->
            <div class="relative w-full flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-black/40">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" id="searchInput" name="search" placeholder="Cari nama atau kelas..."
                    value="{{ request('search') }}"
                    class="w-full rounded-xl border border-black/10 bg-white py-2.5 pl-11 pr-4 text-sm text-black transition-colors plus-jakarta-sans-reguler hover:border-black/20 focus:border-black focus:outline-none focus:ring-0 shadow-sm">
            </div>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-black/5">
        <table id="attendanceTable" class="min-w-full text-left text-sm text-black whitespace-nowrap">
            <!-- (Tabel dan isi tr sama persis seperti sebelumnya) -->
            <thead class="bg-[#F5F5F4]/50">
                <tr class="border-b border-black/10">
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        No</th>
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        Nama</th>
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        Kelas</th>
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        Tanggal</th>
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        Jam</th>
                    <th
                        class="px-5 py-4 text-[10px] font-semibold uppercase tracking-wider text-black/50 plus-jakarta-sans-bold">
                        Status</th>
                </tr>
            </thead>

            <tbody id="attendanceTableBody" class="divide-y divide-black/5">
                @forelse ($presensis as $presensi)
                    <tr class="attendance-row transition hover:bg-black/[0.02]"
                        data-nama="{{ strtolower($presensi->siswa->nama_siswa ?? $presensi->uid_tag) }}"
                        data-kelas="{{ strtolower($presensi->kelas->nama_kelas ?? '') }}"
                        data-tanggal="{{ $presensi->tanggal }}">

                        <td class="px-5 py-4 text-black/40 plus-jakarta-sans-reguler">
                            {{ str_pad($presensis->firstItem() + $loop->iteration - 1, 2, '0', STR_PAD_LEFT) }}
                        </td>

                        <td class="px-5 py-4 font-semibold text-black plus-jakarta-sans-bold">
                            {{ $presensi->siswa->nama_siswa ?? $presensi->uid_tag }}
                        </td>

                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler">
                            {{ $presensi->kelas->nama_kelas ?? '-' }}
                        </td>

                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler">
                            {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}
                        </td>

                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler font-medium">
                            {{ $presensi->waktu }}
                        </td>

                        <td class="px-5 py-4">
                            @if ($presensi->status === 'IN')
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-black px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-white plus-jakarta-sans-bold shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>Masuk
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-black/20 bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-black plus-jakarta-sans-bold shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-black"></span>Keluar
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRow">
                        <td colspan="6" class="px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-40"></i>
                                Belum ada data kehadiran.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div id="noSearchResult"
            class="hidden px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler border-t border-black/5">
            Data tidak ditemukan. Coba kata kunci lain.
        </div>
    </div>

    @if ($presensis->hasPages())
        <div class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-black/5 pt-5 sm:flex-row">
            <p class="text-xs text-black/60 plus-jakarta-sans-reguler">
                Menampilkan <span class="font-semibold text-black">{{ $presensis->firstItem() }}</span> – <span
                    class="font-semibold text-black">{{ $presensis->lastItem() }}</span> dari <span
                    class="font-semibold text-black">{{ $presensis->total() }}</span> data
            </p>

            <div class="inline-flex items-center gap-1.5">
                @if ($presensis->onFirstPage())
                    <span
                        class="inline-flex items-center gap-1.5 rounded-xl border border-black/10 bg-black/5 px-3 py-2 text-xs font-medium text-black/40">
                        Prev
                    </span>
                @else
                    <a href="{{ $presensis->previousPageUrl() }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-black/10 bg-white px-3 py-2 text-xs font-medium text-black transition hover:bg-black/5">
                        Prev
                    </a>
                @endif
                <!-- Silahkan tambahkan loop pagination lainnya dari original file datatable -->
            </div>
        </div>
    @endif
</div>
