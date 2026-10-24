@extends('layouts.app')
@section('title', 'Kelola Kelas')

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
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-chalkboard-user mr-2"></i>Kelola Kelas</h4>
                            <small class="text-muted">Daftar Kelas Akademik per Program Studi</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addClassModal" data-toggle="modal" class="btn btn-success mr-1"><i
                                    class="fa-regular fa-plus mr-1"></i> Tambah Kelas</button>
                            <button data-target="#importClassModal" data-toggle="modal" class="btn btn-warning"><i
                                    class="fa-regular fa-file-import mr-1"></i> Import</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-1">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Nama Kelas</th>
                                <th>Program Studi</th>
                                <th>Slug</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Kelas --}}
    <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addClassModalLabel">Tambah Kelas</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.class.add') }}" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="prodi_id" class="font-weight-bold">Program Studi <span class="text-danger">*</span></label>
                            <select name="prodi_id" id="prodi_id" class="form-control" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodis as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->degree ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Contoh: 12.1A.01 / 2024-SI-A" required>
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

    {{-- Modal Edit Kelas --}}
    <div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="editClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editClassModalLabel">Edit Kelas</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="editClassForm" action="" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-prodi-id" class="font-weight-bold">Program Studi <span class="text-danger">*</span></label>
                            <select name="prodi_id" id="edit-prodi-id" class="form-control" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodis as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->degree ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input id="edit-name" type="text" class="form-control" name="name"
                                placeholder="Masukkan nama kelas" required>
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

    {{-- Modal Import Kelas --}}
    <div class="modal fade" id="importClassModal" tabindex="-1" role="dialog" aria-labelledby="importClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="importClassModalLabel">Import Data Kelas</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.class.import') }}" enctype="multipart/form-data" method="POST"
                    class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="import-prodi-id" class="font-weight-bold">Program Studi Sasaran <span class="text-danger">*</span></label>
                            <select name="prodi_id" id="import-prodi-id" class="form-control" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodis as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->degree ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file" class="font-weight-bold">File CSV / Excel <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" class="form-control"
                                accept=".csv, .xlsx, .xls" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Contoh Format Excel</label>
                            <span class="text-danger small d-block mb-1">*Kolom 2 berisi Nama Kelas, tanpa header baris</span>
                            <table class="table table-bordered table-sm small">
                                <thead>
                                    <tr class="bg-light">
                                        <th width="40">Kolom 1 (No)</th>
                                        <th>Kolom 2 (Nama Kelas)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>12.1A.01</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>12.1B.01</td>
                                    </tr>
                                </tbody>
                            </table>
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
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-1').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.class.data') }}",
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
                        data: 'name',
                        orderable: true,
                    },
                    {
                        data: 'prodi',
                        orderable: false,
                        render: function(data) {
                            return data ? `<span class="badge badge-info">${data.name}</span>` : `<span class="text-muted">-</span>`;
                        }
                    },
                    {
                        data: 'slug',
                        orderable: false,
                        render: function(data) {
                            return `<code>${data}</code>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.class.delete', ':id') }}`;

                            let editBtn =
                                `<button onclick="edit('${row.id}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            let deleteBtn =
                                `<a href="${deleteUrl.replace(':id', row.id)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;
                            return `${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });
        });

        const edit = (id) => {
            $.getJSON(`{{ url('admin/manage/class') }}/${id}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.class.update', ':id') }}`;

                $('#editClassForm').attr('action', updateUrl.replace(':id', id));
                $('#edit-name').val(data.name);
                $('#edit-prodi-id').val(data.prodi_id || '');

                const myModal = new bootstrap.Modal(document.getElementById('editClassModal'));
                myModal.show();
            });
        }
    </script>
@endpush
