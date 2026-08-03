@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-[#F5F5F4]">
        @include('components.sidebar')

        <main class="ml-64 flex-1 p-8 lg:p-10">

            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                    Masukan pengguna
                </p>
                <h1 class="mt-1 font-serif text-4xl italic text-black">
                    Saran
                </h1>
            </div>

            {{-- Top Card --}}
            <div class="mb-8 grid grid-cols-1 gap-4 lg:grid-cols-2">

                {{-- Total Masukan --}}
                <div class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                        Total Masukan
                    </p>

                    <h2 class="mt-4 font-serif text-5xl text-black">
                        {{ $feedbacks->count() }}
                    </h2>

                    <p class="mt-4 border-t border-black/10 pt-3 text-xs text-black/50 plus-jakarta-sans-reguler">
                        Seluruh saran dan masukan yang telah dikirimkan oleh pengguna.
                    </p>
                </div>

                {{-- Topik Terbanyak --}}
                <div class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-black"></span>

                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                            Topik Terbanyak
                        </p>
                    </div>

                    <h2 class="mt-4 font-serif text-4xl text-black">
                        {{ $namaTopik }}
                    </h2>

                    <p class="mt-4 border-t border-black/10 pt-3 text-xs text-black/50 plus-jakarta-sans-reguler">
                        Digunakan pada <strong>{{ $jumlahTopik }}</strong> masukan pengguna.
                    </p>
                </div>

            </div>

            {{-- Filter & Search --}}
            <div
                class="mb-6 flex flex-col gap-3 rounded-2xl border border-black/10 bg-white p-4 lg:flex-row lg:items-center lg:justify-between">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    {{-- Filter topik --}}
                    <select id="filterTopik"
                        class="rounded-xl border border-black/15 px-4 py-2 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                        <option value="all">Semua Topik</option>
                        <option value="absensi">Sistem Absensi</option>
                        <option value="device">Device / Perangkat</option>
                        <option value="tampilan">Tampilan &amp; Kemudahan Penggunaan</option>
                        <option value="notifikasi">Notifikasi</option>
                        <option value="lainnya">Lainnya</option>
                    </select>

                    {{-- Filter tanggal --}}
                    <input type="date" id="filterTanggal"
                        class="rounded-xl border border-black/15 px-4 py-2 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                </div>

                {{-- Search --}}
                <div class="relative w-full sm:w-72">
                    <i
                        class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-black/40"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama atau kata kunci..."
                        class="w-full rounded-full border border-black/15 bg-white py-2 pl-9 pr-4 text-sm text-black focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                </div>

                <button type="button" id="resetFilter"
                    class="whitespace-nowrap rounded-xl border border-black/10 px-4 py-2 text-sm text-black/60 transition hover:bg-black/[0.03] plus-jakarta-sans-reguler">
                    Reset
                </button>
            </div>

            {{-- Grid card --}}
            <div id="feedbackGrid" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

                @forelse ($feedbacks as $item)
                    <div class="feedback-card relative overflow-hidden rounded-2xl border border-black/10 bg-white p-6 shadow-sm transition hover:shadow-md"
                        data-topik="{{ $item->topik }}" data-tanggal="{{ $item->created_at->format('Y-m-d') }}"
                        data-search="{{ strtolower($item->user->name ?? 'Anonim') }} {{ strtolower($item->user->role ?? '') }} {{ strtolower($item->judul) }} {{ strtolower($item->pesan) }}">

                        <div class="mb-4 flex items-center gap-3">
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-full bg-black text-sm font-semibold text-white plus-jakarta-sans-bold">
                                {{ Str::of($item->user->name ?? 'U')->substr(0, 2)->upper() }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-black plus-jakarta-sans-bold">
                                    {{ $item->user->name ?? 'Anonim' }}</p>
                                <p class="text-xs text-black/40 plus-jakarta-sans-reguler">
                                    {{ ucfirst($item->user->role ?? 'User') }}</p>
                            </div>
                        </div>

                        <p class="text-sm font-bold text-black mb-2">{{ $item->judul }}</p>
                        <p class="text-sm leading-relaxed text-black/70 line-clamp-3 plus-jakarta-sans-reguler">
                            {{ $item->pesan }}
                        </p>

                        {{-- Thumbnail bukti --}}
                        @if ($item->bukti)
                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank"
                                class="mt-3 flex items-center gap-2 text-xs font-medium text-black/60 underline decoration-black/30 underline-offset-4 hover:text-black hover:decoration-black plus-jakarta-sans-reguler">
                                <i class="fa-regular fa-image"></i>
                                Lihat gambar bukti
                            </a>
                        @endif

                        <div class="mt-5 flex items-center justify-between border-t border-black/10 pt-4">
                            <div class="flex items-center gap-2 text-xs text-black/40 plus-jakarta-sans-reguler">
                                <i class="fa-regular fa-clock"></i>
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <button type="button"
                            class="mt-5 w-full rounded-full border border-black py-2.5 text-sm font-semibold text-black transition hover:bg-black hover:text-white plus-jakarta-sans-bold">
                            Lihat Detail
                        </button>
                    </div>
                @empty
                    <p class="text-center col-span-full">Belum ada saran masuk.</p>
                @endforelse

            </div>

            {{-- Empty state --}}
            <div id="emptyState" class="hidden flex-col items-center justify-center gap-2 py-20 text-center">
                <i class="fa-regular fa-comment-dots text-3xl text-black/20"></i>
                <p class="text-sm font-medium text-black plus-jakarta-sans-bold">Tidak ada saran ditemukan</p>
                <p class="text-xs text-black/40 plus-jakarta-sans-reguler">Coba ubah topik, tanggal, atau kata kunci
                    pencarian</p>
            </div>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feedback-card');
            const filterTopik = document.getElementById('filterTopik');
            const filterTanggal = document.getElementById('filterTanggal');
            const searchInput = document.getElementById('searchInput');
            const resetBtn = document.getElementById('resetFilter');
            const emptyState = document.getElementById('emptyState');

            function applyFilters() {
                const topik = filterTopik.value;
                const tanggal = filterTanggal.value;
                const keyword = searchInput.value.toLowerCase().trim();

                let visibleCount = 0;

                cards.forEach(card => {
                    const matchTopik = topik === 'all' || card.dataset.topik === topik;
                    const matchTanggal = !tanggal || card.dataset.tanggal === tanggal;
                    const matchSearch = !keyword || card.dataset.search.toLowerCase().includes(keyword);

                    const visible = matchTopik && matchTanggal && matchSearch;
                    card.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (emptyState) {
                    emptyState.classList.toggle('hidden', visibleCount !== 0);
                    emptyState.classList.toggle('flex', visibleCount === 0);
                }
            }

            filterTopik?.addEventListener('change', applyFilters);
            filterTanggal?.addEventListener('change', applyFilters);
            searchInput?.addEventListener('keyup', applyFilters);

            resetBtn?.addEventListener('click', function() {
                filterTopik.value = 'all';
                filterTanggal.value = '';
                searchInput.value = '';
                applyFilters();
            });
        });
    </script>
@endsection
