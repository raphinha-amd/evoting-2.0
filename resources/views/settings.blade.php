@extends('layouts.app')
@section('title', 'Pengaturan')

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
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="card-title text-white">Reset Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-subtitle">
                            Fitur ini akan mereset semua data pengguna kecuali akun admin. Gunakan fitur ini dengan
                            hati-hati karena
                            data yang telah direset tidak dapat dikembalikan!
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.settings.nonactive-user') }}" class="btn btn-outline-danger float-right"
                           data-confirm-delete="true">
                            <i class="fa-regular fa-circle-dot"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-primary">
                        <h4 class="card-title text-white">Reset Kelas</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-subtitle">
                            Fitur ini akan mereset semua data kelas. Gunakan fitur ini dengan
                            hati-hati karena
                            data yang telah direset tidak dapat dikembalikan!
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.settings.nonactive-class') }}" class="btn btn-outline-danger float-right"
                           data-confirm-delete="true">
                            <i class="fa-regular fa-circle-dot"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Custom JS for This Page --}}
@endpush
