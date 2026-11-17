@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F5F5F4] p-6 lg:p-10">

        <!-- Header -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                    Halo, {{ Auth::user()->name ?? 'Siswa' }}
                </p>
                <h1 class="mt-1 font-serif text-4xl text-black">
                    Absensi Kelas
                </h1>
                <p class="mt-1 text-sm text-black/50 plus-jakarta-sans-reguler">
                    Kelas {{ $namaKelas }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('feedback-form.index') }}"
                    class="flex items-center gap-2 rounded-full border border-black/20 bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-black hover:text-white plus-jakarta-sans-bold">
                    <i class="fa-regular fa-comment-dots text-sm"></i>
                    Feedback
                </a>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 rounded-full border border-black/20 bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-black hover:text-white plus-jakarta-sans-bold">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Top card (5 Kolom Statistik) -->
        <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-5">

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                    Total Siswa
                </p>
                <h2 id="statTotal" class="mt-3 font-serif text-3xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Di kelas ini</p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-black"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                        Hadir
                    </p>
                </div>
                <h2 id="statHadir" class="mt-3 font-serif text-3xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                        Sakit
                    </p>
                </div>
                <h2 id="statSakit" class="mt-3 font-serif text-3xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                        Izin
                    </p>
                </div>
                <h2 id="statIzin" class="mt-3 font-serif text-3xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-black/20"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                        Belum Absen
                    </p>
                </div>
                <h2 id="statBelumHadir" class="mt-3 font-serif text-3xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
            </div>

        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm sm:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                        Presensi
                    </p>
                    <h2 class="mt-1 text-xl font-semibold text-black plus-jakarta-sans-bold">
                        Data Kehadiran Kelas
                    </h2>
                </div>
            </div>

            <!-- Filter & Search -->
            <div
                class="mb-6 flex flex-col gap-3 border-y border-black/10 py-4 lg:flex-row lg:items-center lg:justify-between">

                <div class="flex flex-1 flex-wrap items-center gap-3">
                    {{-- Filter tanggal --}}
                    <div class="relative">
                        <i
                            class="fa-regular fa-calendar pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                        <input type="date" id="filterTanggal" value="{{ now()->format('Y-m-d') }}"
                            class="rounded-full border border-black/15 bg-white py-2 pl-9 pr-4 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                    </div>

                    {{-- Filter status lengkap --}}
                    <div class="flex items-center gap-1 rounded-full border border-black/15 p-1">
                        <button type="button" data-filter=""
                            class="filter-status rounded-full bg-black px-4 py-1.5 text-xs font-semibold text-white transition plus-jakarta-sans-bold">
                            Semua
                        </button>
                        <button type="button" data-filter="Hadir"
                            class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                            Hadir
                        </button>
                        <button type="button" data-filter="Sakit"
                            class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                            Sakit
                        </button>
                        <button type="button" data-filter="Izin"
                            class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                            Izin
                        </button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="relative w-full lg:w-64">
                    <i
                        class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama siswa..."
                        class="w-full rounded-full border border-black/15 bg-white py-2 pl-9 pr-4 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-black">
                    <thead>
                        <tr class="border-b border-black/10">
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                No</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Nama Siswa</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Tanggal</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Jam</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data dimuat lewat JS -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="hidden flex-col items-center justify-center p-8 text-center">
                <i class="fa-regular fa-folder-open mb-3 text-3xl text-black/20"></i>
                <p class="font-semibold text-black plus-jakarta-sans-bold">Tidak ada data ditemukan</p>
                <p class="mt-1 text-sm text-black/50 plus-jakarta-sans-reguler">Belum ada data kehadiran untuk filter yang
                    dipilih.</p>
            </div>

            <!-- Pagination Container -->
            <div id="paginationNav"></div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            const perPage = 10;
            let tableData = [];

            const filterTanggal = document.getElementById('filterTanggal');
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('tableBody');
            const emptyState = document.getElementById('emptyState');
            const paginationNav = document.getElementById('paginationNav');

            function loadStats() {
                $.getJSON("{{ route('siswa.absensi.stats') }}", {
                    tanggal: filterTanggal.value
                }, function(res) {
                    $('#statTotal').text(res.total_siswa);
                    $('#statHadir').text(res.hadir);
                    $('#statSakit').text(res.sakit);
                    $('#statIzin').text(res.izin);
                    $('#statBelumHadir').text(res.belum_hadir);
                });
            }

            function fetchTableData() {
                const statusFilter = document.querySelector('.filter-status.bg-black')?.dataset.filter || '';

                $.getJSON("{{ route('siswa.absensi.data') }}", {
                    tanggal: filterTanggal.value,
                    status: statusFilter,
                    search: searchInput.value
                }, function(res) {
                    tableData = res.data || [];
                    currentPage = 1;
                    renderTable();
                    loadStats();
                });
            }

            function renderTable() {
                const totalData = tableData.length;
                const lastPage = Math.ceil(totalData / perPage) || 1;
                if (currentPage > lastPage) currentPage = lastPage;

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const paginatedData = tableData.slice(start, end);

                let html = '';
                if (paginatedData.length === 0) {
                    tableBody.innerHTML = '';
                    emptyState.classList.remove('hidden');
                    emptyState.classList.add('flex');
                    paginationNav.innerHTML = '';
                    return;
                }

                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');

                paginatedData.forEach((row) => {
                    const initials = row.nama.split(' ').map(w => w[0]).join('').substring(0, 2)
                        .toUpperCase();
                    let statusBadge = '';
                    if (row.status === 'Hadir') {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-black px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-white plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Hadir</span>`;
                    } else if (row.status === 'Sakit') {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Sakit</span>`;
                    } else {
                        statusBadge =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Izin</span>`;
                    }

                    html += `
                        <tr class="border-b border-black/5 hover:bg-black/[0.02] transition">
                            <td class="px-6 py-4 text-black/40 plus-jakarta-sans-reguler">${row.no}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-xs font-semibold text-white plus-jakarta-sans-bold">${initials}</span>
                                    <span class="font-medium plus-jakarta-sans-bold text-black">${row.nama}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-black/60 plus-jakarta-sans-reguler">${row.tanggal}</td>
                            <td class="px-6 py-4 text-black/60 plus-jakarta-sans-reguler font-medium">${row.waktu}</td>
                            <td class="px-6 py-4">${statusBadge}</td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
                renderPagination(totalData, start + 1, Math.min(end, totalData), lastPage);
            }

            function renderPagination(total, from, to, lastPage) {
                if (!paginationNav) return;
                if (total <= perPage) {
                    paginationNav.innerHTML = '';
                    return;
                }

                let html = `
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-t border-black/5 pt-5">
                    <p class="text-xs text-black/60 plus-jakarta-sans-reguler text-center sm:text-left">
                        Menampilkan <span class="font-semibold text-black">${from}</span> – <span class="font-semibold text-black">${to}</span> dari <span class="font-semibold text-black">${total}</span> data
                    </p>
                    <div class="flex items-center justify-center sm:justify-end gap-1.5 flex-wrap">
                        <button data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}
                            class="pagination-btn inline-flex h-9 items-center justify-center rounded-xl border border-black/10 bg-white px-3 text-xs font-medium text-black transition hover:bg-black/5 plus-jakarta-sans-reguler disabled:opacity-40 disabled:cursor-not-allowed shadow-sm cursor-pointer">
                            Prev
                        </button>`;

                const range = [];
                const rangeWithDots = [];
                let l;

                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        range.push(i);
                    }
                }

                range.forEach(i => {
                    if (l) {
                        if (i - l === 2) {
                            rangeWithDots.push(l + 1);
                        } else if (i - l !== 1) {
                            rangeWithDots.push('...');
                        }
                    }
                    rangeWithDots.push(i);
                    l = i;
                });

                rangeWithDots.forEach(page => {
                    if (page === '...') {
                        html +=
                            `<span class="inline-flex h-9 w-9 items-center justify-center text-xs text-black/40">...</span>`;
                    } else {
                        const active = page === currentPage;
                        html += `
                        <button data-page="${page}"
                            class="pagination-btn h-9 min-w-[2.25rem] inline-flex items-center justify-center rounded-xl px-3 text-xs transition-colors plus-jakarta-sans-reguler ${active ? 'border border-black bg-black text-white font-semibold shadow-sm' : 'border border-black/10 bg-white text-black hover:bg-black/5 shadow-sm'} cursor-pointer">
                            ${page}
                        </button>`;
                    }
                });

                html += `
                        <button data-page="${currentPage + 1}" ${currentPage === lastPage ? 'disabled' : ''}
                            class="pagination-btn inline-flex h-9 items-center justify-center rounded-xl border border-black/10 bg-white px-3 text-xs font-medium text-black transition hover:bg-black/5 plus-jakarta-sans-reguler disabled:opacity-40 disabled:cursor-not-allowed shadow-sm cursor-pointer">
                            Next
                        </button>
                    </div>
                </div>`;

                paginationNav.innerHTML = html;

                paginationNav.querySelectorAll('.pagination-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        currentPage = parseInt(this.dataset.page, 10);
                        renderTable();
                    });
                });
            }

            filterTanggal?.addEventListener('change', fetchTableData);

            let searchTimeout;
            searchInput?.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(fetchTableData, 300);
            });

            document.querySelectorAll('.filter-status').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-status').forEach(b => b.classList.remove(
                        'bg-black', 'text-white'));
                    document.querySelectorAll('.filter-status').forEach(b => b.classList.add(
                        'text-black/60'));
                    this.classList.add('bg-black', 'text-white');
                    this.classList.remove('text-black/60');
                    fetchTableData();
                });
            });

            fetchTableData();
        });
    </script>
@endsection
