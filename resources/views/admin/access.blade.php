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
                @include('components.dashboard.access.datatable')
            </div>

        </main>
    </div>

    {{-- Modal Tambah & Edit Pengguna --}}
    @include('components.dashboard.access.modal.add')
    @include('components.dashboard.access.modal.edit')

    <script>
        $(function() {

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

            // ============ DataTable ============
            const table = $('#accessTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('access.data') }}",
                    dataSrc: 'data'
                },
                pageLength: 5,
                responsive: true,
                ordering: true,
                dom: 'rt<"mt-6 flex flex-col items-center justify-between gap-3 border-t border-black/10 pt-4 sm:flex-row"ip>',
                columns: [{
                        data: 'no',
                        className: 'px-6 py-4 text-black/40'
                    },
                    {
                        data: null,
                        className: 'px-6 py-4',
                        render: function(row) {
                            const initials = row.name.split(' ').map(w => w[0]).join('').substring(
                                0, 2).toUpperCase();
                            return `
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-black text-xs font-semibold text-white plus-jakarta-sans-bold">
                                        ${initials}
                                    </span>
                                    <span class="font-medium plus-jakarta-sans-bold">${row.name}</span>
                                </div>`;
                        }
                    },
                    {
                        data: 'email',
                        className: 'px-6 py-4 text-black/60 plus-jakarta-sans-reguler'
                    },
                    {
                        data: 'role',
                        className: 'px-6 py-4',
                        render: function(data, type) {
                            if (type === 'display') {
                                const label = data ? data.charAt(0).toUpperCase() + data.slice(1) :
                                    '-';
                                return `<span class="rounded-full border border-black/20 px-3 py-1 text-xs text-black/70 plus-jakarta-sans-reguler">${label}</span>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'kelas',
                        className: 'px-6 py-4',
                        orderable: false,
                        render: function(kelas, type, row) {
                            if (type !== 'display') {
                                return (kelas || []).join(' ');
                            }
                            if (!['guru', 'siswa', 'orangtua'].includes(row.role)) {
                                return '<span class="text-black/30">-</span>';
                            }
                            if (!kelas || kelas.length === 0) {
                                return '<span class="text-black/30 italic">Belum ada kelas</span>';
                            }
                            return `<div class="flex flex-wrap gap-1">${
                                kelas.map(k => `<span class="rounded-full bg-black/5 px-2.5 py-1 text-xs text-black/70 plus-jakarta-sans-reguler">${k}</span>`).join('')
                            }</div>`;
                        }
                    },
                    {
                        data: 'id',
                        className: 'px-6 py-4',
                        orderable: false,
                        render: function(id) {
                            return `
                            <div class="flex items-center gap-3 text-xs plus-jakarta-sans-bold">
                                <button type="button" data-id="${id}" class="btn-edit text-black underline decoration-black/30 underline-offset-4 hover:decoration-black">Edit</button>
                                <button type="button" data-id="${id}" class="btn-hapus text-black/50 underline decoration-black/20 underline-offset-4 hover:text-black hover:decoration-black">Hapus</button>
                            </div>`;
                        }
                    },
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada pengguna terdaftar",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    }
                }
            });

            // ============ Search kustom (dengan debounce) ============
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const value = this.value;
                searchTimeout = setTimeout(() => {
                    table.search(value).draw();
                }, 300);
            });

            // ============ Filter Role ============
            $('#filterRole').on('change', function() {
                const value = this.value;
                table.column(3).search(value ? '^' + value + '$' : '', true, false).draw();
            });

            // ============ Top card stats (AJAX) ============
            function loadStats() {
                $.getJSON("{{ route('access.stats') }}", function(res) {
                    $('#statTotal').text(res.total);
                    $('#statSiswa').text(res.siswa);
                    $('#statGuru').text(res.guru);
                    $('#statOrangTua').text(res.orang_tua);

                    const persenSiswa = res.total > 0 ?
                        Math.round((res.siswa / res.total) * 100) :
                        0;

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
                        table.ajax.reload(null, false);
                        loadStats();
                        showNotif(res.message ?? 'Pengguna baru berhasil ditambahkan.');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
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
            $('#accessTable tbody').on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.getJSON(`{{ url('access') }}/${id}/edit`, function(res) {
                    const u = res.user;
                    $('#editUserId').val(u.id);
                    $('#edit_name').val(u.name);
                    $('#edit_email').val(u.email);
                    $('#edit_role').val(u.role);
                    $('#edit_password').val('');
                    $('#edit_password_confirmation').val('');

                    // Guru
                    $('#edit_nip').val(u.nip ?? '');
                    $('#edit_kelasWrapper input[name="kelas_ids[]"]').prop('checked', false);
                    (u.kelas_ids || []).forEach(function(kid) {
                        $(`#edit_kelasWrapper input[value="${kid}"]`).prop('checked', true);
                    });

                    // Siswa
                    $('#edit_id_siswa').val(u.id_siswa ?? '');
                    $('#edit_kelas_id').val(u.kelas_id ?? '');

                    // Orang tua
                    $('#edit_nama_ayah').val(u.nama_ayah ?? '');
                    $('#edit_nama_ibu').val(u.nama_ibu ?? '');
                    $('#edit_no_hp').val(u.no_hp ?? '');
                    $('#edit_siswaAksesWrapper input[name="siswa_ids[]"]').prop('checked', false);
                    (u.siswa_ids || []).forEach(function(sid) {
                        $(`#edit_siswaAksesWrapper input[value="${sid}"]`).prop('checked',
                            true);
                    });

                    toggleRoleFields($('#edit_role'), $('#edit_guruFields'), $('#edit_siswaFields'),
                        $('#edit_orangtuaFields'));

                    $('#formEditUser').find('.error-message').remove();
                    $('#formEditUser').find('input, select').removeClass('border-red-500');

                    openEditUserModal();
                }).fail(function() {
                    showNotif('Gagal memuat data pengguna.', 'error');
                });
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
                        table.ajax.reload(null, false);
                        loadStats();
                        showNotif(res.message ?? 'Data pengguna berhasil diperbarui.');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
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
            $('#accessTable tbody').on('click', '.btn-hapus', function() {
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
                                table.ajax.reload(null, false);
                                loadStats();
                                showNotif(res.message ?? 'Pengguna berhasil dihapus.');
                            },
                            error: function() {
                                showNotif('Gagal menghapus pengguna.', 'error');
                            }
                        });
                    }
                });
            });

            // ============ Modal Helpers ============
            function openTambahUserModal() {
                $('#tambahUserModal').removeClass('hidden').addClass('flex');
            }
            window.openTambahUserModal = openTambahUserModal;

            function closeTambahUserModal() {
                $('#tambahUserModal').addClass('hidden').removeClass('flex');
                $('#guruFields').addClass('hidden');
                $('#siswaFields').addClass('hidden');
                $('#orangtuaFields').addClass('hidden');
                $('#formTambahUser')[0].reset();
                $('#formTambahUser').find('.error-message').remove();
                $('#formTambahUser').find('input, select').removeClass('border-red-500');
            }
            window.closeTambahUserModal = closeTambahUserModal;

            function openEditUserModal() {
                $('#editUserModal').removeClass('hidden').addClass('flex');
            }
            window.openEditUserModal = openEditUserModal;

            function closeEditUserModal() {
                $('#editUserModal').addClass('hidden').removeClass('flex');
                $('#formEditUser').find('.error-message').remove();
                $('#formEditUser').find('input, select').removeClass('border-red-500');
            }
            window.closeEditUserModal = closeEditUserModal;
        });
    </script>
@endsection
