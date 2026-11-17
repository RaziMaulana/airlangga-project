@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-[#F5F5F4] overflow-x-hidden">

        @include('components.sidebar')

        <main class="flex-1 w-full max-w-full overflow-x-hidden p-4 pt-20 sm:p-6 sm:pt-24 lg:ml-64 lg:p-10">

            <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between mt-2 sm:mt-0">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold">
                        Ringkasan hari ini
                    </p>
                    <h1 class="mt-1 font-serif text-3xl sm:text-4xl text-black">
                        Dashboard
                    </h1>
                </div>

                <p class="text-xs sm:text-sm text-black/50 plus-jakarta-sans-reguler font-medium">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="mb-8">
                @include('components.dashboard.topcard')
            </div>

            <div class="mb-8">
                @include('components.dashboard.datatable')
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- DOM Elements ---
            const dateModeInput = document.getElementById('filterDateMode');
            const dateInput = document.getElementById('filterDate');
            const startDateInput = document.getElementById('filterStartDate');
            const endDateInput = document.getElementById('filterEndDate');
            const singleDateWrapper = document.getElementById('singleDateWrapper');
            const rangeDateWrapper = document.getElementById('rangeDateWrapper');

            const statusInput = document.getElementById('filterStatus');
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('attendanceTableBody');
            const studentCount = document.getElementById('studentCount');
            const paginationNav = document.getElementById('paginationWrapper');

            let currentController = null;
            let pollTimer = null;
            let currentPage = 1;
            let chartInstance = null;

            // --- Filter Mode Interactivity UI ---
            dateModeInput?.addEventListener('change', function() {
                if (this.value === 'range') {
                    singleDateWrapper.classList.add('hidden');
                    rangeDateWrapper.classList.remove('hidden');
                    rangeDateWrapper.classList.add('flex');
                } else {
                    rangeDateWrapper.classList.add('hidden');
                    rangeDateWrapper.classList.remove('flex');
                    singleDateWrapper.classList.remove('hidden');
                }
                currentPage = 1;
                fetchDashboardData();
            });

            // --- Filter Input Changes ---
            [dateInput, startDateInput, endDateInput, statusInput].forEach(input => {
                input?.addEventListener('change', () => {
                    currentPage = 1;
                    fetchDashboardData();
                });
            });

            // --- Live Search with Debounce ---
            let searchTimeout = null;
            searchInput?.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentPage = 1;
                    fetchDashboardData();
                }, 300);
            });

            // --- Chart Management ---
            function initChart(labels = [], data = []) {
                const canvas = document.getElementById('attendanceChart');
                if (!canvas) return;

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Kehadiran (%)',
                            data: data,
                            borderColor: '#000000',
                            backgroundColor: 'rgba(0, 0, 0, 0.03)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#000000',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 0,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#000000',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: 'rgba(0,0,0,0.5)'
                                }
                            },
                            y: {
                                display: false,
                                min: 0,
                                suggestedMax: 105
                            }
                        }
                    }
                });
            }

            function updateChart(labels = [], data = []) {
                if (!chartInstance) {
                    initChart(labels, data);
                    return;
                }
                chartInstance.data.labels = labels;
                chartInstance.data.datasets[0].data = data;
                chartInstance.update();
            }

            // --- Top Card Updater ---
            function updateTopCard(stats = {}) {
                const setText = (id, text) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = text ?? '-';
                };

                setText('persentaseKehadiranValue', stats.kehadiran);
                setText('persentaseKeterlambatanValue', stats.keterlambatan);
                setText('kehadiranSubtitle', `${stats.hadir_count ?? 0} dari ${stats.total_siswa ?? 0} siswa`);
                setText('keterlambatanSubtitle',
                    `${stats.terlambat_count ?? 0} dari ${stats.total_siswa ?? 0} siswa`);
                setText('trendKehadiran',
                    `${stats.trend_kehadiran?.arrow ?? ''} ${stats.trend_kehadiran?.label ?? ''}`);
                setText('trendKeterlambatan',
                    `${stats.trend_keterlambatan?.arrow ?? ''} ${stats.trend_keterlambatan?.label ?? ''}`);
            }

            // --- Render Table Rows Dynamically ---
            function updateTableHTML(presensis = []) {
                if (!tableBody) return;

                if (presensis.length === 0) {
                    tableBody.innerHTML = `
                    <tr id="emptyRow">
                        <td colspan="6" class="px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-40"></i>
                                Belum ada data kehadiran.
                            </div>
                        </td>
                    </tr>`;
                    return;
                }

                const fragment = document.createDocumentFragment();
                const offset = (currentPage - 1) * 10;

                presensis.forEach((item, index) => {
                    const iteration = String(offset + index + 1).padStart(2, '0');

                    let badgeHTML = '';
                    if (item.status === 'Hadir') {
                        badgeHTML =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-black px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-white plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Hadir</span>`;
                    } else if (item.status === 'Sakit') {
                        badgeHTML =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Sakit</span>`;
                    } else {
                        badgeHTML =
                            `<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide plus-jakarta-sans-bold shadow-sm"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Izin</span>`;
                    }

                    const row = document.createElement('tr');
                    row.className = 'attendance-row transition hover:bg-black/[0.02]';
                    row.innerHTML = `
                        <td class="px-5 py-4 text-black/40 plus-jakarta-sans-reguler">${iteration}</td>
                        <td class="px-5 py-4 font-semibold text-black plus-jakarta-sans-bold">${item.nama}</td>
                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler">${item.kelas}</td>
                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler">${item.tanggal}</td>
                        <td class="px-5 py-4 text-black/60 plus-jakarta-sans-reguler font-medium">${item.waktu}</td>
                        <td class="px-5 py-4">${badgeHTML}</td>
                    `;
                    fragment.appendChild(row);
                });

                tableBody.innerHTML = '';
                tableBody.appendChild(fragment);
            }

            // --- Render Pagination ---
            function renderPagination(pagination) {
                const wrapper = document.getElementById('paginationWrapper');
                if (!wrapper) return;

                if (!pagination || pagination.last_page <= 1) {
                    wrapper.innerHTML = '';
                    return;
                }

                const currentPageNum = pagination.current_page;
                const lastPage = pagination.last_page;
                const delta = 2;
                let range = [];
                let rangeWithDots = [];
                let l;

                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPageNum - delta && i <= currentPageNum + delta)) {
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

                let pagesHTML = '';
                rangeWithDots.forEach(page => {
                    if (page === '...') {
                        pagesHTML +=
                            `<span class="inline-flex h-8 w-8 items-center justify-center text-xs text-black/40">...</span>`;
                    } else if (page === currentPageNum) {
                        pagesHTML +=
                            `<span class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-black bg-black text-xs font-semibold text-white shadow-sm">${page}</span>`;
                    } else {
                        pagesHTML +=
                            `<button data-page="${page}" class="ajax-pagination-btn inline-flex h-8 w-8 items-center justify-center rounded-xl border border-black/10 bg-white text-xs font-medium text-black transition hover:bg-black/5">${page}</button>`;
                    }
                });

                wrapper.innerHTML = `
                    <p class="text-xs text-black/60 plus-jakarta-sans-reguler">
                        Menampilkan <span class="font-semibold text-black">${pagination.from ?? 0}</span> – <span class="font-semibold text-black">${pagination.to ?? 0}</span> dari <span class="font-semibold text-black">${pagination.total ?? 0}</span> data
                    </p>
                    <div class="inline-flex items-center gap-1.5">
                        <button data-page="${currentPageNum - 1}" ${currentPageNum === 1 ? 'disabled' : ''}
                            class="ajax-pagination-btn inline-flex items-center rounded-xl border border-black/10 bg-white px-3 py-2 text-xs font-medium text-black transition hover:bg-black/5 disabled:opacity-40 disabled:cursor-not-allowed">
                            Prev
                        </button>
                        ${pagesHTML}
                        <button data-page="${currentPageNum + 1}" ${currentPageNum === lastPage ? 'disabled' : ''}
                            class="ajax-pagination-btn inline-flex items-center rounded-xl border border-black/10 bg-white px-3 py-2 text-xs font-medium text-black transition hover:bg-black/5 disabled:opacity-40 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                `;

                // Bind event click pada tombol paginasi AJAX yang baru dirender
                wrapper.querySelectorAll('.ajax-pagination-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        currentPage = parseInt(this.dataset.page, 10);
                        fetchDashboardData();
                    });
                });
            }

            // --- AJAX Fetch Handler ---
            function fetchDashboardData() {
                const mode = dateModeInput?.value || 'single';
                const status = statusInput?.value || '';
                const search = searchInput?.value || '';

                if (tableBody) {
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-black"></i>
                                <span class="text-xs font-medium tracking-wide">Memuat data kehadiran...</span>
                            </div>
                        </td>
                    </tr>`;
                }

                if (currentController) currentController.abort();
                currentController = new AbortController();

                let url =
                    `{{ route('dashboard.index') }}?status=${encodeURIComponent(status)}&date_mode=${mode}&page=${currentPage}&search=${encodeURIComponent(search)}`;

                if (mode === 'single') {
                    url += `&tanggal=${encodeURIComponent(dateInput?.value || '')}`;
                } else {
                    url +=
                        `&start_date=${encodeURIComponent(startDateInput?.value || '')}&end_date=${encodeURIComponent(endDateInput?.value || '')}`;
                }

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: currentController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateTableHTML(data.presensis);
                        if (studentCount) studentCount.textContent = `Total: ${data.count} siswa`;
                        renderPagination(data.pagination);
                        updateTopCard(data.stats);
                        updateChart(data.weeklyTrend?.labels, data.weeklyTrend?.data);
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            console.error('Error fetching data:', error);
                            if (tableBody) {
                                tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center text-red-500 plus-jakarta-sans-reguler">
                                    Gagal memuat data. Silakan coba lagi.
                                </td>
                            </tr>`;
                            }
                        }
                    });
            }

            // --- Polling & Visibility ---
            function startPolling() {
                stopPolling();
                pollTimer = setInterval(() => {
                    if (document.visibilityState === 'visible') fetchDashboardData();
                }, 15000);
            }

            function stopPolling() {
                if (pollTimer) clearInterval(pollTimer);
            }

            // --- Initial Setup ---
            initChart({!! json_encode($weeklyTrend['labels'] ?? []) !!}, {!! json_encode($weeklyTrend['data'] ?? []) !!});

            // Intervensi klik paginasi awal (jika ada render dari server)
            document.querySelectorAll('.ajax-pagination-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const urlObj = new URL(this.href);
                    currentPage = parseInt(urlObj.searchParams.get('page') || 1, 10);
                    fetchDashboardData();
                });
            });

            startPolling();

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') fetchDashboardData();
            });
        });
    </script>
@endsection
