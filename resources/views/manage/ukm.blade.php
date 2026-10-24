@extends('layouts.app')
@section('title', 'Kelola Unit Kegiatan Mahasiswa (UKM)')

@push('css')
    {{-- Custom CSS for This Page --}}
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/datatables.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger border-left-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif

            @if (Session::has('success'))
                <div class="alert alert-success border-left-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (Session::has('warning'))
                <div class="alert alert-warning border-left-warning" role="alert">
                    {{ Session::get('warning') }}
                </div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger border-left-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-people-group mr-2"></i>Kelola Unit Kegiatan Mahasiswa (UKM)</h4>
                            <small class="text-muted">Universitas Bina Sarana Informatika Kampus Kota Sukabumi</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addUkmModal" data-toggle="modal" class="btn btn-success">
                                <i class="fa-regular fa-plus mr-1"></i> Tambah UKM
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-ukm">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Kode</th>
                                <th>Nama UKM</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Anggota</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah UKM --}}
    <div class="modal fade" id="addUkmModal" tabindex="-1" role="dialog" aria-labelledby="addUkmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addUkmModalLabel">Tambah UKM Baru</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.ukm.add') }}" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nama UKM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Contoh: BSI Computer Club (BSICC)" required>
                        </div>
                        <div class="form-group">
                            <label for="code" class="font-weight-bold">Kode Singkatan</label>
                            <input type="text" class="form-control" name="code" id="code"
                                placeholder="Contoh: BSICC">
                        </div>
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="3"
                                placeholder="Deskripsi atau bidang kegiatan UKM"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-loading">
                            <span class="btn-text">Simpan</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit UKM --}}
    <div class="modal fade" id="editUkmModal" tabindex="-1" role="dialog" aria-labelledby="editUkmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editUkmModalLabel">Edit UKM</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="POST" id="editUkmForm" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nama UKM <span class="text-danger">*</span></label>
                            <input id="edit-name" type="text" class="form-control" name="name"
                                placeholder="Nama UKM" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-code" class="font-weight-bold">Kode Singkatan</label>
                            <input id="edit-code" type="text" class="form-control" name="code"
                                placeholder="Contoh: BSICC">
                        </div>
                        <div class="form-group">
                            <label for="edit-description" class="font-weight-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" id="edit-description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-loading">
                            <span class="btn-text">Ubah</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kelola Anggota UKM --}}
    <div class="modal fade" id="manageMembersModal" tabindex="-1" role="dialog" aria-labelledby="manageMembersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title font-weight-bold text-primary" id="manageMembersModalLabel">
                            <i class="fa-regular fa-users mr-1"></i> Kelola Anggota UKM: <span id="member-modal-ukm-name"></span>
                        </h5>
                        <small class="text-muted">Kelola data pemilih terdaftar khusus untuk pemilihan berbasis UKM ini</small>
                    </div>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Nav Tabs for Import / Manual Add --}}
                    <div class="card mb-3 border-left-primary shadow-sm">
                        <div class="card-header bg-white py-2">
                            <ul class="nav nav-pills card-header-pills" id="memberTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold" id="import-tab" data-toggle="pill" href="#tab-import" role="tab">
                                        <i class="fa-regular fa-file-excel mr-1 text-success"></i> Import Anggota (Excel)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" id="manual-tab" data-toggle="pill" href="#tab-manual" role="tab">
                                        <i class="fa-regular fa-user-plus mr-1 text-primary"></i> Tambah Manual (NIM)
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body py-3">
                            <div class="tab-content" id="memberTabContent">
                                {{-- Import Excel Tab --}}
                                <div class="tab-pane fade show active" id="tab-import" role="tabpanel">
                                    <form action="" method="POST" id="importMembersForm" enctype="multipart/form-data" class="form-with-loading">
                                        @csrf
                                        <div class="alert alert-info py-2 mb-2 font-weight-normal">
                                            <i class="fa-regular fa-circle-info mr-1"></i>
                                            <strong>Petunjuk Import:</strong> Cukup gunakan file Excel (<strong>.xlsx</strong> / <strong>.xls</strong> / <strong>.csv</strong>) dengan 1 kolom berisi daftar <strong>NIM mahasiswa</strong>.
                                            Sistem otomatis memverifikasi dan menghubungkan akun mahasiswa ke UKM ini.
                                        </div>
                                        <div class="form-row align-items-center">
                                            <div class="col-md-8 mb-2 mb-md-0">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="file" id="memberExcelFile" accept=".xlsx,.xls,.csv" required>
                                                    <label class="custom-file-label" for="memberExcelFile" id="memberExcelLabel">Pilih file Excel...</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-success btn-block btn-loading">
                                                    <span class="btn-text"><i class="fa-regular fa-file-import mr-1"></i> Upload & Import</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                {{-- Manual Add Tab --}}
                                <div class="tab-pane fade" id="tab-manual" role="tabpanel">
                                    <form action="javascript:void(0);" method="POST" id="addMemberManualForm" onsubmit="submitManualMember(event); return false;">
                                        @csrf
                                        <div class="form-row align-items-center">
                                            <div class="col-md-8 mb-2 mb-md-0">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa-regular fa-id-card"></i></span>
                                                    </div>
                                                    <input type="text" name="username" id="manualMemberNim" class="form-control" placeholder="Masukkan NIM mahasiswa lalu tekan Enter / Tambah" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary btn-block font-weight-bold" id="btnSubmitManualMember">
                                                    <span class="btn-text"><i class="fa-regular fa-plus mr-1"></i> Tambah Anggota</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="manualMemberAlert" class="mt-2" style="display: none;"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Members Table --}}
                    <div class="card shadow-sm">
                        <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fa-regular fa-list-check mr-1"></i> Daftar Anggota Terdaftar</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reloadMembersTable()">
                                <i class="fa-regular fa-arrows-rotate mr-1"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body p-2 p-md-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover w-100 nowrap" id="table-ukm-members">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" width="40">No</th>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th>Kelas</th>
                                            <th class="text-center" width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script>
        let currentUkmId = null;
        let membersTable = null;

        $(document).ready(function() {
            // Main UKM DataTable
            $('#table-ukm').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.ukm.data') }}",
                },
                order: [
                    [2, 'asc']
                ],
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            let pageInfo = $('#table-ukm').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'code',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-primary">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'name',
                        orderable: true,
                        render: function(data, type, row) {
                            return `<strong>${data}</strong>`;
                        }
                    },
                    {
                        data: 'description',
                        orderable: false,
                        render: function(data) {
                            return data ? (data.length > 50 ? data.substr(0, 50) + '...' : data) : '<span class="text-muted italic">-</span>';
                        }
                    },
                    {
                        data: 'users_count',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return `<span class="badge badge-info py-1 px-2"><i class="fa-regular fa-users mr-1"></i> ${data || 0} Mahasiswa</span>`;
                        }
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-success">Aktif</span>` : `<span class="badge badge-secondary">Nonaktif</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.ukm.delete', ':id') }}`;
                            let membersBtn =
                                `<button onclick="openMembersModal('${row.id}', '${row.name.replace(/'/g, "\\'")}')" class="btn btn-sm btn-info mr-1" title="Kelola Anggota"><i class="fa-regular fa-users mr-1"></i> Anggota</button>`;
                            let editBtn =
                                `<button onclick="editUkm('${row.id}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            let deleteBtn =
                                `<a href="${deleteUrl.replace(':id', row.id)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;
                            return `${membersBtn}${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });

            // Update custom file label when file selected
            $('#memberExcelFile').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $('#memberExcelLabel').text(fileName || 'Pilih file Excel...');
            });

            // Autofocus NIM input when switching to manual tab
            $('a[data-toggle="pill"][href="#tab-manual"]').on('shown.bs.tab', function () {
                $('#manualMemberNim').focus();
            });

            // Handle Enter key on NIM input
            $('#manualMemberNim').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    submitManualMember(e);
                }
            });

            // AJAX Submit Manual Member Add
            $('#addMemberManualForm').on('submit', function (e) {
                e.preventDefault();
                submitManualMember(e);
                return false;
            });

            @if (session('open_ukm_id'))
                setTimeout(function() {
                    openMembersModal("{{ session('open_ukm_id') }}", "{{ session('open_ukm_name', 'UKM') }}");
                }, 300);
            @endif
        });

        const showManualAlert = (type, message) => {
            const $alert = $('#manualMemberAlert');
            const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';

            $alert.removeClass('alert-success alert-warning alert-danger')
                  .addClass(`alert ${alertClass} py-2 px-3 small font-weight-bold shadow-sm`)
                  .html(`<i class="fa-regular ${icon} mr-1"></i> ${message}`)
                  .stop(true, true).fadeIn();

            setTimeout(() => {
                $alert.fadeOut();
            }, 6000);
        };

        const submitManualMember = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            const $btn = $('#btnSubmitManualMember');
            const $input = $('#manualMemberNim');
            const nim = $input.val().trim();

            if (!currentUkmId) {
                showManualAlert('danger', 'ID UKM tidak valid. Silakan buka kembali modal anggota.');
                return false;
            }

            if (!nim) {
                showManualAlert('warning', 'Harap masukkan NIM mahasiswa terlebih dahulu.');
                $input.focus();
                return false;
            }

            $btn.prop('disabled', true);
            $btn.find('.btn-text').addClass('d-none');
            $btn.find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: `{{ url('admin/manage/ukm') }}/${currentUkmId}/members/add`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    username: nim
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    $btn.prop('disabled', false);
                    $btn.find('.btn-text').removeClass('d-none');
                    $btn.find('.spinner-border').addClass('d-none');
                    $input.val('').focus();

                    showManualAlert('success', res.message || 'Anggota berhasil ditambahkan!');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }

                    reloadMembersTable();
                    if ($.fn.DataTable.isDataTable('#table-ukm')) {
                        $('#table-ukm').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false);
                    $btn.find('.btn-text').removeClass('d-none');
                    $btn.find('.spinner-border').addClass('d-none');

                    let errMsg = 'Gagal menambahkan anggota.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }

                    showManualAlert('danger', errMsg);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Perhatian',
                            text: errMsg,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    }

                    $input.select().focus();
                }
            });

            return false;
        };

        const editUkm = (id) => {
            $.getJSON(`{{ url('admin/manage/ukm') }}/${id}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.ukm.update', ':id') }}`;
                $('#editUkmForm').attr('action', updateUrl.replace(':id', id));
                $('#edit-name').val(data.name);
                $('#edit-code').val(data.code);
                $('#edit-description').val(data.description);

                const myModal = new bootstrap.Modal(document.getElementById('editUkmModal'));
                myModal.show();
            });
        };

        const openMembersModal = (ukmId, ukmName) => {
            currentUkmId = ukmId;
            $('#member-modal-ukm-name').text(ukmName);
            $('#manualMemberNim').val('');
            $('#manualMemberAlert').hide().empty();

            // Update import form action
            $('#importMembersForm').attr('action', `{{ url('admin/manage/ukm') }}/${ukmId}/members/import`);

            // Initialize or reload members table
            if ($.fn.DataTable.isDataTable('#table-ukm-members')) {
                $('#table-ukm-members').DataTable().ajax.url(`{{ url('admin/manage/ukm') }}/${ukmId}/members/data`).load();
            } else {
                initMembersTable(ukmId);
            }

            const myModal = new bootstrap.Modal(document.getElementById('manageMembersModal'));
            myModal.show();
        };

        const initMembersTable = (ukmId) => {
            membersTable = $('#table-ukm-members').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ url('admin/manage/ukm') }}/${ukmId}/members/data`,
                },
                order: [
                    [1, 'asc']
                ],
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            let pageInfo = $('#table-ukm-members').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'username',
                        orderable: true,
                        render: function(data) {
                            return `<span class="badge badge-light border font-weight-bold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'fullname',
                        orderable: true,
                    },
                    {
                        data: 'kelas',
                        orderable: false,
                        render: function(data) {
                            return data && data.prodi ? data.prodi.name : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'kelas',
                        orderable: false,
                        render: function(data) {
                            return data ? `<span class="badge badge-secondary">${data.name}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button onclick="removeMember('${row.id}', '${row.fullname.replace(/'/g, "\\'")}')" class="btn btn-sm btn-danger" title="Keluarkan dari UKM"><i class="fa-regular fa-user-xmark"></i></button>`;
                        }
                    }
                ]
            });
        };

        const reloadMembersTable = () => {
            if (currentUkmId && $.fn.DataTable.isDataTable('#table-ukm-members')) {
                $('#table-ukm-members').DataTable().ajax.reload(null, false);
            }
        };

        const removeMember = (userId, fullname) => {
            const doDelete = () => {
                $.ajax({
                    url: `{{ url('admin/manage/ukm') }}/${currentUkmId}/members/${userId}/remove`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Anggota berhasil dikeluarkan dari UKM.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        reloadMembersTable();
                        if ($.fn.DataTable.isDataTable('#table-ukm')) {
                            $('#table-ukm').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal mengeluarkan anggota dari UKM.'
                            });
                        } else {
                            alert('Gagal mengeluarkan anggota dari UKM.');
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluarkan Anggota?',
                    text: `Apakah Anda yakin ingin mengeluarkan ${fullname} dari UKM ini?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Keluarkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin mengeluarkan ${fullname} dari UKM ini?`)) {
                    doDelete();
                }
            }
        };
    </script>
@endpush
