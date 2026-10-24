@extends('layouts.app')
@section('title', 'Kelola Role')

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
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-id-badge mr-2"></i>Kelola Role Pengguna</h4>
                            <small class="text-muted">Manajemen Peran & Hak Akses Pemilih Kampus</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addRoleModal" data-toggle="modal" class="btn btn-success"><i
                                    class="fa-regular fa-plus mr-1"></i> Tambah Role</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-roles">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Nama Role</th>
                                <th>Slug</th>
                                <th class="text-center">Terikat Kelas?</th>
                                <th class="text-center">Hak Suara?</th>
                                <th class="text-center">Total Pengguna</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Role --}}
    <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addRoleModalLabel">Tambah Role</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.roles.add') }}" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="role-name" class="font-weight-bold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="role-name"
                                placeholder="Contoh: Mahasiswa, Dosen, Staf" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="add-has-class" name="has_class" value="1">
                                <label class="custom-control-label font-weight-bold" for="add-has-class">Terikat Kelas Akademik</label>
                                <small class="form-text text-muted">Centang jika pengguna dengan role ini wajib memiliki Kelas & Program Studi (seperti Mahasiswa).</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="add-can-vote" name="can_vote" value="1" checked>
                                <label class="custom-control-label font-weight-bold" for="add-can-vote">Memiliki Hak Suara (Voter)</label>
                                <small class="form-text text-muted">Centang jika role ini diizinkan untuk memberikan suara dalam pemilihan.</small>
                            </div>
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

    {{-- Modal Edit Role --}}
    <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editRoleModalLabel">Edit Role</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="editRoleForm" action="" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-role-name" class="font-weight-bold">Nama Role <span class="text-danger">*</span></label>
                            <input id="edit-role-name" type="text" class="form-control" name="name"
                                placeholder="Masukkan nama role" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="edit-has-class" name="has_class" value="1">
                                <label class="custom-control-label font-weight-bold" for="edit-has-class">Terikat Kelas Akademik</label>
                                <small class="form-text text-muted">Centang jika pengguna role ini terikat dengan kelas & prodi.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="edit-can-vote" name="can_vote" value="1">
                                <label class="custom-control-label font-weight-bold" for="edit-can-vote">Memiliki Hak Suara (Voter)</label>
                                <small class="form-text text-muted">Centang jika role ini berhak memilih.</small>
                            </div>
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
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-roles').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.roles.data') }}",
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
                            let pageInfo = $('#table-roles').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'name',
                        orderable: true,
                    },
                    {
                        data: 'slug',
                        orderable: true,
                        render: function(data) {
                            return `<code>${data}</code>`;
                        }
                    },
                    {
                        data: 'has_class',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-success"><i class="fa-regular fa-check mr-1"></i>Ya</span>` : `<span class="badge badge-secondary">Tidak</span>`;
                        }
                    },
                    {
                        data: 'can_vote',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-primary"><i class="fa-regular fa-check mr-1"></i>Ya</span>` : `<span class="badge badge-danger">Tidak</span>`;
                        }
                    },
                    {
                        data: 'users_count',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return `<span class="badge badge-info">${data || 0} User</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.roles.delete', ':id') }}`;
                            let editBtn =
                                `<button onclick="editRole('${row.id}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            
                            let deleteBtn = '';
                            if (row.slug !== 'admin' && row.slug !== 'mahasiswa') {
                                deleteBtn = `<a href="${deleteUrl.replace(':id', row.id)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;
                            } else {
                                deleteBtn = `<button class="btn btn-sm btn-secondary" disabled title="Role Sistem (Tidak dapat dihapus)"><i class="fa-regular fa-lock"></i></button>`;
                            }

                            return `${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });
        });

        const editRole = (id) => {
            $.getJSON(`{{ url('admin/manage/roles') }}/${id}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.roles.update', ':id') }}`;
                $('#editRoleForm').attr('action', updateUrl.replace(':id', id));
                $('#edit-role-name').val(data.name);
                $('#edit-has-class').prop('checked', !!data.has_class);
                $('#edit-can-vote').prop('checked', !!data.can_vote);

                const myModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
                myModal.show();
            });
        }
    </script>
@endpush
