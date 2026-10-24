@extends('layouts.app')
@section('title', 'Kelola Program Studi')

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
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-graduation-cap mr-2"></i>Kelola Program Studi</h4>
                            <small class="text-muted">Universitas Bina Sarana Informatika Kampus Kota Sukabumi</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addProdiModal" data-toggle="modal" class="btn btn-success"><i
                                    class="fa-regular fa-plus mr-1"></i> Tambah Prodi</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-prodi">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Nama Program Studi</th>
                                <th>Kode</th>
                                <th>Jenjang</th>
                                <th class="text-center">Total Kelas</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Prodi --}}
    <div class="modal fade" id="addProdiModal" tabindex="-1" role="dialog" aria-labelledby="addProdiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addProdiModalLabel">Tambah Program Studi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.prodi.add') }}" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nama Program Studi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Contoh: Sistem Informasi" required>
                        </div>
                        <div class="form-group">
                            <label for="code" class="font-weight-bold">Kode Prodi</label>
                            <input type="text" class="form-control" name="code" id="code"
                                placeholder="Contoh: SI-S1">
                        </div>
                        <div class="form-group">
                            <label for="degree" class="font-weight-bold">Jenjang Pendidikan</label>
                            <select name="degree" id="degree" class="form-control">
                                <option value="S1">S1 (Strata 1)</option>
                                <option value="D3">D3 (Diploma 3)</option>
                                <option value="S2">S2 (Magister)</option>
                            </select>
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

    {{-- Modal Edit Prodi --}}
    <div class="modal fade" id="editProdiModal" tabindex="-1" role="dialog" aria-labelledby="editProdiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editProdiModalLabel">Edit Program Studi</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="editProdiForm" action="" method="POST" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nama Program Studi <span class="text-danger">*</span></label>
                            <input id="edit-name" type="text" class="form-control" name="name"
                                placeholder="Masukkan nama program studi" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-code" class="font-weight-bold">Kode Prodi</label>
                            <input id="edit-code" type="text" class="form-control" name="code"
                                placeholder="Contoh: SI-S1">
                        </div>
                        <div class="form-group">
                            <label for="edit-degree" class="font-weight-bold">Jenjang Pendidikan</label>
                            <select name="degree" id="edit-degree" class="form-control">
                                <option value="S1">S1 (Strata 1)</option>
                                <option value="D3">D3 (Diploma 3)</option>
                                <option value="S2">S2 (Magister)</option>
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
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-prodi').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.prodi.data') }}",
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
                            let pageInfo = $('#table-prodi').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'name',
                        orderable: true,
                    },
                    {
                        data: 'code',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-info">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'degree',
                        orderable: true,
                        render: function(data) {
                            return data ? `<span class="badge badge-primary">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'kelas_count',
                        className: 'text-center',
                        orderable: true,
                        render: function(data) {
                            return `<span class="badge badge-secondary">${data || 0} Kelas</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.prodi.delete', ':id') }}`;
                            let editBtn =
                                `<button onclick="editProdi('${row.id}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            let deleteBtn =
                                `<a href="${deleteUrl.replace(':id', row.id)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;
                            return `${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });
        });

        const editProdi = (id) => {
            $.getJSON(`{{ url('admin/manage/prodi') }}/${id}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.prodi.update', ':id') }}`;
                $('#editProdiForm').attr('action', updateUrl.replace(':id', id));
                $('#edit-name').val(data.name);
                $('#edit-code').val(data.code);
                $('#edit-degree').val(data.degree || 'S1');

                const myModal = new bootstrap.Modal(document.getElementById('editProdiModal'));
                myModal.show();
            });
        }
    </script>
@endpush
