@extends('layouts.copyright')
@section('title', 'Hak Cipta & Divisi Teknis')

@section('content')
    <div class="container py-3">
        {{-- Back Button --}}
        <div class="py-2">
            @php
                $backRoute = auth()->check()
                    ? (auth()->user()->role == 'admin' ? route('admin.dashboard') : route('user.dashboard'))
                    : route('view.login');
            @endphp
            <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-regular fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Header Card --}}
        <div class="card bg-dark border-secondary my-3 text-center shadow-sm">
            <div class="card-body py-4">
                <h4 class="font-weight-bold text-white mb-1">Divisi Teknis & Bantuan Teknis</h4>
                <p class="text-muted mb-0 small">KPUM Universitas Bina Sarana Informatika Kampus Kota Sukabumi</p>
            </div>
        </div>

        {{-- Members Grid --}}
        <div class="row justify-content-center">
            @foreach ($member as $item)
                <div class="col-12 col-md-4 mb-4">
                    <div class="card bg-dark border-secondary h-100 text-center shadow-sm">
                        <div class="card-body p-4 d-flex flex-column align-items-center">
                            <img src="{{ asset($item['img']) }}" alt="{{ $item['name'] }}"
                                class="rounded-circle mb-3 border border-secondary shadow-sm"
                                style="width: 130px; height: 130px; object-fit: cover;">

                            <h5 class="font-weight-bold text-white mb-1">{{ $item['name'] }}</h5>

                            <div class="mb-3">
                                <span class="badge {{ str_contains($item['role'], 'Ketua') ? 'badge-primary' : 'badge-info' }} px-3 py-1">
                                    {{ $item['role'] }}
                                </span>
                            </div>

                            <div class="mt-auto pt-3 border-top border-secondary w-100">
                                @if (!empty($item['github']))
                                    <a href="{{ $item['github'] }}" target="_blank" class="btn btn-sm btn-secondary mx-1" title="GitHub">
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                @endif
                                @if (!empty($item['website']))
                                    <a href="{{ $item['website'] }}" target="_blank" class="btn btn-sm btn-info mx-1" title="Website / Portofolio">
                                        <i class="fa-regular fa-globe"></i>
                                    </a>
                                @endif
                                @if (!empty($item['instagram']))
                                    <a href="{{ $item['instagram'] }}" target="_blank" class="btn btn-sm btn-danger mx-1" title="Instagram">
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="text-center text-muted small my-4">
            Hak Cipta &copy; {{ now()->year }} KPUM Universitas BSI Kampus Kota Sukabumi
        </div>
    </div>
@endsection
