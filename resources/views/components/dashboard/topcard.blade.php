<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
            Ringkasan Statistik
        </p>
        <h2 class="mt-1 text-2xl font-semibold text-black plus-jakarta-sans-bold">
            Statistik Kehadiran
        </h2>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
    <div
        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-black/10 bg-white p-5 lg:p-6 shadow-sm transition-shadow hover:shadow-md">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                Presentase Kehadiran
            </p>
            <span class="h-2 w-2 rounded-full bg-black"></span>
        </div>

        <div class="mt-6 mb-2">
            <h2 class="font-serif text-5xl text-black md:text-6xl">
                <span id="persentaseKehadiranValue">{{ $stats['kehadiran'] }}</span><span
                    class="text-2xl not-italic text-black/40">%</span>
            </h2>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-black/10 pt-4">
            <p class="text-xs text-black/50 plus-jakarta-sans-reguler" id="kehadiranSubtitle">
                {{ $stats['hadir_count'] }} dari {{ $stats['total_siswa'] }} siswa
            </p>
            <span
                class="rounded-full bg-black/5 px-2.5 py-1 text-[10px] font-semibold text-black plus-jakarta-sans-bold"
                id="trendKehadiran">
                {{ $stats['trend_kehadiran']['arrow'] }} {{ $stats['trend_kehadiran']['label'] }}
            </span>
        </div>
    </div>

    <div
        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-black/10 bg-white p-5 lg:p-6 shadow-sm transition-shadow hover:shadow-md">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                Presentase Keterlambatan
            </p>
            <span class="h-2 w-2 rounded-full border-2 border-black/30"></span>
        </div>

        <div class="mt-6 mb-2">
            <h2 class="font-serif text-5xl text-black md:text-6xl">
                <span id="persentaseKeterlambatanValue">{{ $stats['keterlambatan'] }}</span><span
                    class="text-2xl not-italic text-black/40">%</span>
            </h2>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-black/10 pt-4">
            <p class="text-xs text-black/50 plus-jakarta-sans-reguler" id="keterlambatanSubtitle">
                {{ $stats['terlambat_count'] }} dari {{ $stats['total_siswa'] }} siswa
            </p>
            <span
                class="rounded-full border border-black/10 bg-white px-2.5 py-1 text-[10px] font-semibold text-black/60 plus-jakarta-sans-bold"
                id="trendKeterlambatan">
                {{ $stats['trend_keterlambatan']['arrow'] }} {{ $stats['trend_keterlambatan']['label'] }}
            </span>
        </div>
    </div>

    <div
        class="md:col-span-2 relative overflow-hidden rounded-2xl border border-black/10 bg-white p-5 lg:p-6 shadow-sm transition-shadow hover:shadow-md">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                    Konsistensi Kehadiran
                </p>
                <h3 class="mt-1 font-serif text-2xl text-black">Tren Mingguan</h3>
            </div>
            <span
                class="inline-flex w-fit items-center rounded-full border border-black/10 bg-[#F5F5F4] px-3 py-1.5 text-[10px] font-semibold text-black/60 plus-jakarta-sans-bold">
                7 Hari Terakhir
            </span>
        </div>

        <div class="relative w-full h-64 sm:h-72">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>
