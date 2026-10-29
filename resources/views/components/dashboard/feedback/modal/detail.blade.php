<div id="feedbackDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-xl ring-1 ring-black/5">

        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-gray-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span id="detailAvatar"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-black text-sm font-semibold text-white plus-jakarta-sans-bold">
                    --
                </span>
                <div>
                    <p id="detailNama" class="text-sm font-semibold text-gray-900 plus-jakarta-sans-bold">-</p>
                    <p id="detailRole" class="text-xs text-black/40 plus-jakarta-sans-reguler">-</p>
                </div>
            </div>

            <button type="button" onclick="closeFeedbackDetailModal()"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">

            <div class="flex flex-wrap items-center gap-2">
                <span id="detailTopik"
                    class="rounded-full border border-black/20 px-3 py-1 text-xs text-black/70 plus-jakarta-sans-reguler">
                    -
                </span>
                <span class="flex items-center gap-1.5 text-xs text-black/40 plus-jakarta-sans-reguler">
                    <i class="fa-regular fa-clock"></i>
                    <span id="detailTanggal">-</span>
                </span>
            </div>

            <h3 id="detailJudul" class="mt-4 text-lg font-bold text-black plus-jakarta-sans-bold">
                -
            </h3>

            <p id="detailPesan"
                class="mt-2 whitespace-pre-line text-sm leading-relaxed text-black/70 plus-jakarta-sans-reguler">
                -
            </p>

            {{-- Bukti --}}
            <div id="detailBuktiWrapper" class="mt-5 hidden">
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                    Bukti Terlampir
                </p>
                <a id="detailBuktiLink" href="#" target="_blank"
                    class="block overflow-hidden rounded-xl border border-black/10">
                    <img id="detailBuktiImg" src="" alt="Bukti"
                        class="max-h-72 w-full object-cover transition hover:opacity-90">
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-5">
            <button type="button" onclick="closeFeedbackDetailModal()"
                class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 plus-jakarta-sans-reguler">
                Tutup
            </button>
        </div>

    </div>
</div>
