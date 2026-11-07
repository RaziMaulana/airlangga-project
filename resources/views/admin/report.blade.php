@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components.sidebar')

        <main class="ml-64 flex w-[calc(100%-16rem)] items-center justify-center p-8">

            <div class="grid w-full max-w-5xl grid-cols-1 gap-8 md:grid-cols-2">

                <!-- Card 1: Laporan Harian -->
                <div class="rounded-2xl bg-white p-8 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-[#3874FF]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#393E46]">
                            Laporan Harian
                        </h2>
                    </div>

                    <p class="mt-3 text-sm text-gray-500">
                        Cetak data rekap rekam presensi masuk dan keluar siswa berdasarkan hari tertentu.
                    </p>

                    <form action="{{ route('report.export') }}" method="GET" class="mt-6 space-y-4">
                        <input type="hidden" name="type" value="harian">

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Pilih Kelas</label>
                            <select name="id_kelas" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-[#3874FF] focus:bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-[#3874FF] focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Format Dokumen</label>
                            <select name="format" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-[#3874FF] focus:bg-white">
                                <option value="pdf">PDF Document (.pdf)</option>
                                <option value="word">Word Document (.docx)</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-[#3874FF] py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Generate Laporan
                        </button>
                    </form>
                </div>

                <!-- Card 2: Laporan Bulanan -->
                <div class="rounded-2xl bg-white p-8 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#393E46]">
                            Laporan Bulanan
                        </h2>
                    </div>

                    <p class="mt-3 text-sm text-gray-500">
                        Cetak matriks rekapitulasi kehadiran seluruh siswa dalam kurun waktu satu bulan.
                    </p>

                    <form action="{{ route('report.export') }}" method="GET" class="mt-6 space-y-4">
                        <input type="hidden" name="type" value="bulanan">

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Pilih Kelas</label>
                            <select name="id_kelas" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-purple-600 focus:bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelases as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Pilih Bulan</label>
                            <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-purple-600 focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500">Format Dokumen</label>
                            <select name="format" required
                                class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 outline-none focus:border-purple-600 focus:bg-white">
                                <option value="pdf">PDF Document (.pdf)</option>
                                <option value="word">Word Document (.docx)</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-purple-600 py-3 text-sm font-semibold text-white transition hover:bg-purple-700">
                            Generate Laporan
                        </button>
                    </form>
                </div>

            </div>

        </main>
    </div>
@endsection
