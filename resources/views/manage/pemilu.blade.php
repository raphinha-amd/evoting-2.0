@extends('layouts.app')
@section('title', 'Kelola Pemilu')

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
                            <h4 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-check-to-slot mr-2"></i>Kelola Pemilu (KPUM)</h4>
                            <small class="text-muted">Komisi Pemilihan Umum Mahasiswa - Universitas BSI Kampus Kota Sukabumi</small>
                        </div>
                        <div class="col-auto">
                            <button data-target="#addPemiluModal" data-toggle="modal" class="btn btn-success"><i
                                    class="fa-regular fa-plus mr-1"></i> Tambah Pemilu</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover w-100 nowrap" id="table-1">
                        <thead class="thead-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Pemilu</th>
                            <th>Tipe & Sasaran Prodi</th>
                            <th class="text-center">Total Peserta</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="220">Aksi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pemilu --}}
    <div class="modal fade" id="addPemiluModal" tabindex="-1" role="dialog" aria-labelledby="addPemiluModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="addPemiluModalLabel">Tambah Pemilu Baru</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.manage.pemilu.add') }}" method="POST" id="add-pemilu-form"
                      class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nama Pemilu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name"
                                   placeholder="Contoh: Pemilihan Ketua BEM / Pemilihan Ketua HIMASI" required>
                        </div>
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" cols="30" rows="3"
                                      placeholder="Deskripsi singkat mengenai pemilihan ini" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold d-block">Tipe Pemilihan <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input add-radio-scope" type="radio" name="is_private" id="add-radio-private-no"
                                       value="0" checked>
                                <label class="custom-control-label" for="add-radio-private-no">
                                    <strong class="text-success"><i class="fa-regular fa-globe mr-1"></i> Public</strong> (Semua Mahasiswa)
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input add-radio-scope" type="radio" name="is_private" id="add-radio-private-prodi"
                                       value="1">
                                <label class="custom-control-label" for="add-radio-private-prodi">
                                    <strong class="text-warning"><i class="fa-regular fa-graduation-cap mr-1"></i> Private Prodi</strong> (Khusus Prodi + Password)
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input add-radio-scope" type="radio" name="is_private" id="add-radio-private-ukm"
                                       value="2">
                                <label class="custom-control-label" for="add-radio-private-ukm">
                                    <strong class="text-primary"><i class="fa-regular fa-people-group mr-1"></i> Private UKM</strong> (Khusus Anggota UKM + Password)
                                </label>
                            </div>
                        </div>

                        {{-- Bagian Khusus Private Prodi --}}
                        <div id="add-private-prodi-container" class="border rounded p-3 bg-light mb-3 d-none">
                            <h6 class="font-weight-bold text-primary"><i class="fa-regular fa-graduation-cap mr-1"></i> Sasaran Program Studi</h6>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Pilih Program Studi yang Berhak Memilih <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach ($prodis as $p)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input add-prodi-cb" name="prodi_ids[]"
                                                       id="add-prodi-{{ $p->id }}" value="{{ $p->id }}">
                                                <label class="custom-control-label" for="add-prodi-{{ $p->id }}">
                                                    {{ $p->name }} <span class="badge badge-secondary">{{ $p->degree ?? '-' }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Hanya mahasiswa dari Program Studi yang dicentang yang dapat berpartisipasi.</small>
                            </div>
                        </div>

                        {{-- Bagian Khusus Private UKM --}}
                        <div id="add-private-ukm-container" class="border rounded p-3 bg-light mb-3 d-none">
                            <h6 class="font-weight-bold text-primary"><i class="fa-regular fa-people-group mr-1"></i> Sasaran Unit Kegiatan Mahasiswa (UKM)</h6>
                            <div class="form-group mb-0">
                                <label for="add-ukm-id" class="font-weight-bold">Pilih UKM <span class="text-danger">*</span></label>
                                <select name="ukm_id" id="add-ukm-id" class="form-control">
                                    <option value="">-- Pilih Unit Kegiatan Mahasiswa --</option>
                                    @foreach ($ukms as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} {{ $u->code ? '('.$u->code.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya mahasiswa yang terdaftar sebagai anggota UKM ini yang dapat berpartisipasi.</small>
                            </div>
                        </div>

                        {{-- Bagian Password Pemilu Private --}}
                        <div id="add-password-container" class="form-group border rounded p-3 bg-light mb-3 d-none">
                            <label for="password" class="font-weight-bold"><i class="fa-regular fa-key mr-1 text-warning"></i> Password Akses Pemilu <small class="text-muted font-weight-normal">(Opsional)</small></label>
                            <input type="text" name="password" id="password" class="form-control"
                                   placeholder="Kosongkan jika tidak membutuhkan password tambahan">
                            <small class="text-muted">Jika diisi, pemilih wajib memasukkan password ini. Jika dikosongkan, mahasiswa yang berhak dapat langsung memilih.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="status" value="1"
                                       id="add-status-checkbox" checked>
                                <label class="custom-control-label font-weight-bold" for="add-status-checkbox">
                                    Aktifkan Pemilu (Dapat dilihat pemilih)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-loading">
                            <span class="btn-text">Tambah Pemilu</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Pemilu --}}
    <div class="modal fade" id="editPemiluModal" tabindex="-1" role="dialog" aria-labelledby="editPemiluModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="editPemiluModalLabel">Edit Pemilu</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="POST" id="edit-pemilu-form" class="form-with-loading">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nama Pemilu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit-name"
                                   placeholder="Masukkan nama pemilu" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-description" class="font-weight-bold">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="edit-description" cols="30" rows="3"
                                      required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold d-block">Tipe Pemilihan <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input edit-radio-scope" type="radio" name="is_private"
                                       id="edit-radio-private-no" value="0">
                                <label class="custom-control-label" for="edit-radio-private-no">
                                    <strong class="text-success"><i class="fa-regular fa-globe mr-1"></i> Public</strong> (Semua Mahasiswa)
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input edit-radio-scope" type="radio" name="is_private"
                                       id="edit-radio-private-prodi" value="1">
                                <label class="custom-control-label" for="edit-radio-private-prodi">
                                    <strong class="text-warning"><i class="fa-regular fa-graduation-cap mr-1"></i> Private Prodi</strong> (Khusus Prodi + Password)
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input edit-radio-scope" type="radio" name="is_private"
                                       id="edit-radio-private-ukm" value="2">
                                <label class="custom-control-label" for="edit-radio-private-ukm">
                                    <strong class="text-primary"><i class="fa-regular fa-people-group mr-1"></i> Private UKM</strong> (Khusus Anggota UKM + Password)
                                </label>
                            </div>
                        </div>

                        {{-- Bagian Khusus Private Prodi di Edit --}}
                        <div id="edit-private-prodi-container" class="border rounded p-3 bg-light mb-3 d-none">
                            <h6 class="font-weight-bold text-primary"><i class="fa-regular fa-graduation-cap mr-1"></i> Sasaran Program Studi</h6>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Pilih Program Studi yang Berhak Memilih <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach ($prodis as $p)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input edit-prodi-checkbox" name="prodi_ids[]"
                                                       id="edit-prodi-{{ $p->id }}" value="{{ $p->id }}">
                                                <label class="custom-control-label" for="edit-prodi-{{ $p->id }}">
                                                    {{ $p->name }} <span class="badge badge-secondary">{{ $p->degree ?? '-' }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Khusus Private UKM di Edit --}}
                        <div id="edit-private-ukm-container" class="border rounded p-3 bg-light mb-3 d-none">
                            <h6 class="font-weight-bold text-primary"><i class="fa-regular fa-people-group mr-1"></i> Sasaran Unit Kegiatan Mahasiswa (UKM)</h6>
                            <div class="form-group mb-0">
                                <label for="edit-ukm-id" class="font-weight-bold">Pilih UKM <span class="text-danger">*</span></label>
                                <select name="ukm_id" id="edit-ukm-id" class="form-control">
                                    <option value="">-- Pilih Unit Kegiatan Mahasiswa --</option>
                                    @foreach ($ukms as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} {{ $u->code ? '('.$u->code.')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Bagian Password Pemilu Private di Edit --}}
                        <div id="edit-password-container" class="form-group border rounded p-3 bg-light mb-3 d-none">
                            <label for="edit-password" class="font-weight-bold"><i class="fa-regular fa-key mr-1 text-warning"></i> Password Akses Pemilu <small class="text-muted font-weight-normal">(Opsional)</small></label>
                            <input type="text" name="password" id="edit-password" class="form-control"
                                   placeholder="Kosongkan jika tidak membutuhkan password tambahan">
                            <small class="text-muted">Jika diisi, pemilih wajib memasukkan password ini. Jika dikosongkan, mahasiswa yang berhak dapat langsung memilih.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="status" value="1"
                                       id="edit-status-checkbox">
                                <label class="custom-control-label font-weight-bold" for="edit-status-checkbox">
                                    Aktifkan Pemilu
                                </label>
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

    {{-- Modal Hasil Voting --}}
    <div class="modal fade" id="resultVotingModal" tabindex="-1" role="dialog"
         aria-labelledby="resultVotingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="resultTitle">Detail & Statistik Voting</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-5 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header font-weight-bold">Partisipasi Pemilih</div>
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <canvas id="statusVote"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header font-weight-bold">Perolehan Suara Kandidat</div>
                                <div class="card-body">
                                    <canvas id="statistikVote"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header font-weight-bold">Perolehan Suara per Kelas</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm table-hover">
                                            <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th>Nama Kelas</th>
                                                <th width="150">Jumlah Suara</th>
                                            </tr>
                                            </thead>
                                            <tbody id="resultTableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="" id="exportResultPdf" class="btn btn-danger"><i class="fa-regular fa-file-pdf mr-1"></i> Export PDF</a>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"><\/script>');
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#table-1').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.pemilu.data') }}",
                },
                order: [
                    [0, 'desc']
                ],
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            let pageInfo = $('#table-1').DataTable().page.info();
                            return meta.row + 1 + pageInfo.start;
                        }
                    },
                    {
                        data: 'name',
                        orderable: true,
                        render: function(data, type, row) {
                            let desc = row.description ? `<small class="text-muted d-block">${row.description.substring(0, 60)}...</small>` : '';
                            return `<strong>${data}</strong>${desc}`;
                        }
                    },
                    {
                        data: 'is_private',
                        orderable: false,
                        render: function (data, type, row) {
                            if (data == 1) {
                                let prodiBadges = '';
                                if (row.prodis && row.prodis.length > 0) {
                                    prodiBadges = row.prodis.map(p => `<span class="badge badge-info mr-1">${p.name}</span>`).join('');
                                } else {
                                    prodiBadges = '<small class="text-muted">Semua Prodi</small>';
                                }
                                return `<span class="badge badge-warning"><i class="fa-regular fa-graduation-cap mr-1"></i>Private Prodi</span><div class="mt-1">${prodiBadges}</div>`;
                            } else if (data == 2) {
                                let ukmName = row.ukm ? row.ukm.name : 'UKM';
                                return `<span class="badge badge-primary"><i class="fa-regular fa-people-group mr-1"></i>Private UKM</span><div class="mt-1"><span class="badge badge-info">${ukmName}</span></div>`;
                            } else {
                                return `<span class="badge badge-success"><i class="fa-regular fa-globe mr-1"></i>Public (Semua Mahasiswa)</span>`;
                            }
                        }
                    },
                    {
                        data: 'total_participants',
                        className: 'text-center',
                        orderable: true,
                        render: function (data) {
                            return `<span class="badge badge-primary font-weight-bold">${data || 0} Mahasiswa</span>`;
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: true,
                        render: function (data) {
                            return data == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function (data, type, row) {
                            const deleteUrl = `{{ route('admin.manage.pemilu.delete', ':slug') }}`;
                            const kandidatUrl = `{{ route('admin.manage.pemilu.kandidat', ':slug') }}`;
                            const syncUrl = `{{ route('admin.pemilu.data.sync', ':slug') }}`;

                            let kandidatBtn = `<a href="${kandidatUrl.replace(':slug', row.slug)}" class="btn btn-sm btn-info mr-1" title="Kelola Kandidat"><i class="fa-regular fa-user-tie"></i></a>`;
                            let resultBtn = `<button onclick="result('${row.slug}')" class="btn btn-sm btn-warning mr-1" title="Lihat Hasil Voting"><i class="fa-regular fa-chart-pie"></i></button>`;
                            let syncBtn = `<a href="${syncUrl.replace(':slug', row.slug)}" data-method="POST" class="btn btn-sm btn-secondary mr-1 btn-sync" data-slug="${row.slug}" title="Sinkron Total Peserta"><i class="fa-regular fa-sync"></i></a>`;
                            let editBtn = `<button onclick="edit('${row.slug}')" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="fa-regular fa-edit"></i></button>`;
                            let deleteBtn = `<a href="${deleteUrl.replace(':slug', row.slug)}" class="btn btn-sm btn-danger" data-confirm-delete="true" title="Hapus"><i class="fa-regular fa-trash"></i></a>`;

                            return `${kandidatBtn}${resultBtn}${syncBtn}${editBtn}${deleteBtn}`;
                        }
                    }
                ],
            });

            // Toggle container private pemilu
            function toggleAddContainers() {
                let val = $('input[name="is_private"]:checked', '#add-pemilu-form').val();
                if (val == '1') {
                    $('#add-private-prodi-container').removeClass('d-none');
                    $('#add-private-ukm-container').addClass('d-none');
                    $('#add-password-container').removeClass('d-none');
                } else if (val == '2') {
                    $('#add-private-prodi-container').addClass('d-none');
                    $('#add-private-ukm-container').removeClass('d-none');
                    $('#add-password-container').removeClass('d-none');
                } else {
                    $('#add-private-prodi-container').addClass('d-none');
                    $('#add-private-ukm-container').addClass('d-none');
                    $('#add-password-container').addClass('d-none');
                }
            }

            function toggleEditContainers() {
                let val = $('input[name="is_private"]:checked', '#edit-pemilu-form').val();
                if (val == '1') {
                    $('#edit-private-prodi-container').removeClass('d-none');
                    $('#edit-private-ukm-container').addClass('d-none');
                    $('#edit-password-container').removeClass('d-none');
                } else if (val == '2') {
                    $('#edit-private-prodi-container').addClass('d-none');
                    $('#edit-private-ukm-container').removeClass('d-none');
                    $('#edit-password-container').removeClass('d-none');
                } else {
                    $('#edit-private-prodi-container').addClass('d-none');
                    $('#edit-private-ukm-container').addClass('d-none');
                    $('#edit-password-container').addClass('d-none');
                }
            }

            $('.add-radio-scope').on('change', toggleAddContainers);
            $('.edit-radio-scope').on('change', toggleEditContainers);

            // Handler sinkron peserta
            $(document).on('click', '.btn-sync', function (e) {
                e.preventDefault();
                let slug = $(this).data('slug');
                let form = $('<form>', {
                    action: `{{ url('admin/manage/pemilu') }}/${slug}/sync`,
                    method: 'POST'
                }).append('@csrf');
                $('body').append(form);
                form.submit();
            });

            $('#add-pemilu-form').on('submit', function () {
                if (!$('#add-status-checkbox').is(':checked')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'status',
                        value: '0'
                    }).appendTo('#add-pemilu-form');
                }
            });
        });

        const edit = (slug) => {
            $.getJSON(`{{ url('admin/manage/pemilu') }}/${slug}/data`, (data) => {
                const updateUrl = `{{ route('admin.manage.pemilu.edit', ':slug') }}`;
                $('#edit-pemilu-form').attr('action', updateUrl.replace(':slug', slug));

                $("#edit-name").val(data.name);
                $("#edit-description").val(data.description);

                $('.edit-prodi-checkbox').prop('checked', false);
                $('#edit-ukm-id').val('');

                $(`input[name="is_private"][value="${data.is_private}"]`, '#edit-pemilu-form').prop('checked', true);

                if (data.is_private == 1) {
                    $('#edit-private-prodi-container').removeClass('d-none');
                    $('#edit-private-ukm-container').addClass('d-none');
                    $('#edit-password-container').removeClass('d-none');
                    $("#edit-password").val(data.password);

                    if (data.prodis && data.prodis.length > 0) {
                        data.prodis.forEach(p => {
                            $(`#edit-prodi-${p.id}`).prop('checked', true);
                        });
                    }
                } else if (data.is_private == 2) {
                    $('#edit-private-prodi-container').addClass('d-none');
                    $('#edit-private-ukm-container').removeClass('d-none');
                    $('#edit-password-container').removeClass('d-none');
                    $("#edit-password").val(data.password);
                    $('#edit-ukm-id').val(data.ukm_id || '');
                } else {
                    $('#edit-private-prodi-container').addClass('d-none');
                    $('#edit-private-ukm-container').addClass('d-none');
                    $('#edit-password-container').addClass('d-none');
                    $("#edit-password").val('');
                }

                if (data.status == 1) {
                    $('#edit-status-checkbox').prop('checked', true);
                } else {
                    $('#edit-status-checkbox').prop('checked', false);
                }

                $('#edit-pemilu-form').off('submit').on('submit', function () {
                    if (!$('#edit-status-checkbox').is(':checked')) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'status',
                            value: '0'
                        }).appendTo('#edit-pemilu-form');
                    }
                });

                const myModal = new bootstrap.Modal(document.getElementById('editPemiluModal'));
                myModal.show();
            });
        }

        let pieChartIstance;
        let barChartIstance;

        const result = (slug) => {
            const ctx = document.getElementById('statusVote');

            if (pieChartIstance) {
                pieChartIstance.destroy();
            }

            const pieChart = (data) => {
                pieChartIstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [data.voted, data.not_voted],
                            backgroundColor: [
                                '#4e73df',
                                '#e74a3b',
                            ],
                            label: 'Status'
                        }],
                        labels: [
                            'Sudah Voting',
                            'Belum Voting',
                        ],
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'right',
                        },
                    }
                });
            }

            const barChart = (data) => {
                const ctx = document.getElementById('statistikVote');

                if (barChartIstance) {
                    barChartIstance.destroy();
                }

                barChartIstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total Suara',
                            data: data.data,
                            backgroundColor: '#4e73df',
                            borderColor: '#2e59d9',
                            borderWidth: 1.5,
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0
                                }
                            }]
                        },
                    }
                });
            }

            $.getJSON(`{{ url('admin/manage/pemilu') }}/${slug}/result/data`, (data) => {
                const exportUrl = `{{ route('admin.manage.pemilu.export.result', ':slug') }}`;
                $('#exportResultPdf').attr('href', exportUrl.replace(':slug', slug));

                $('#resultTitle').text(`Detail Voting Pemilu | Total Peserta: ${data.total_users}`);
                pieChart(data.pie_charts);
                barChart(data.bar_charts);

                $('#resultTableBody').empty();
                data.votes_per_class.forEach((kelas, i) => {
                    $('<tr>').appendTo('#resultTableBody').append(
                        `<td class="text-center">${i + 1}</td>`,
                        `<td><strong>${kelas.class_name}</strong></td>`,
                        `<td><span class="badge badge-primary">${kelas.votes_count} Suara</span></td>`
                    );
                });

                const myModal = new bootstrap.Modal(document.getElementById('resultVotingModal'));
                myModal.show();
            });
        }
    </script>
@endpush
