@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components.sidebar')

        <main class="ml-64 flex w-[calc(100%-16rem)] items-center justify-center p-8">

            <div class="grid w-full max-w-5xl grid-cols-1 gap-8 md:grid-cols-2">

                <!-- Card 1 -->
                <div class="rounded-2xl bg-white p-8 shadow-2xl transition hover:-translate-y-1 hover:shadow-3xl">
                    <h2 class="text-2xl font-bold text-[#393E46]">
                        Card Pertama
                    </h2>

                    <p class="mt-4 text-gray-600">
                        Isi card pertama dapat berupa informasi, tombol aksi, atau menu lainnya.
                    </p>

                    <button
                        class="mt-8 rounded-xl bg-[#3874FF] px-6 py-3 font-semibold text-white transition hover:bg-blue-700">
                        Selengkapnya
                    </button>
                </div>

                <!-- Card 2 -->
                <div class="rounded-2xl bg-white p-8 shadow-2xl transition hover:-translate-y-1 hover:shadow-3xl">
                    <h2 class="text-2xl font-bold text-[#393E46]">
                        Card Kedua
                    </h2>

                    <p class="mt-4 text-gray-600">
                        Isi card kedua dapat digunakan untuk fitur lain atau menampilkan data.
                    </p>

                    <button
                        class="mt-8 rounded-xl bg-[#3874FF] px-6 py-3 font-semibold text-white transition hover:bg-blue-700">
                        Selengkapnya
                    </button>
                </div>

            </div>

        </main>
    </div>
@endsection
