@extends('layouts.app')
@section('title', 'Kelola User')

@push('css')
    {{-- Custom CSS for This Page --}}
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css') }}">
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
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-users mr-2"></i>Kelola Pengguna (Voter)</h4>
                            <small class="text-muted">Data Pemilih & Civitas Akademika BSI Kampus Kota Sukabumi</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addUserModal" data-toggle="modal" class="btn btn-success mr-1"><i
                                    class="fa-regular fa-plus mr-1"></i> Tambah User</button>
                            <button data-target="#importUserModal" data-toggle="modal" class="btn btn-warning mr-1"><i
                                    class="fa-regular fa-file-import mr-1"></i> Import</button>
                            <button data-target="#exportUserModal" data-toggle="modal" class="btn btn-info mr-1"><i
                                    class="fa-regular fa-file-export mr-1"></i> Export PDF</button>
                            <a href="{{ route('admin.manage.users', ['admin' => true]) }}" class="btn btn-primary"><i
                                    class="fa-regular fa-user-shield mr-1"></i> Admin KPUM</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-1">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Nama Lengkap</th>
                                <th>NIM / Username</th>
                                <th>Password</th>
                                <th>Kelas & Prodi</th>
                                <th>Role</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah User --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addUserModalLabel">Tambah User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.user.add') }}" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="fullname" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" id="fullname"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="username" class="font-weight-bold">NIM / Username <span class="text-danger">*</span></label>
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <input id="create-username" type="text" class="form-control" name="username"
                                        placeholder="Masukkan NIM atau username" required>
                                </div>
                                <div class="col-2">
                                    <button onclick="$('#create-username').val(randomNumber(8))" type="button"
                                        class="btn btn-outline-primary" title="Generate Random"><i class="fa-regular fa-shuffle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <input id="create-password" type="text" class="form-control" name="password"
                                        placeholder="Masukkan password" required>
                                </div>
                                <div class="col-2">
                                    <button onclick="$('#create-password').val(randomNumber(8))" type="button"
                                        class="btn btn-outline-primary" title="Generate Random"><i class="fa-regular fa-shuffle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add-role-select" class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="add-role-select" class="form-control" required style="width: 100%;">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    @if ($r->slug !== 'admin')
                                        <option value="{{ $r->slug }}" data-has-class="{{ $r->has_class ? '1' : '0' }}">{{ $r->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none" id="add-user-group">
                            <label for="add-kelas-select" class="font-weight-bold">Kelas Akademik <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="add-kelas-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->prodi->name ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-loading">
                            <span class="btn-text">Tambah</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit User --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editUserModalLabel">Edit User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="editUserForm" action="" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-fullname" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input id="edit-fullname" type="text" class="form-control" name="fullname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-username" class="font-weight-bold">NIM / Username <span class="text-danger">*</span></label>
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <input id="edit-username" type="text" class="form-control" name="username" required>
                                </div>
                                <div class="col-2">
                                    <button onclick="$('#edit-username').val(randomNumber(8))" type="button"
                                        class="btn btn-outline-primary" title="Generate Random"><i class="fa-regular fa-shuffle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <input id="edit-password" type="text" class="form-control" name="password" required>
                                </div>
                                <div class="col-2">
                                    <button onclick="$('#edit-password').val(randomNumber(8))" type="button"
                                        class="btn btn-outline-primary" title="Generate Random"><i class="fa-regular fa-shuffle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-role-select" class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit-role-select" class="form-control" required style="width: 100%;">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    @if ($r->slug !== 'admin')
                                        <option value="{{ $r->slug }}" data-has-class="{{ $r->has_class ? '1' : '0' }}">{{ $r->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none" id="edit-user-group">
                            <label for="edit-kelas-select" class="font-weight-bold">Kelas Akademik <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="edit-kelas-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->prodi->name ?? '-' }})</option>
                                @endforeach
                            </select>
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

    {{-- Modal Import User --}}
    <div class="modal fade" id="importUserModal" tabindex="-1" role="dialog" aria-labelledby="importUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="importUserModalLabel">Import Data User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.user.import') }}" method="POST" enctype="multipart/form-data"
                    class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="import-role-select" class="font-weight-bold">Role Pengguna <span class="text-danger">*</span></label>
                            <select name="role" id="import-role-select" class="form-control" required style="width: 100%;">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    @if ($r->slug !== 'admin')
                                        <option value="{{ $r->slug }}" data-has-class="{{ $r->has_class ? '1' : '0' }}">{{ $r->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none" id="import-user-group">
                            <label for="import-kelas-select" class="font-weight-bold">Kelas Akademik <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="import-kelas-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->prodi->name ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file" class="font-weight-bold">File CSV / Excel <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv, .xlsx, .xls" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Contoh Format Excel</label>
                            <span class="text-danger small d-block mb-1">*Tanpa header baris</span>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm small">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="40">Kolom 1</th>
                                            <th>Kolom 2 (Nama)</th>
                                            <th>Kolom 3 (NIM/User)</th>
                                            <th>Kolom 4 (Password)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Budi Santoso</td>
                                            <td>12210001</td>
                                            <td>budi123</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-loading">
                            <span class="btn-text">Import</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Export User --}}
    <div class="modal fade" id="exportUserModal" tabindex="-1" role="dialog" aria-labelledby="exportUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="exportUserModalLabel">Export Data User (PDF)</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.user.export') }}" method="GET">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="export-role-select" class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="export-role-select" class="form-control" required style="width: 100%;">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    @if ($r->slug !== 'admin')
                                        <option value="{{ $r->slug }}" data-has-class="{{ $r->has_class ? '1' : '0' }}">{{ $r->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none" id="export-user-group">
                            <label for="export-kelas-select" class="font-weight-bold">Kelas Akademik <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="export-kelas-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->prodi->name ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info"><i class="fa-regular fa-download mr-1"></i> Download PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-1').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('admin.user.data') }}",
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
                            let pageInfo = $('#table-1').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'fullname',
                        orderable: true,
                    },
                    {
                        data: 'username',
                        orderable: false,
                        render: function(data) {
                            return `<code>${data}</code>`;
                        }
                    },
                    {
                        data: 'unencrypted_password',
                        orderable: false,
                    },
                    {
                        data: 'kelas',
                        orderable: false,
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            let prodiName = data.prodi ? data.prodi.name : '';
                            return `<span class="badge badge-info">${data.name}</span> <small class="text-muted d-block">${prodiName}</small>`;
                        }
                    },
                    {
                        data: 'role_data',
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            let name = data ? data.name : row.role;
                            return `<span class="badge badge-primary text-capitalize">${name}</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.user.delete', ':username') }}`;

                            let editBtn =
                                `<button onclick="edit('${row.id}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            let deleteBtn =
                                `<a href="${deleteUrl.replace(':username', row.username)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;
                            return `${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });

            // Handle dynamic role change for showing/hiding kelas
            const handleRoleChange = (selectElem, groupElem) => {
                let selectedOption = $(selectElem).find(':selected');
                let hasClass = selectedOption.data('has-class');
                if (hasClass == '1' || $(selectElem).val() === 'mahasiswa') {
                    $(groupElem).removeClass('d-none');
                } else {
                    $(groupElem).addClass('d-none');
                }
            };

            $('#add-role-select').on('change', function() {
                handleRoleChange(this, '#add-user-group');
            });

            $('#edit-role-select').on('change', function() {
                handleRoleChange(this, '#edit-user-group');
            });

            $('#import-role-select').on('change', function() {
                handleRoleChange(this, '#import-user-group');
            });

            $('#export-role-select').on('change', function() {
                handleRoleChange(this, '#export-user-group');
            });
        });

        const edit = (id) => {
            $.getJSON(`{{ url('admin/manage/users') }}/${id}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.user.update', ':id') }}`;

                $('#editUserForm').attr('action', updateUrl.replace(':id', id));
                $('#edit-fullname').val(data.fullname);
                $('#edit-username').val(data.username);
                $('#edit-password').val(data.unencrypted_password);
                $('#edit-role-select').val(data.role).trigger('change');
                $('#edit-kelas-select').val(data.kelas_id || '').trigger('change');

                const myModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                myModal.show();
            });
        }

        const randomNumber = (length) => {
            let result = '';
            let characters = '0123456789';
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result;
        }
    </script>
@endpush
