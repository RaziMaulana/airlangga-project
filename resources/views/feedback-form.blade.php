@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F5F5F4] p-6 lg:p-10">
        <div class="mx-auto max-w-2xl">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                    Masukan Anda berarti
                </p>
                <h1 class="mt-1 font-serif text-4xl text-black">
                    Sampaikan Saran
                </h1>
                <p class="mt-2 text-sm text-black/50 plus-jakarta-sans-reguler">
                    Punya ide untuk membuat sistem ini lebih baik? Tulis di sini, tim kami akan meninjaunya.
                </p>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-6 shadow-sm sm:p-8">
                {{-- Tambahkan id="form-saran" pada form --}}
                <form id="form-saran">
                    @csrf

                    {{-- Judul saran --}}
                    <div>
                        <label for="judul" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                            Judul
                        </label>
                        <input id="judul" type="text" name="judul" required
                            placeholder="Ringkasan singkat saran Anda"
                            class="mt-1 w-full border-b border-black/20 bg-transparent px-1 py-2.5 text-black transition placeholder:text-black/30 focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                    </div>

                    {{-- Kategori terkait --}}
                    <div class="mt-6">
                        <label for="topik" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                            Topik terkait
                        </label>
                        <select id="topik" name="topik" required
                            class="mt-1 w-full appearance-none border-b border-black/20 bg-transparent px-1 py-2.5 text-black transition focus:border-black focus:outline-none plus-jakarta-sans-reguler">
                            <option value="" disabled selected>Pilih topik</option>
                            <option value="absensi">Sistem Absensi</option>
                            <option value="device">Device / Perangkat</option>
                            <option value="tampilan">Tampilan &amp; Kemudahan Penggunaan</option>
                            <option value="notifikasi">Notifikasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Isi saran --}}
                    <div class="mt-6">
                        <label for="pesan" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                            Isi Saran
                        </label>
                        <textarea id="pesan" name="pesan" rows="5" required
                            placeholder="Jelaskan saran Anda selengkap mungkin, semakin detail semakin membantu kami..."
                            class="mt-1 w-full resize-none rounded-xl border border-black/15 bg-transparent px-4 py-3 text-black transition placeholder:text-black/30 focus:border-black focus:outline-none plus-jakarta-sans-reguler"></textarea>
                    </div>

                    {{-- Gambar bukti (opsional) --}}
                    <div class="mt-6">
                        <label for="bukti" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                            Gambar Bukti <span class="font-normal text-black/40">(opsional)</span>
                        </label>
                        <input id="bukti" type="file" name="bukti" accept="image/png, image/jpeg, image/webp"
                            class="mt-1 block w-full text-sm text-black/60 file:mr-4 file:rounded-full file:border-0 file:bg-black file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-black/80 plus-jakarta-sans-reguler">
                        <p class="mt-1 text-xs text-black/40 plus-jakarta-sans-reguler">Format JPG/PNG/WEBP, maksimal 2MB.
                        </p>

                        <img id="buktiPreview" src="" alt="Preview bukti"
                            class="mt-3 hidden max-h-48 rounded-xl border border-black/10 object-cover">
                    </div>

                    {{-- Info pengirim --}}
                    <div class="mt-6 flex items-center gap-3 rounded-xl bg-black/[0.03] px-4 py-3">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-black text-xs font-semibold text-white plus-jakarta-sans-bold">
                            {{ Str::of(Auth::user()->name ?? 'U')->substr(0, 2)->upper() }}
                        </span>
                        <div>
                            <p class="text-xs text-black/40 plus-jakarta-sans-reguler">Dikirim sebagai</p>
                            <p class="text-sm font-medium text-black plus-jakarta-sans-bold">
                                {{ Auth::user()->name ?? 'Pengguna' }}
                            </p>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="mt-8 flex items-center justify-end gap-3">
                        {{-- Logika IF Batal --}}
                        @if (Auth::user()->role === 'orang_tua')
                            <a href="{{ route('orangtua.index') }}"
                                class="text-sm text-black/60 underline decoration-black/30 underline-offset-4 transition hover:text-black hover:decoration-black plus-jakarta-sans-reguler">Batal</a>
                        @elseif (Auth::user()->role === 'murid')
                            <a href="{{ route('siswa.index') }}"
                                class="text-sm text-black/60 underline decoration-black/30 underline-offset-4 transition hover:text-black hover:decoration-black plus-jakarta-sans-reguler">Batal</a>
                        @elseif (Auth::user()->role === 'guru')
                            <a href="{{ route('guru.index') }}"
                                class="text-sm text-black/60 underline decoration-black/30 underline-offset-4 transition hover:text-black hover:decoration-black plus-jakarta-sans-reguler">Batal</a>
                        @else
                            <a href="{{ route('dashboard.index') }}"
                                class="text-sm text-black/60 underline decoration-black/30 underline-offset-4 transition hover:text-black hover:decoration-black plus-jakarta-sans-reguler">Batal</a>
                        @endif

                        {{-- Tambahkan id pada button untuk efek loading --}}
                        <button type="submit" id="btn-submit"
                            class="rounded-full bg-black px-7 py-3 text-sm font-semibold text-white transition hover:bg-black/80 plus-jakarta-sans-bold">
                            Kirim Saran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import jQuery dan SweetAlert2 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            // Preview gambar sebelum upload
            $('#bukti').on('change', function() {
                const file = this.files[0];
                const $preview = $('#buktiPreview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => $preview.attr('src', e.target.result).removeClass('hidden');
                    reader.readAsDataURL(file);
                } else {
                    $preview.addClass('hidden').attr('src', '');
                }
            });

            $('#form-saran').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let btnSubmit = $('#btn-submit');

                btnSubmit.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...');

                // Gunakan FormData karena ada file upload
                let formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('feedback-form.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        btnSubmit.prop('disabled', false).html('Kirim Saran');

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonColor: '#000000',
                                confirmButtonText: 'Tutup'
                            });

                            form[0].reset();
                            $('#buktiPreview').addClass('hidden').attr('src', '');
                        }
                    },
                    error: function(xhr) {
                        btnSubmit.prop('disabled', false).html('Kirim Saran');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = '';

                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                html: errorMessage,
                                confirmButtonColor: '#000000'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: 'Gagal mengirim saran. Silakan coba beberapa saat lagi.',
                                confirmButtonColor: '#000000'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
