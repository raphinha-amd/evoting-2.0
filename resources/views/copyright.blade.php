@extends('layouts.copyright')
@section('title', 'Hak Cipta & Divisi Teknis')

@push('css')
    <style>
        :root {
            --ev-primary: #4e73df;
            --ev-primary-btn: #375ad2;
            --ev-primary-ink: #223a7a;
            --ev-primary-tint: #eef2ff;
            --ev-canvas: #f5f7fb;
            --ev-surface: #ffffff;
            --ev-ink: #111827;
            --ev-ink-muted: #4b5563;
            --ev-border: #dbe2ef;
            --ev-slate-tint: #f1f5f9;
            --ev-slate-ink: #334155;
        }

        body {
            background-color: var(--ev-canvas);
            color: var(--ev-ink);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .ev-copyright-shell {
            max-width: 980px;
            margin: 0 auto;
            padding: 28px 16px 40px;
        }

        .ev-topbar {
            margin-bottom: 20px;
        }

        .ev-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--ev-ink);
            background-color: var(--ev-surface);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: border-color 150ms ease, background-color 150ms ease, color 150ms ease;
        }

        .ev-back-link:hover {
            color: var(--ev-primary-ink);
            border-color: var(--ev-primary);
            background-color: var(--ev-primary-tint);
            text-decoration: none;
        }

        .ev-back-link:focus-visible {
            outline: 2px solid var(--ev-primary-btn);
            outline-offset: 2px;
        }

        .ev-header-card {
            background-color: var(--ev-surface);
            border: 1px solid var(--ev-border);
            border-top: 4px solid var(--ev-primary);
            border-radius: 16px;
            padding: 28px 24px;
            margin-bottom: 28px;
            text-align: center;
            box-shadow: 0 10px 28px -10px rgba(17, 24, 39, 0.07);
        }

        .ev-header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ev-ink);
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .ev-header-subtitle {
            font-size: 0.9375rem;
            color: var(--ev-ink-muted);
            margin: 0;
        }

        .ev-member-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .ev-member-card {
            background-color: var(--ev-surface);
            border: 1px solid var(--ev-border);
            border-radius: 14px;
            padding: 28px 22px 22px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        .ev-member-card.is-lead {
            border-top: 3px solid var(--ev-primary);
        }

        .ev-member-card:hover {
            border-color: #b8c6e0;
            box-shadow: 0 10px 24px -12px rgba(17, 24, 39, 0.12);
        }

        .ev-member-avatar {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--ev-border);
            margin-bottom: 16px;
        }

        .ev-member-mystery {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            background-color: #1e293b;
            color: #ffffff;
            border: 2px solid var(--ev-border);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
        }

        .ev-member-name {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--ev-ink);
            margin-bottom: 8px;
        }

        .ev-role-tag {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.4;
            padding: 5px 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .ev-role-tag.is-lead {
            background-color: var(--ev-primary-tint);
            color: var(--ev-primary-ink);
        }

        .ev-role-tag.is-support {
            background-color: var(--ev-slate-tint);
            color: var(--ev-slate-ink);
        }

        .ev-social-row {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--ev-border);
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .ev-social-btn {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid var(--ev-border);
            background-color: var(--ev-surface);
            color: var(--ev-ink-muted);
            font-size: 1rem;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
        }

        .ev-social-btn:hover {
            background-color: var(--ev-primary-tint);
            border-color: var(--ev-primary);
            color: var(--ev-primary-ink);
            text-decoration: none;
        }

        .ev-social-btn:focus-visible {
            outline: 2px solid var(--ev-primary-btn);
            outline-offset: 2px;
        }

        .ev-footer {
            text-align: center;
            font-size: 0.8125rem;
            color: var(--ev-ink-muted);
            margin-top: 32px;
        }

        @media (max-width: 767.98px) {
            .ev-copyright-shell {
                padding: 18px 14px 32px;
            }

            .ev-header-card {
                padding: 22px 18px;
            }

            .ev-header-title {
                font-size: 1.25rem;
            }

            .ev-member-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ev-back-link,
            .ev-member-card,
            .ev-social-btn {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')
    <main class="ev-copyright-shell">
        <div class="ev-topbar">
            @php
                $backRoute = auth()->check()
                    ? (auth()->user()->role == 'admin' ? route('admin.dashboard') : route('user.dashboard'))
                    : route('view.login');
            @endphp
            <a href="{{ $backRoute }}" class="ev-back-link">
                <i class="fa-regular fa-arrow-left" aria-hidden="true"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        <header class="ev-header-card">
            <h1 class="ev-header-title">Divisi Teknis &amp; Bantuan Teknis</h1>
            <p class="ev-header-subtitle">
                RuangSuara KPUM &bull; Universitas Bina Sarana Informatika Kampus Kota Sukabumi
            </p>
        </header>

        <section class="ev-member-grid" aria-label="Daftar Anggota Divisi Teknis">
            @foreach ($member as $item)
                @php
                    $isLead = str_contains($item['role'], 'Ketua');
                @endphp
                <article class="ev-member-card {{ $isLead ? 'is-lead' : '' }}">
                    @if (!empty($item['img']))
                        <img
                            src="{{ asset($item['img']) }}"
                            alt="{{ $item['name'] }}"
                            width="112"
                            height="112"
                            class="ev-member-avatar"
                        >
                    @else
                        <div class="ev-member-mystery" aria-hidden="true">
                            <i class="fa-regular fa-user-secret"></i>
                        </div>
                    @endif

                    <h2 class="ev-member-name">{{ $item['name'] }}</h2>

                    <span class="ev-role-tag {{ $isLead ? 'is-lead' : 'is-support' }}">
                        {{ $item['role'] }}
                    </span>

                    <div class="ev-social-row">
                        @if (!empty($item['github']))
                            <a
                                href="{{ $item['github'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ev-social-btn"
                                aria-label="GitHub {{ $item['name'] }}"
                                title="GitHub"
                            >
                                <i class="fa-brands fa-github" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if (!empty($item['website']))
                            <a
                                href="{{ $item['website'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ev-social-btn"
                                aria-label="Website {{ $item['name'] }}"
                                title="Website / Portofolio"
                            >
                                <i class="fa-regular fa-globe" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if (!empty($item['instagram']))
                            <a
                                href="{{ $item['instagram'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ev-social-btn"
                                aria-label="Instagram {{ $item['name'] }}"
                                title="Instagram"
                            >
                                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        <footer class="ev-footer">
            Hak Cipta &copy; {{ now()->year }} RuangSuara KPUM &bull; Universitas Bina Sarana Informatika Kampus Kota Sukabumi
        </footer>
    </main>
@endsection
