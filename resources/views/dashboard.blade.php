@extends('layouts.app')
@section('title', 'Dasbor')

@push('css')
    {{-- Custom CSS for This Page --}}
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
    </div>

    @if(auth()->user()->role === 'mahasiswa' || auth()->user()->role === 'siswa')
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger border-left-danger fw-semibold shadow-sm" role="alert">
                    ⚠️ <strong>Jangan golput!</strong> Satu suara kamu sangat berarti untuk menentukan masa depan kepengurusan mahasiswa Universitas BSI Kampus Kota Sukabumi.
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->role == 'admin')
        <div class="row">
            @if (Session::has('status'))
                <div class="col-12">
                    <div class="alert alert-success border-left-success" role="alert">
                        {{ session('status') }}
                    </div>
                </div>
            @endif
            <div class="col-12">
                <div class="alert alert-info border-left-info shadow-sm" role="alert">
                    <h5 class="alert-heading font-weight-bold mb-1"><i class="fa-regular fa-building-columns mr-2"></i>Komisi Pemilihan Umum Mahasiswa (KPUM)</h5>
                    Selamat datang di Dasbor E-Voting <strong>Universitas Bina Sarana Informatika Kampus Kota Sukabumi</strong>. Sistem ini dikelola secara terpusat oleh KPUM untuk memfasilitasi pemilihan ketua BEM dan Himpunan Mahasiswa Program Studi (HIMASI, dll.).
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa-regular fa-list-check mr-2"></i>Pemilu Aktif Terkelola</h6>
                    </div>
                    <div class="card-body">
                        @if ($pemilu->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fa-regular fa-inbox fa-3x mb-3 text-gray-300"></i>
                                <p class="mb-0">Belum ada pemilu yang sedang aktif. Silakan buka menu <strong>Kelola Pemilu</strong> untuk menambahkan.</p>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($pemilu as $item)
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <div class="card border-left-primary h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h5 class="card-title font-weight-bold text-primary mb-0">{{ $item->name }}</h5>
                                                    @if ($item->is_private == 1)
                                                        <span class="badge badge-warning"><i class="fa-regular fa-lock mr-1"></i>Private Prodi</span>
                                                    @elseif ($item->is_private == 2)
                                                        <span class="badge badge-info"><i class="fa-regular fa-people-group mr-1"></i>Private UKM</span>
                                                    @else
                                                        <span class="badge badge-success"><i class="fa-regular fa-globe mr-1"></i>Public</span>
                                                    @endif
                                                </div>
                                                <p class="card-text text-muted small">{{ Str::limit($item->description, 80) }}</p>
                                                @if ($item->is_private == 1 && $item->prodis->count() > 0)
                                                    <div class="mb-2">
                                                        <small class="font-weight-bold text-secondary">Sasaran Prodi:</small><br>
                                                        @foreach ($item->prodis as $prodi)
                                                            <span class="badge badge-info mr-1">{{ $prodi->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @elseif ($item->is_private == 2 && $item->ukm)
                                                    <div class="mb-2">
                                                        <small class="font-weight-bold text-secondary">Sasaran UKM:</small><br>
                                                        <span class="badge badge-primary"><i class="fa-regular fa-people-group mr-1"></i>{{ $item->ukm->name }}</span>
                                                    </div>
                                                @endif
                                                <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><i class="fa-regular fa-users mr-1"></i>{{ $item->total_participants }} Peserta</small>
                                                    <a href="{{ route('admin.manage.pemilu.kandidat', $item->slug) }}" class="btn btn-sm btn-outline-primary">Kelola Kandidat</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="text-primary font-weight-bold m-0"><i class="fa-regular fa-box-archive mr-2"></i>Daftar Pemilihan yang Dapat Anda Ikuti</h5>
                                @if (auth()->user()->kelas && auth()->user()->kelas->prodi)
                                    <small class="text-muted">Status: Mahasiswa Prodi <strong>{{ auth()->user()->kelas->prodi->name }}</strong> (Kelas {{ auth()->user()->kelas->name }})</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($pemilu->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fa-regular fa-clipboard-check fa-3x mb-3 text-gray-300"></i>
                                <h5 class="text-gray-600">Tidak Ada Pemilu yang Sedang Aktif</h5>
                                <p class="mb-0">Saat ini belum ada jadwal pemilihan yang dibuka untuk program studi Anda.</p>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($pemilu as $item)
                                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 shadow-sm border-0 border-top-primary">
                                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                                                <h6 class="card-title font-weight-bold mb-0 text-white">{{ $item->name }}</h6>
                                                @if ($item->is_private == 1)
                                                    <span class="badge badge-warning text-dark"><i class="fa-regular fa-lock mr-1"></i>Private Prodi</span>
                                                @elseif ($item->is_private == 2)
                                                    <span class="badge badge-info text-white"><i class="fa-regular fa-people-group mr-1"></i>Private UKM</span>
                                                @else
                                                    <span class="badge badge-light text-primary"><i class="fa-regular fa-globe mr-1"></i>Public</span>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text text-muted">{{ $item->description }}</p>
                                                @if ($item->is_private == 1 && $item->prodis->count() > 0)
                                                    <div class="mb-2">
                                                        <small class="font-weight-bold text-secondary">Program Studi Sasaran:</small><br>
                                                        @foreach ($item->prodis as $prodi)
                                                            <span class="badge badge-info mr-1">{{ $prodi->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @elseif ($item->is_private == 2 && $item->ukm)
                                                    <div class="mb-2">
                                                        <small class="font-weight-bold text-secondary">Unit Kegiatan Mahasiswa:</small><br>
                                                        <span class="badge badge-primary"><i class="fa-regular fa-people-group mr-1"></i>{{ $item->ukm->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white border-top-0 pt-0">
                                                @if ($item->is_private && !empty($item->password))
                                                    <button onclick="verify('{{ $item->slug }}')"
                                                            class="btn btn-primary btn-block shadow-sm"><i
                                                            class="fa-regular fa-key mr-2"></i>Masuk Pemilihan
                                                    </button>
                                                @else
                                                    <a href="{{ route('user.pemilu.join', $item->slug) }}"
                                                       class="btn btn-primary btn-block shadow-sm"><i
                                                            class="fa-regular fa-door-open mr-2"></i>Masuk Pemilihan</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Verifikasi Password Pemilu Private --}}
        <div class="modal fade" id="verifyPasswordModal" tabindex="-1" role="dialog"
             aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="verifyPasswordModalLabel"><i class="fa-regular fa-lock mr-2"></i>Verifikasi Pemilu Private</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="" method="POST" id="verifyPasswordForm" class="form-with-loading">
                        <div class="modal-body">
                            @csrf
                            <p class="text-muted small">Pemilihan ini bersifat privat untuk program studi Anda. Silakan masukkan password pemilihan yang diberikan oleh panitia KPUM.</p>
                            <div class="form-group">
                                <label for="password" class="font-weight-bold">Password Pemilihan</label>
                                <input id="password" type="password" class="form-control" name="password"
                                       placeholder="Masukkan password pemilihan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-loading">
                                <span class="btn-text">Verifikasi & Masuk</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script>
        const verify = (slug) => {
            const verifyUrl = `{{ url('user/pemilu') }}/${slug}/verify-password/join`;
            $('#verifyPasswordForm').attr('action', verifyUrl);
            const myModal = new bootstrap.Modal(document.getElementById('verifyPasswordModal'));
            myModal.show();
        }
    </script>
@endpush
