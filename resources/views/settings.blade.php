@extends('layouts.app')
@section('title', 'Pengaturan & Pemeliharaan')

@push('css')
    {{-- Custom CSS for This Page --}}
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger border-left-danger shadow-sm" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif

            @if (Session::has('success'))
                <div class="alert alert-success border-left-success shadow-sm" role="alert">
                    <i class="fa-regular fa-circle-check mr-2"></i>{{ Session::get('success') }}
                </div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger border-left-danger shadow-sm" role="alert">
                    <i class="fa-regular fa-circle-xmark mr-2"></i>{{ Session::get('error') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Header Banner --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="font-weight-bold mb-1"><i class="fa-regular fa-sliders mr-2"></i>Pengaturan & Pemeliharaan Sistem</h4>
                            <p class="mb-0 text-white-50">
                                Pusat konfigurasi dan tindakan pemeliharaan data berkala Komisi Pemilihan Umum Mahasiswa (KPUM) Universitas BSI Kampus Kota Sukabumi.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <span class="badge badge-light text-primary px-3 py-2 font-weight-bold">
                                <i class="fa-regular fa-shield-halved mr-1"></i> Mode Admin KPUM
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danger Zone Warning --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-left-warning shadow-sm" role="alert">
                <h6 class="alert-heading font-weight-bold mb-1">
                    <i class="fa-regular fa-triangle-exclamation mr-1"></i> Perhatian: Operasi Pemeliharaan Massal (Danger Zone)
                </h6>
                <small class="d-block text-dark">
                    Fitur pada halaman ini memodifikasi status data dalam jumlah besar sekaligus. Pastikan Anda telah melakukan export atau pencadangan (backup) data hasil rekapitulasi sebelum menjalankan pembersihan berkala semesteran.
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Card Reset Pengguna --}}
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fa-regular fa-users-slash mr-2"></i>Reset Status Pengguna
                    </h6>
                    <span class="badge badge-warning font-weight-bold px-2 py-1">
                        {{ $activeUserCount ?? 0 }} Pengguna Aktif
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            Aksi ini akan menonaktifkan status seluruh akun mahasiswa/pemilih aktif. Akun administrator KPUM tetap aman dan tidak akan terpengaruh.
                        </p>
                        <div class="alert alert-light border small text-muted py-2 mb-3">
                            <i class="fa-regular fa-circle-info mr-1 text-info"></i>
                            Gunakan tindakan ini ketika tahun akademik telah berakhir atau pemilihan periode telah selesai dilaksanakan.
                        </div>
                    </div>
                    <a href="{{ route('admin.settings.nonactive-user') }}" class="btn btn-outline-danger btn-block font-weight-bold"
                       data-confirm-delete="true">
                        <i class="fa-regular fa-power-off mr-1"></i> Nonaktifkan Seluruh Mahasiswa
                    </a>
                </div>
            </div>
        </div>

        {{-- Card Reset Kelas --}}
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fa-regular fa-chalkboard-user mr-2"></i>Reset Status Kelas
                    </h6>
                    <span class="badge badge-warning font-weight-bold px-2 py-1">
                        {{ $activeClassCount ?? 0 }} Kelas Aktif
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            Aksi ini akan menonaktifkan seluruh kelas perkuliahan aktif. Data program studi tetap tersimpan dan tidak akan terhapus.
                        </p>
                        <div class="alert alert-light border small text-muted py-2 mb-3">
                            <i class="fa-regular fa-circle-info mr-1 text-info"></i>
                            Gunakan tindakan ini sebelum mengimpor rombongan belajar (kelas) untuk semester akademik baru.
                        </div>
                    </div>
                    <a href="{{ route('admin.settings.nonactive-class') }}" class="btn btn-outline-danger btn-block font-weight-bold"
                       data-confirm-delete="true">
                        <i class="fa-regular fa-power-off mr-1"></i> Nonaktifkan Seluruh Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
