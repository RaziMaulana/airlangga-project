@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F5F5F4] p-6 lg:p-10">

        <!-- Header -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                    Halo, {{ Auth::user()->name ?? 'Guru' }}
                </p>
                <h1 class="mt-1 font-serif text-4xl text-black">
                    Absensi Kelas
                </h1>
                <p class="mt-1 text-sm text-black/50 plus-jakarta-sans-reguler">
                    Pantau kehadiran siswa di semua kelas yang kamu ampu
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

        {{-- Jika guru belum diberi kelas sama sekali --}}
        @if ($kelasList->isEmpty())
            <div class="rounded-2xl border border-black/10 bg-white p-8 text-center shadow-sm">
                <i class="fa-regular fa-folder-open mb-3 text-3xl text-black/20"></i>
                <p class="font-semibold text-black plus-jakarta-sans-bold">Belum ada kelas yang diampu</p>
                <p class="mt-1 text-sm text-black/50 plus-jakarta-sans-reguler">
                    Hubungi admin untuk mendapatkan akses ke kelas.
                </p>
            </div>
        @else
            <!-- Top card -->
            <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">

                <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                        Total Siswa
                    </p>
                    <h2 id="statTotal" class="mt-3 font-serif text-4xl text-black">0</h2>
                    <p id="statTotalKelas" class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Di
                        {{ $kelasList->count() }} kelas</p>
                </div>

                <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-black"></span>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                            Sudah Masuk
                        </p>
                    </div>
                    <h2 id="statHadir" class="mt-3 font-serif text-4xl text-black">0</h2>
                    <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
                </div>

                <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full border border-black/40"></span>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                            Sudah Pulang
                        </p>
                    </div>
                    <h2 id="statPulang" class="mt-3 font-serif text-4xl text-black">0</h2>
                    <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Hari ini</p>
                </div>

                <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-black/20"></span>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">
                            Belum Tap-In
                        </p>
                    </div>
                    <h2 id="statBelumHadir" class="mt-3 font-serif text-4xl text-black">0</h2>
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
                            Data Kehadiran
                        </h2>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div
                    class="mb-6 flex flex-col gap-3 border-y border-black/10 py-4 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex flex-1 flex-wrap items-center gap-3">
                        {{-- Switch Kelas — hanya kelas milik guru ini --}}
                        <div class="relative">
                            <i
                                class="fa-solid fa-chalkboard-user pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                            <select id="filterKelas"
                                class="appearance-none rounded-full border border-black/15 bg-white py-2 pl-9 pr-8 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                                <option value="">Semua Kelas Saya</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter tanggal --}}
                        <div class="relative">
                            <i
                                class="fa-regular fa-calendar pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                            <input type="date" id="filterTanggal" value="{{ now()->format('Y-m-d') }}"
                                class="rounded-full border border-black/15 bg-white py-2 pl-9 pr-4 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                        </div>

                        {{-- Filter status --}}
                        <div class="flex items-center gap-2 rounded-full border border-black/15 p-1">
                            <button type="button" data-filter=""
                                class="filter-status rounded-full bg-black px-4 py-1.5 text-xs font-semibold text-white transition plus-jakarta-sans-bold">
                                Semua
                            </button>
                            <button type="button" data-filter="IN"
                                class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                                Masuk
                            </button>
                            <button type="button" data-filter="OUT"
                                class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                                Keluar
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
                    <table id="attendanceClassTable" class="min-w-full text-left text-sm text-black">
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
                                    Kelas</th>
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
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        @endif
    </div>

    <script>
        $(function() {
            const table = $('#attendanceClassTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('guru.absensi.data') }}",
                    data: function(d) {
                        d.kelas_id = $('#filterKelas').val();
                        d.tanggal = $('#filterTanggal').val();
                        d.status = $('.filter-status.bg-black').data('filter');
                        d.search = $('#searchInput').val();
                    },
                    dataSrc: 'data'
                },
                pageLength: 10,
                lengthChange: false,
                dom: 't<"flex items-center justify-between mt-4 text-xs text-black/50"ip>',
                columns: [{
                        data: 'no',
                        className: 'px-6 py-4 text-black/40'
                    },
                    {
                        data: 'nama',
                        className: 'px-6 py-4',
                        render: function(nama) {
                            const initials = nama.split(' ').map(w => w[0]).join('').substring(0, 2)
                                .toUpperCase();
                            return `
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-xs font-semibold text-white plus-jakarta-sans-bold">${initials}</span>
                                    <span class="font-medium plus-jakarta-sans-bold">${nama}</span>
                                </div>`;
                        }
                    },
                    {
                        data: 'kelas',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'tanggal',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'waktu',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'status',
                        className: 'px-6 py-4',
                        render: function(status) {
                            if (status === 'IN') {
                                return `<span class="inline-flex items-center gap-1.5 rounded-full bg-black px-3 py-1 text-xs font-medium text-white plus-jakarta-sans-bold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>Masuk</span>`;
                            }
                            return `<span class="inline-flex items-center gap-1.5 rounded-full border border-black/30 px-3 py-1 text-xs font-medium text-black plus-jakarta-sans-bold">
                                <span class="h-1.5 w-1.5 rounded-full bg-black/50"></span>Keluar</span>`;
                        }
                    },
                ],
                language: {
                    zeroRecords: 'Tidak ada data ditemukan',
                    emptyTable: 'Belum ada data kehadiran',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    paginate: {
                        previous: '<i class="fa-solid fa-chevron-left"></i>',
                        next: '<i class="fa-solid fa-chevron-right"></i>'
                    }
                }
            });

            function loadStats() {
                $.getJSON("{{ route('guru.absensi.stats') }}", {
                    kelas_id: $('#filterKelas').val(),
                    tanggal: $('#filterTanggal').val()
                }, function(res) {
                    $('#statTotal').text(res.total_siswa);
                    $('#statHadir').text(res.hadir);
                    $('#statPulang').text(res.pulang);
                    $('#statBelumHadir').text(res.belum_hadir);
                });
            }
            loadStats();

            // ============ Switch Kelas ============
            $('#filterKelas').on('change', function() {
                table.ajax.reload();
                loadStats();
            });

            // ============ Filter tanggal ============
            $('#filterTanggal').on('change', function() {
                table.ajax.reload();
                loadStats();
            });

            // ============ Filter status pill ============
            $('.filter-status').on('click', function() {
                $('.filter-status').removeClass('bg-black text-white').addClass('text-black/60');
                $(this).addClass('bg-black text-white').removeClass('text-black/60');
                table.ajax.reload();
            });

            // ============ Search nama siswa (debounce) ============
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => table.ajax.reload(), 300);
            });
        });
    </script>
@endsection
