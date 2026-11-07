@extends('layouts.app')

@section('content')
    <div class="flex-1 min-h-screen bg-[#F5F5F4]">
        @include('components.sidebar')

        <main class="w-full p-4 pt-20 sm:p-6 sm:pt-24 lg:ml-64 lg:w-[calc(100%-16rem)] lg:p-10">

            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.25em] text-black/40 plus-jakarta-sans-bold sm:text-xs">
                        Manajemen pengguna
                    </p>
                    <h1 class="mt-1 font-serif text-3xl text-black sm:text-4xl">
                        Akses
                    </h1>
                </div>

                <button type="button" onclick="openTambahUserModal()"
                    class="w-full rounded-full bg-black px-6 py-3 text-sm font-semibold text-white transition hover:bg-black/80 plus-jakarta-sans-bold sm:w-auto">
                    + Tambah pengguna
                </button>
            </div>

            {{-- Top card --}}
            <div id="topcardContainer" class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2">
                @include('components.dashboard.access.topcard')
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl border border-black/10 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                {{-- Datatable Component --}}
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/40 plus-jakarta-sans-bold">
                            Daftar pengguna
                        </p>
                        <h2 class="mt-1 text-xl font-semibold text-black plus-jakarta-sans-bold">
                            Semua Akun
                        </h2>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative">
                            <select id="filterRole"
                                class="w-full appearance-none rounded-full border border-black/10 bg-[#F5F5F4] px-4 py-2.5 pr-9 text-sm text-black/75 focus:border-black focus:outline-none plus-jakarta-sans-reguler sm:w-44 cursor-pointer">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                                <option value="orangtua">Orang Tua</option>
                                <option value="staff">Staff</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-black/30"></i>
                        </div>

                        <div class="relative">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-xs text-black/30"></i>
                            <input type="text" id="searchInput" placeholder="Cari nama atau email..."
                                class="w-full rounded-full border border-black/10 bg-[#F5F5F4] py-2.5 pl-10 pr-4 text-sm text-black placeholder:text-black/30 focus:border-black focus:outline-none plus-jakarta-sans-reguler sm:w-64">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="accessTable" class="min-w-full text-left text-sm text-black">
                        <thead>
                            <tr class="border-b border-black/10">
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    Nama</th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    Role</th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    Kelas</th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-black/40 plus-jakarta-sans-bold">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="accessTableBody" class="divide-y divide-black/5">
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xl text-black"></i> Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="paginationNav"></div>
            </div>

        </main>
    </div>

    {{-- Modal Tambah & Edit Pengguna --}}
    @include('components.dashboard.access.modal.add')
    @include('components.dashboard.access.modal.edit')

    <script>
        $(function() {
            let allData = [];
            let currentPage = 1;
            const perPage = 5;

            // ============ SweetAlert Toast ============
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            function showNotif(message, icon = 'success') {
                Toast.fire({
                    icon,
                    title: message
                });
            }

            // ============ Render Tabel & Paginasi Kustom (Mirip Dashboard) ============
            function renderTableAndPagination() {
                const searchVal = ($('#searchInput').val() || '').toLowerCase().trim();
                const roleVal = $('#filterRole').val() || '';

                // Filter data
                const filteredData = allData.filter(item => {
                    const matchSearch = item.name.toLowerCase().includes(searchVal) || item.email
                        .toLowerCase().includes(searchVal);
                    const matchRole = roleVal === '' || item.role === roleVal;
                    return matchSearch && matchRole;
                });

                const totalData = filteredData.length;
                const lastPage = Math.ceil(totalData / perPage) || 1;
                if (currentPage > lastPage) currentPage = lastPage;

                const start = (currentPage - 1) * perPage;
                const paginatedData = filteredData.slice(start, start + perPage);

                const $tbody = $('#accessTableBody');
                $tbody.empty();

                if (paginatedData.length === 0) {
                    $tbody.append(`
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-black/50 plus-jakarta-sans-reguler">
                                Data tidak ditemukan.
                            </td>
                        </tr>
                    `);
                    $('#paginationNav').empty();
                    return;
                }

                paginatedData.forEach((row, index) => {
                    const iteration = String(start + index + 1).padStart(2, '0');
                    const initials = row.name.split(' ').map(w => w[0]).join('').substring(0, 2)
                        .toUpperCase();

                    let roleLabel = row.role ? row.role.charAt(0).toUpperCase() + row.role.slice(1) : '-';

                    let kelasHtml = '<span class="text-black/30">-</span>';
                    if (['guru', 'siswa', 'orangtua'].includes(row.role)) {
                        if (!row.kelas || row.kelas.length === 0) {
                            kelasHtml = '<span class="text-black/30 italic">Belum ada kelas</span>';
                        } else {
                            kelasHtml =
                                `<div class="flex flex-wrap gap-1">${row.kelas.map(k => `<span class="rounded-full bg-black/5 px-2.5 py-1 text-xs text-black/70 plus-jakarta-sans-reguler">${k}</span>`).join('')}</div>`;
                        }
                    }

                    $tbody.append(`
                        <tr class="transition hover:bg-black/[0.02]">
                            <td class="px-6 py-4 text-black/40">${iteration}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-xs font-semibold text-white plus-jakarta-sans-bold">${initials}</span>
                                    <span class="font-medium plus-jakarta-sans-bold text-black">${row.name}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-black/60 plus-jakarta-sans-reguler">${row.email}</td>
                            <td class="px-6 py-4"><span class="rounded-full border border-black/20 px-3 py-1 text-xs text-black/70 plus-jakarta-sans-reguler">${roleLabel}</span></td>
                            <td class="px-6 py-4">${kelasHtml}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 text-xs plus-jakarta-sans-bold">
                                    <button type="button" data-id="${row.id}" class="btn-edit text-black underline decoration-black/30 underline-offset-4 hover:decoration-black">Edit</button>
                                    <button type="button" data-id="${row.id}" class="btn-hapus text-black/50 underline decoration-black/20 underline-offset-4 hover:text-black hover:decoration-black">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    `);
                });

                renderPaginationNav(totalData, start + 1, Math.min(start + perPage, totalData), lastPage);
            }

            function renderPaginationNav(total, from, to, lastPage) {
                const $nav = $('#paginationNav');
                if (total === 0) {
                    $nav.empty();
                    return;
                }

                let html = `
                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-t border-black/5 pt-5">
                    <p class="text-xs text-black/60 plus-jakarta-sans-reguler text-center sm:text-left">
                        Menampilkan <span class="font-semibold text-black">${from}</span> – <span class="font-semibold text-black">${to}</span> dari <span class="font-semibold text-black">${total}</span> data
                    </p>
                    <div class="flex items-center justify-center sm:justify-end gap-1.5 flex-wrap">
                        <button data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}
                            class="pagination-btn inline-flex h-9 items-center justify-center rounded-xl border border-black/10 bg-white px-3 text-xs font-medium text-black transition hover:bg-black/5 plus-jakarta-sans-reguler disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                            Prev
                        </button>`;

                const delta = 2;
                const range = [];
                const rangeWithDots = [];
                let l;

                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPage - delta && i <= currentPage + delta)) {
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
                            class="pagination-btn h-9 min-w-[2.25rem] inline-flex items-center justify-center rounded-xl px-3 text-xs transition-colors plus-jakarta-sans-reguler ${active ? 'border-black bg-black text-white font-semibold shadow-sm' : 'border border-black/10 bg-white text-black hover:bg-black/5 shadow-sm'}">
                            ${page}
                        </button>`;
                    }
                });

                html += `
                        <button data-page="${currentPage + 1}" ${currentPage === lastPage ? 'disabled' : ''}
                            class="pagination-btn inline-flex h-9 items-center justify-center rounded-xl border border-black/10 bg-white px-3 text-xs font-medium text-black transition hover:bg-black/5 plus-jakarta-sans-reguler disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                            Next
                        </button>
                    </div>
                </div>`;

                $nav.html(html);

                $nav.find('.pagination-btn').on('click', function() {
                    if (this.disabled) return;
                    currentPage = parseInt($(this).data('page'), 10);
                    renderTableAndPagination();
                });
            }

            // ============ Load Data dari Controller ============
            function loadTableData() {
                $.getJSON("{{ route('access.data') }}", function(res) {
                    allData = res.data || [];
                    renderTableAndPagination();
                });
            }
            loadTableData();

            // Event Listeners untuk Filter & Search
            $('#searchInput').on('keyup', function() {
                currentPage = 1;
                renderTableAndPagination();
            });

            $('#filterRole').on('change', function() {
                currentPage = 1;
                renderTableAndPagination();
            });

            // ============ Top card stats (AJAX) ============
            function loadStats() {
                $.getJSON("{{ route('access.stats') }}", function(res) {
                    $('#statTotal').text(res.total);
                    $('#statSiswa').text(res.siswa);
                    $('#statGuru').text(res.guru);
                    $('#statOrangTua').text(res.orang_tua);

                    const persenSiswa = res.total > 0 ? Math.round((res.siswa / res.total) * 100) : 0;
                    $('#statAktifPersen').text(persenSiswa);
                    $('#statAktifBar').css('width', persenSiswa + '%');
                });
            }
            loadStats();

            function toggleRoleFields($roleSelect, $guruWrapper, $siswaWrapper, $orangtuaWrapper) {
                const role = $roleSelect.val();
                $guruWrapper.toggleClass('hidden', role !== 'guru');
                $siswaWrapper.toggleClass('hidden', role !== 'siswa');
                $orangtuaWrapper.toggleClass('hidden', role !== 'orangtua');
            }

            $('#role').on('change', function() {
                toggleRoleFields($(this), $('#guruFields'), $('#siswaFields'), $('#orangtuaFields'));
            });

            $('#edit_role').on('change', function() {
                toggleRoleFields($(this), $('#edit_guruFields'), $('#edit_siswaFields'), $(
                    '#edit_orangtuaFields'));
            });

            // ============ Tambah Pengguna ============
            $('#formTambahUser').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');

                $form.find('.error-message').remove();
                $form.find('input, select').removeClass('border-red-500');
                $submitBtn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(res) {
                        closeTambahUserModal();
                        loadTableData();
                        loadStats();
                        showNotif(res.message ?? 'Pengguna baru berhasil ditambahkan.');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                const $input = $form.find(`[name="${field}"]`);
                                $input.addClass('border-red-500');
                                $input.after(
                                    `<p class="error-message mt-2 text-sm text-red-600">${messages[0]}</p>`
                                    );
                            });
                        } else {
                            showNotif('Terjadi kesalahan, silakan coba lagi.', 'error');
                        }
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).text('Simpan Pengguna');
                    }
                });
            });

            // ============ Edit Pengguna ============
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.getJSON(`{{ url('access') }}/${id}/edit`, function(res) {
                    const u = res.user;
                    $('#editUserId').val(u.id);
                    $('#edit_name').val(u.name);
                    $('#edit_email').val(u.email);
                    $('#edit_role').val(u.role);
                    $('#edit_password').val('');
                    $('#edit_password_confirmation').val('');

                    $('#edit_nip').val(u.nip ?? '');
                    $('#edit_kelasWrapper input[name="kelas_ids[]"]').prop('checked', false);
                    (u.kelas_ids || []).forEach(kid => $(`#edit_kelasWrapper input[value="${kid}"]`)
                        .prop('checked', true));

                    $('#edit_id_siswa').val(u.id_siswa ?? '');
                    $('#edit_kelas_id').val(u.kelas_id ?? '');

                    $('#edit_nama_ayah').val(u.nama_ayah ?? '');
                    $('#edit_nama_ibu').val(u.nama_ibu ?? '');
                    $('#edit_no_hp').val(u.no_hp ?? '');
                    $('#edit_siswaAksesWrapper input[name="siswa_ids[]"]').prop('checked', false);
                    (u.siswa_ids || []).forEach(sid => $(
                        `#edit_siswaAksesWrapper input[value="${sid}"]`).prop('checked',
                        true));

                    toggleRoleFields($('#edit_role'), $('#edit_guruFields'), $('#edit_siswaFields'),
                        $('#edit_orangtuaFields'));

                    $('#formEditUser').find('.error-message').remove();
                    $('#formEditUser').find('input, select').removeClass('border-red-500');

                    openEditUserModal();
                }).fail(() => showNotif('Gagal memuat data pengguna.', 'error'));
            });

            $('#formEditUser').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const id = $('#editUserId').val();
                const $submitBtn = $form.find('button[type="submit"]');

                $form.find('.error-message').remove();
                $form.find('input, select').removeClass('border-red-500');
                $submitBtn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: `{{ url('access') }}/${id}`,
                    method: 'PUT',
                    data: $form.serialize(),
                    success: function(res) {
                        closeEditUserModal();
                        loadTableData();
                        loadStats();
                        showNotif(res.message ?? 'Data pengguna berhasil diperbarui.');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                const $input = $form.find(`[name="${field}"]`);
                                $input.addClass('border-red-500');
                                $input.after(
                                    `<p class="error-message mt-2 text-sm text-red-600">${messages[0]}</p>`
                                    );
                            });
                        } else {
                            showNotif('Terjadi kesalahan, silakan coba lagi.', 'error');
                        }
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // ============ Hapus Pengguna ============
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                const name = $(this).closest('tr').find('td:eq(1) span:last').text().trim();

                Swal.fire({
                    title: 'Hapus pengguna?',
                    html: `Data <b>${name || 'pengguna ini'}</b> akan dihapus permanen dan tidak dapat dikembalikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#000',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('access') }}/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                loadTableData();
                                loadStats();
                                showNotif(res.message ?? 'Pengguna berhasil dihapus.');
                            },
                            error: () => showNotif('Gagal menghapus pengguna.', 'error')
                        });
                    }
                });
            });

            // ============ Modal Helpers ============
            window.openTambahUserModal = () => $('#tambahUserModal').removeClass('hidden').addClass('flex');
            window.closeTambahUserModal = () => {
                $('#tambahUserModal').addClass('hidden').removeClass('flex');
                $('#guruFields, #siswaFields, #orangtuaFields').addClass('hidden');
                $('#formTambahUser')[0].reset();
                $('#formTambahUser').find('.error-message').remove();
                $('#formTambahUser').find('input, select').removeClass('border-red-500');
            };

            window.openEditUserModal = () => $('#editUserModal').removeClass('hidden').addClass('flex');
            window.closeEditUserModal = () => {
                $('#editUserModal').addClass('hidden').removeClass('flex');
                $('#formEditUser').find('.error-message').remove();
                $('#formEditUser').find('input, select').removeClass('border-red-500');
            };
        });
    </script>
@endsection
