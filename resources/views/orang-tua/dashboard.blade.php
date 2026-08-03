@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F5F5F4] p-6 lg:p-10">

        <!-- Header -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                    Halo, {{ Auth::user()->name ?? 'Orang Tua' }}
                </p>
                <h1 class="mt-1 font-serif text-4xl text-black">
                    Monitor Anak
                </h1>
                <p class="mt-1 text-sm text-black/50 plus-jakarta-sans-reguler">
                    Pantau kehadiran anak Anda di sekolah
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

        {{-- Pemilih anak --}}
        <div class="mb-6 flex items-center gap-3 overflow-x-auto pb-1">
            @foreach ($anakList as $i => $anak)
                @php
                    $initials = collect(explode(' ', $anak->nama_siswa))
                        ->map(fn($w) => $w[0] ?? '')
                        ->join('');
                    $initials = strtoupper(substr($initials, 0, 2));
                @endphp
                <button type="button" data-id="{{ $anak->id_siswa }}" data-nama="{{ $anak->nama_siswa }}"
                    data-kelas="{{ $anak->kelas->nama_kelas ?? '-' }}"
                    class="child-tab flex items-center gap-3 rounded-full border px-4 py-2 transition plus-jakarta-sans-bold
                        {{ $i === 0 ? 'border-black bg-black text-white' : 'border-black/15 bg-white text-black/60 hover:border-black/40 hover:text-black' }}">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                            {{ $i === 0 ? 'bg-white text-black' : 'bg-black/10 text-black' }}">
                        {{ $initials }}
                    </span>
                    <span class="text-sm">{{ $anak->nama_siswa }}</span>
                </button>
            @endforeach
        </div>

        <!-- Status hari ini -->
        <div
            class="mb-8 flex flex-col gap-5 rounded-2xl border border-black/10 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div class="flex items-center gap-4">
                <span id="anakInitial"
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-black text-lg font-semibold text-white plus-jakarta-sans-bold">
                    -
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                        Status hari ini &middot; {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </p>
                    <h2 id="anakNama" class="mt-1 font-serif text-3xl text-black">-</h2>
                    <p id="anakKelas" class="text-sm text-black/50 plus-jakarta-sans-reguler">-</p>
                </div>
            </div>

            <div class="flex items-center gap-6 sm:justify-end">
                <div>
                    <p class="text-xs text-black/40 plus-jakarta-sans-reguler">Jam masuk</p>
                    <p id="jamMasukHariIni" class="text-lg font-semibold text-black plus-jakarta-sans-bold">-</p>
                </div>
                <div class="h-10 w-px bg-black/10"></div>
                <span id="statusHariIniBadge"
                    class="inline-flex items-center gap-2 rounded-full border border-black/30 px-5 py-2.5 text-sm font-semibold text-black plus-jakarta-sans-bold">
                    <span class="h-2 w-2 rounded-full bg-black/40"></span>
                    <span id="statusHariIniText">-</span>
                </span>
            </div>
        </div>

        <!-- Ringkasan bulan ini -->
        <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-2">

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-black"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">Hadir
                    </p>
                </div>
                <h2 id="statHadir" class="mt-3 font-serif text-4xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Bulan terpilih</p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full border border-black/40"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-black/40 plus-jakarta-sans-bold">Belum
                        Tap Pulang</p>
                </div>
                <h2 id="statBelumPulang" class="mt-3 font-serif text-4xl text-black">0</h2>
                <p class="mt-2 text-xs text-black/50 plus-jakarta-sans-reguler">Bulan terpilih</p>
            </div>

        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm sm:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                        Riwayat
                    </p>
                    <h2 class="mt-1 text-xl font-semibold text-black plus-jakarta-sans-bold">
                        Riwayat Kehadiran
                    </h2>
                </div>
            </div>

            <!-- Filter & Search -->
            <div
                class="mb-6 flex flex-col gap-3 border-y border-black/10 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-1 flex-wrap items-center gap-3">
                    {{-- Filter bulan --}}
                    <div class="relative">
                        <i
                            class="fa-regular fa-calendar pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                        <select id="filterBulan"
                            class="appearance-none rounded-full border border-black/15 bg-white py-2 pl-9 pr-8 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    {{-- Filter status --}}
                    <div class="flex items-center gap-2 rounded-full border border-black/15 p-1">
                        <button type="button" data-filter="all"
                            class="filter-status rounded-full bg-black px-4 py-1.5 text-xs font-semibold text-white transition plus-jakarta-sans-bold">
                            Semua
                        </button>
                        <button type="button" data-filter="Hadir"
                            class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                            Hadir
                        </button>
                        <button type="button" data-filter="Belum Pulang"
                            class="filter-status rounded-full px-4 py-1.5 text-xs font-semibold text-black/60 transition hover:text-black plus-jakarta-sans-bold">
                            Belum Pulang
                        </button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="relative w-full sm:w-64">
                    <i
                        class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                    <input type="text" id="searchInput" placeholder="Cari tanggal..."
                        class="w-full rounded-full border border-black/15 bg-white py-2 pl-9 pr-4 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="attendanceParentTable" class="min-w-full text-left text-sm text-black">
                    <thead>
                        <tr class="border-b border-black/10">
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                No</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Tanggal</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Jam Masuk</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Jam Pulang</th>
                            <th
                                class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div id="emptyState" class="hidden flex-col items-center justify-center gap-2 py-16 text-center">
                    <i class="fa-regular fa-folder-open text-3xl text-black/20"></i>
                    <p class="text-sm font-medium text-black plus-jakarta-sans-bold">Tidak ada data ditemukan</p>
                    <p class="text-xs text-black/40 plus-jakarta-sans-reguler">Kartu RFID anak ini mungkin belum terdaftar,
                        atau coba ubah filter</p>
                </div>
            </div>

        </div>

    </div>

    <script>
        $(function() {
            let currentIdSiswa = $('.child-tab.bg-black').data('id');

            const table = $('#attendanceParentTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('orangtua.absensi.data') }}",
                    data: function(d) {
                        d.id_siswa = currentIdSiswa;
                        d.bulan = $('#filterBulan').val();
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
                        data: 'tanggal',
                        className: 'px-6 py-4 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'jam_masuk',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'jam_pulang',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'status',
                        className: 'px-6 py-4',
                        render: function(status) {
                            if (status === 'Hadir') {
                                return `<span class="inline-flex items-center gap-1.5 rounded-full bg-black px-3 py-1 text-xs font-medium text-white plus-jakarta-sans-bold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>Hadir</span>`;
                            }
                            if (status === 'Belum Pulang') {
                                return `<span class="inline-flex items-center gap-1.5 rounded-full border border-black/30 px-3 py-1 text-xs font-medium text-black plus-jakarta-sans-bold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-black/50"></span>Belum Pulang</span>`;
                            }
                            return `<span class="inline-flex items-center gap-1.5 rounded-full bg-black/10 px-3 py-1 text-xs font-medium text-black/60 plus-jakarta-sans-bold">
                                <span class="h-1.5 w-1.5 rounded-full bg-black/30"></span>Alpa</span>`;
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
                },
                drawCallback: function() {
                    const visibleRows = this.api().rows({
                        search: 'applied'
                    }).count();
                    $('#emptyState').toggleClass('hidden', visibleRows !== 0).toggleClass('flex',
                        visibleRows === 0);
                }
            });

            function loadStats() {
                $.getJSON("{{ route('orangtua.absensi.stats') }}", {
                    id_siswa: currentIdSiswa,
                    bulan: $('#filterBulan').val()
                }, function(res) {
                    $('#jamMasukHariIni').text(res.jam_masuk_hari_ini ?? '-');
                    $('#statusHariIniText').text(res.status_hari_ini);
                    $('#statHadir').text(res.total_hadir);
                    $('#statBelumPulang').text(res.belum_pulang);

                    const $badge = $('#statusHariIniBadge');
                    if (res.status_hari_ini === 'Hadir') {
                        $badge.removeClass('border-black/30 text-black').addClass(
                            'bg-black text-white border-black');
                        $badge.find('span:first').removeClass('bg-black/40').addClass('bg-white');
                    } else {
                        $badge.removeClass('bg-black text-white border-black').addClass(
                            'border-black/30 text-black');
                        $badge.find('span:first').removeClass('bg-white').addClass('bg-black/40');
                    }
                });
            }

            function updateHeaderAnak($tab) {
                const nama = $tab.data('nama');
                const initials = nama.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
                $('#anakInitial').text(initials);
                $('#anakNama').text(nama);
                $('#anakKelas').text($tab.data('kelas'));
            }

            updateHeaderAnak($('.child-tab.bg-black'));
            loadStats();

            $('.child-tab').on('click', function() {
                $('.child-tab').removeClass('bg-black text-white border-black').addClass(
                    'bg-white text-black/60 border-black/15');
                $('.child-tab').find('span:first').removeClass('bg-white text-black').addClass(
                    'bg-black/10 text-black');

                $(this).removeClass('bg-white text-black/60 border-black/15').addClass(
                    'bg-black text-white border-black');
                $(this).find('span:first').removeClass('bg-black/10 text-black').addClass(
                    'bg-white text-black');

                currentIdSiswa = $(this).data('id');
                updateHeaderAnak($(this));
                table.ajax.reload();
                loadStats();
            });

            $('#filterBulan').on('change', function() {
                table.ajax.reload();
                loadStats();
            });

            $('.filter-status').on('click', function() {
                $('.filter-status').removeClass('bg-black text-white').addClass('text-black/60');
                $(this).addClass('bg-black text-white').removeClass('text-black/60');
                table.ajax.reload();
            });

            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => table.ajax.reload(), 300);
            });
        });
    </script>
@endsection
