@extends('layouts.app')
@section('title', 'Profil Pengguna')

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

    <div class="row">
        {{-- Profile Summary Card --}}
        <div class="col-12 col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <form action="{{ route('profile.update.image') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                        @csrf
                        <div class="position-relative d-inline-block mb-3">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/upload/' . $user->profile_picture) }}"
                                     alt="{{ $user->fullname }}"
                                     class="img-fluid rounded-circle border shadow-sm"
                                     style="width: 130px; height: 130px; object-fit: cover;">
                            @else
                                <img src="{{ asset('assets/img/avatar-1.png') }}"
                                     alt="Avatar Default"
                                     class="img-fluid rounded-circle border shadow-sm"
                                     style="width: 130px; height: 130px; object-fit: cover;">
                            @endif
                            <input onchange="document.getElementById('avatarForm').submit()" class="d-none" type="file" name="image" id="image-input" accept="image/*">
                            <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute shadow"
                                    style="bottom: 0; right: 5px; width: 36px; height: 36px; padding: 0;"
                                    id="change-button" title="Ubah Foto Profil">
                                <i class="fa-regular fa-camera"></i>
                            </button>
                        </div>
                    </form>

                    <h5 class="font-weight-bold text-dark mb-1">{{ $user->fullname }}</h5>
                    <p class="text-muted mb-2 font-weight-bold">
                        <span class="badge badge-light border text-dark px-2 py-1">
                            <i class="fa-regular fa-id-card mr-1"></i>{{ $user->username }}
                        </span>
                    </p>

                    <div class="mb-3">
                        @if ($user->role === 'admin')
                            <span class="badge badge-danger px-3 py-1 font-weight-bold">
                                <i class="fa-regular fa-shield-halved mr-1"></i> Administrator KPUM
                            </span>
                        @elseif ($user->role === 'mahasiswa')
                            <span class="badge badge-primary px-3 py-1 font-weight-bold">
                                <i class="fa-regular fa-graduation-cap mr-1"></i> Mahasiswa Aktif
                            </span>
                        @else
                            <span class="badge badge-secondary px-3 py-1 font-weight-bold">
                                {{ ucfirst($user->role) }}
                            </span>
                        @endif
                    </div>

                    <hr>

                    {{-- Academic Details --}}
                    <div class="text-left small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Institusi:</span>
                            <span class="font-weight-bold text-right">Universitas BSI Sukabumi</span>
                        </div>
                        @if ($user->kelas)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Program Studi:</span>
                                <span class="font-weight-bold text-primary text-right">{{ $user->kelas->prodi?->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Kelas:</span>
                                <span class="badge badge-info">{{ $user->kelas->name }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status Akun:</span>
                            <span class="text-success font-weight-bold"><i class="fa-solid fa-circle text-success mr-1" style="font-size: 8px;"></i>Aktif</span>
                        </div>
                    </div>

                    @if ($user->ukms && $user->ukms->count() > 0)
                        <hr>
                        <div class="text-left">
                            <small class="font-weight-bold text-secondary d-block mb-1">
                                <i class="fa-regular fa-people-group mr-1"></i> Keanggotaan UKM:
                            </small>
                            @foreach ($user->ukms as $ukm)
                                <span class="badge badge-primary mr-1 mb-1">{{ $ukm->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Edit Form Card --}}
        <div class="col-12 col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fa-regular fa-user-pen mr-2"></i>Edit Informasi Profil
                    </h5>
                    <small class="text-muted">Perbarui data nama, username (NIM), atau kata sandi akun Anda</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" class="form-with-loading">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                                        </div>
                                        <input value="{{ $user->fullname }}" name="fullname" type="text" class="form-control"
                                            placeholder="Masukkan nama lengkap" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">NIM / Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa-regular fa-id-badge"></i></span>
                                        </div>
                                        <input value="{{ $user->username }}" name="username" type="text" class="form-control"
                                            placeholder="Masukkan NIM atau username" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="font-weight-bold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-regular fa-lock"></i></span>
                                </div>
                                <input value="{{ $user->unencrypted_password }}" type="password" id="password"
                                    class="form-control" name="password" placeholder="Masukkan password baru" required>
                                <div class="input-group-append">
                                    <button onclick="seePassword()" type="button" class="btn btn-outline-secondary" title="Lihat/Sembunyikan Password">
                                        <i class="fa-regular fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Pastikan kata sandi Anda terjaga kerahasiaannya untuk melindungi hak suara pemilihan.</small>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Hak Akses / Role</label>
                                    <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Kelas & Program Studi</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $user->kelas ? $user->kelas->name . ' (' . ($user->kelas->prodi?->name ?? 'Prodi N/A') . ')' : 'Tidak terikat kelas' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold btn-loading shadow-sm">
                                <span class="btn-text"><i class="fa-regular fa-floppy-disk mr-1"></i> Simpan Perubahan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
    <script>
        const seePassword = () => {
            const passwordInput = document.querySelector('#password');
            const passwordIcon = document.querySelector('#password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        };

        const changeButton = document.getElementById('change-button');
        const imageInput = document.getElementById('image-input');

        if (changeButton && imageInput) {
            changeButton.addEventListener('click', () => {
                imageInput.click();
            });
        }
    </script>
@endpush
