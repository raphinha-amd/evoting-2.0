@extends('layouts.app')
@section('title', 'Bilik Suara - ' . $pemilu->name)

@push('css')
    <style>
        .candidate-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .candidate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
        .candidate-img-wrapper {
            position: relative;
            background: #f8f9fc;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .candidate-img {
            max-height: 100%;
            width: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .candidate-card:hover .candidate-img {
            transform: scale(1.03);
        }
        .candidate-number-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }
        .vision-mission-content {
            white-space: pre-wrap;
            line-height: 1.8;
            font-size: 0.95rem;
            color: #333;
        }
    </style>
@endpush

@php
    $gridClass = 'col-12 col-md-6 col-lg-4';
    if ($kandidat->count() === 1) {
        $gridClass = 'col-12 col-md-8 col-lg-6 mx-auto';
    } elseif ($kandidat->count() === 2) {
        $gridClass = 'col-12 col-md-6 col-lg-5 mx-auto';
    } elseif ($kandidat->count() >= 4) {
        $gridClass = 'col-12 col-md-6 col-lg-3';
    }
@endphp

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

    {{-- Ballot Header Banner --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <span class="badge badge-light text-primary font-weight-bold px-3 py-1 mb-2">
                                <i class="fa-regular fa-check-to-slot mr-1"></i> Surat Suara Elektronik KPUM
                            </span>
                            <h3 class="font-weight-bold mb-1">{{ $pemilu->name }}</h3>
                            <p class="mb-2 text-white-50">
                                {{ $pemilu->description }}
                            </p>
                            <div>
                                @if ($pemilu->is_private == 1)
                                    <span class="badge badge-warning text-dark font-weight-bold mr-1">
                                        <i class="fa-regular fa-graduation-cap mr-1"></i> Private Prodi:
                                        {{ $pemilu->prodis->pluck('name')->join(', ') ?: 'Prodi Terpilih' }}
                                    </span>
                                @elseif ($pemilu->is_private == 2)
                                    <span class="badge badge-info text-white font-weight-bold mr-1">
                                        <i class="fa-regular fa-people-group mr-1"></i> Private UKM:
                                        {{ $pemilu->ukm?->name ?? 'UKM' }}
                                    </span>
                                @else
                                    <span class="badge badge-light text-success font-weight-bold mr-1">
                                        <i class="fa-regular fa-globe mr-1"></i> Public (Semua Mahasiswa)
                                    </span>
                                @endif
                                <span class="badge badge-light text-primary font-weight-bold">
                                    <i class="fa-regular fa-shield-halved mr-1"></i> Asas Luber Jurdil
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light text-primary font-weight-bold shadow-sm">
                                <i class="fa-regular fa-arrow-left mr-1"></i> Kembali ke Dasbor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Voting Instructions --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info border-left-info shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa-regular fa-circle-info fa-2x mr-3 text-info"></i>
                    <div>
                        <strong class="d-block">Petunjuk Pemilihan:</strong>
                        <small>
                            Pelajari visi dan misi setiap kandidat dengan menekan tombol <strong>Visi & Misi</strong>.
                            Tentukan pilihan Anda dengan menekan <strong>Pilih Kandidat</strong>. Suara yang sudah disimpan bersifat final dan tidak dapat diubah kembali.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Candidate Cards Grid --}}
    <div class="row">
        @forelse ($kandidat as $item)
            <div class="{{ $gridClass }} mb-4">
                <div class="card h-100 shadow-sm border-0 candidate-card">
                    {{-- Image & Candidate Number --}}
                    <div class="candidate-img-wrapper">
                        <div class="candidate-number-badge bg-primary text-white">
                            {{ sprintf('%02d', $loop->iteration) }}
                        </div>
                        @if ($item->image)
                            <img src="{{ asset('storage/upload/' . $item->image) }}" alt="{{ $item->name }}" class="candidate-img">
                        @else
                            <div class="text-center py-5">
                                <i class="fa-regular fa-user-tie fa-5x text-gray-300"></i>
                                <span class="d-block text-muted mt-2 font-weight-bold">Foto Belum Tersedia</span>
                            </div>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <small class="text-uppercase font-weight-bold text-muted letter-spacing-1">Calon Ketua / Kandidat</small>
                            <h5 class="font-weight-bold text-primary mb-3 mt-1">{{ $item->name }}</h5>
                        </div>

                        <div>
                            <button onclick="visiMisi('{{ $pemilu->slug }}', '{{ $item->id }}')"
                                    class="btn btn-outline-primary btn-block mb-2 font-weight-bold">
                                <i class="fa-regular fa-book-open mr-1"></i> Visi & Misi
                            </button>
                            <button onclick="voting('{{ $pemilu->slug }}', '{{ $item->id }}', '{{ addslashes($item->name) }}', '{{ $loop->iteration }}')"
                                    class="btn btn-success btn-block font-weight-bold shadow-sm py-2">
                                <i class="fa-regular fa-check mr-1"></i> Pilih Kandidat Ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <i class="fa-regular fa-users-slash fa-4x text-gray-300 mb-3"></i>
                        <h5 class="font-weight-bold text-dark">Belum Ada Kandidat Terdaftar</h5>
                        <p class="text-muted">Panitia KPUM belum menambahkan data kandidat untuk pemilihan ini.</p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary font-weight-bold">
                            Kembali ke Dasbor
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal Visi & Misi --}}
    <div class="modal fade" id="visionMissionModal" tabindex="-1" role="dialog"
         aria-labelledby="visionMissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <small class="text-muted text-uppercase font-weight-bold">Visi & Misi Kandidat</small>
                        <h5 class="modal-title font-weight-bold text-primary" id="visionMissionModalLabel"></h5>
                    </div>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="card bg-light border-0 p-3">
                        <div class="vision-mission-content" id="visionMissionParagraph"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Voting --}}
    <div class="modal fade" id="votingModal" tabindex="-1" role="dialog" aria-labelledby="votingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold text-white" id="votingModalLabel">
                        <i class="fa-regular fa-check-to-slot mr-2"></i>Konfirmasi Suara Anda
                    </h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="POST" id="voteForm" class="form-with-loading">
                    @csrf
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 text-success shadow-sm" style="width: 70px; height: 70px;">
                                <i class="fa-regular fa-vote-yea fa-2x"></i>
                            </span>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1">Apakah Anda yakin dengan pilihan ini?</h5>
                        <p class="text-muted small">Anda akan memberikan hak suara Anda kepada:</p>

                        <div class="alert alert-light border py-3 my-3">
                            <span class="badge badge-primary px-3 py-1 font-weight-bold mb-2" id="confirmCandidateNumber"></span>
                            <h4 class="text-primary font-weight-bold mb-0" id="confirmCandidateName"></h4>
                        </div>

                        <div class="alert alert-warning py-2 small mb-0 text-left">
                            <i class="fa-regular fa-triangle-exclamation mr-1"></i>
                            <strong>Peringatan:</strong> Setelah Anda menekan tombol konfirmasi, suara Anda akan dicatat secara permanen dan tidak dapat diubah atau dibatalkan.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-secondary font-weight-bold" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success font-weight-bold px-4 btn-loading">
                            <span class="btn-text"><i class="fa-regular fa-check mr-1"></i> Ya, Simpan Pilihan Saya</span>
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
    <script>
        const visiMisi = (slug, id) => {
            $.getJSON(`{{ url('user/pemilu') }}/${slug}/kandidat/${id}/data`, (data) => {
                $('#visionMissionParagraph').text(data.vision_mission || 'Belum ada deskripsi visi & misi yang diinputkan.');
                $('#visionMissionModalLabel').text(data.name);

                const myModal = new bootstrap.Modal(document.getElementById('visionMissionModal'));
                myModal.show();
            });
        };

        const voting = (slug, id, candidateName, candidateNumber) => {
            const urlVote = `{{ route('user.pemilu.vote', [':slug', ':id']) }}`;
            $('#voteForm').attr('action', urlVote.replace(':id', id).replace(':slug', slug));
            $('#confirmCandidateName').text(candidateName);
            $('#confirmCandidateNumber').text(`Kandidat No. ${candidateNumber}`);

            const myModal = new bootstrap.Modal(document.getElementById('votingModal'));
            myModal.show();
        };
    </script>
@endpush
