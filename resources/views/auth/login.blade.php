@extends('layouts.auth')
@section('title', 'Login')

@push('css')
    <style>
        :root {
            --ev-primary: #4e73df;
            --ev-primary-btn: #375ad2;
            --ev-primary-btn-hover: #2b4bc0;
            --ev-primary-ink: #223a7a;
            --ev-primary-tint: #f3f6fd;
            --ev-canvas: #f5f7fb;
            --ev-surface: #ffffff;
            --ev-ink: #111827;
            --ev-ink-muted: #4b5563;
            --ev-border: #dbe2ef;
            --ev-danger-bg: #fef2f2;
            --ev-danger-border: #fecaca;
            --ev-danger-ink: #991b1b;
        }

        body {
            background-color: var(--ev-canvas) !important;
            background-image: none !important;
            color: var(--ev-ink);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            padding: 24px 16px;
        }

        .ev-login-shell {
            width: 100%;
            max-width: 880px;
            margin: 0 auto;
        }

        .ev-login-card {
            background: var(--ev-surface);
            border: 1px solid var(--ev-border);
            border-top: 4px solid var(--ev-primary);
            border-radius: 16px;
            box-shadow: 0 12px 32px -8px rgba(17, 24, 39, 0.08);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
        }

        .ev-brand-panel {
            background-color: var(--ev-primary-tint);
            border-right: 1px solid var(--ev-border);
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }

        .ev-brand-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto 0;
        }

        .ev-brand-logo {
            width: 168px;
            height: 168px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .ev-brand-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--ev-primary-ink);
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }

        .ev-brand-subtitle {
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--ev-ink-muted);
            max-width: 240px;
            margin: 0;
        }

        .ev-brand-meta {
            font-size: 0.75rem;
            color: var(--ev-ink-muted);
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px solid var(--ev-border);
            width: 100%;
        }

        .ev-form-panel {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .ev-form-header {
            margin-bottom: 28px;
        }

        .ev-form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ev-ink);
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .ev-form-title sup {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--ev-primary-ink);
            margin-left: 2px;
        }

        .ev-form-desc {
            font-size: 0.9375rem;
            color: var(--ev-ink-muted);
            margin: 0;
        }

        .ev-field {
            margin-bottom: 18px;
        }

        .ev-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--ev-ink);
            margin-bottom: 6px;
        }

        .ev-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .ev-input {
            width: 100%;
            min-height: 46px;
            padding: 10px 14px;
            font-size: 0.9375rem;
            color: var(--ev-ink);
            background-color: var(--ev-surface);
            border: 1px solid var(--ev-border);
            border-radius: 10px;
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        .ev-input.has-toggle {
            padding-right: 48px;
        }

        .ev-input::placeholder {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .ev-input:hover {
            border-color: #b4c2db;
        }

        .ev-input:focus {
            outline: none;
            border-color: var(--ev-primary-btn);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
        }

        .ev-toggle-password {
            position: absolute;
            right: 4px;
            width: 40px;
            height: 40px;
            min-width: 44px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--ev-ink-muted);
            cursor: pointer;
            transition: color 150ms ease, background-color 150ms ease;
        }

        .ev-toggle-password:hover {
            color: var(--ev-ink);
            background-color: var(--ev-primary-tint);
        }

        .ev-toggle-password:focus-visible {
            outline: 2px solid var(--ev-primary-btn);
            outline-offset: 2px;
        }

        .ev-alert {
            background-color: var(--ev-danger-bg);
            border: 1px solid var(--ev-danger-border);
            color: var(--ev-danger-ink);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.875rem;
            margin-bottom: 18px;
        }

        .ev-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .ev-submit-btn {
            width: 100%;
            min-height: 48px;
            padding: 12px 20px;
            margin-top: 6px;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #ffffff;
            background-color: var(--ev-primary-btn);
            border: 1px solid var(--ev-primary-btn);
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 150ms ease, border-color 150ms ease, transform 150ms ease;
        }

        .ev-submit-btn:hover:not(:disabled) {
            background-color: var(--ev-primary-btn-hover);
            border-color: var(--ev-primary-btn-hover);
        }

        .ev-submit-btn:active:not(:disabled) {
            transform: translateY(1px);
        }

        .ev-submit-btn:focus-visible {
            outline: 2px solid var(--ev-primary-ink);
            outline-offset: 2px;
        }

        .ev-submit-btn:disabled {
            opacity: 0.75;
            cursor: not-allowed;
        }

        .ev-footer-note {
            text-align: center;
            font-size: 0.8125rem;
            color: whitesmoke;
            margin-top: 18px;
        }

        @media (max-width: 767.98px) {
            body {
                padding: 16px 12px;
            }

            .ev-login-card {
                grid-template-columns: 1fr;
            }

            .ev-brand-panel {
                border-right: none;
                border-bottom: 1px solid var(--ev-border);
                padding: 28px 24px 24px;
            }

            .ev-brand-logo {
                width: 96px;
                height: 96px;
                margin-bottom: 12px;
            }

            .ev-brand-meta {
                display: none;
            }

            .ev-form-panel {
                padding: 28px 22px 32px;
            }

            .ev-form-header {
                margin-bottom: 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ev-input,
            .ev-toggle-password,
            .ev-submit-btn {
                transition: none;
            }

            .ev-submit-btn:active:not(:disabled) {
                transform: none;
            }
        }
    </style>
@endpush

@section('content')
    <main class="ev-login-shell">
        <div class="ev-login-card">
            <section class="ev-brand-panel" aria-label="Identitas KPUM UBSI">
                <div class="ev-brand-body">
                    <img
                        src="{{ asset('assets/img/logo_kpum.png') }}"
                        alt="Logo KPUM UBSI Sukabumi"
                        width="168"
                        height="168"
                        fetchpriority="high"
                        class="ev-brand-logo"
                    >
                    <h2 class="ev-brand-title">KPUM UBSI</h2>
                    <p class="ev-brand-subtitle">
                        Universitas Bina Sarana Informatika Kampus Kota Sukabumi
                    </p>
                </div>
                <p class="ev-brand-meta mb-0">
                    Portal Pemungutan Suara Elektronik
                </p>
            </section>

            <section class="ev-form-panel" aria-label="Formulir Login">
                <div class="ev-form-header">
                    <h1 class="ev-form-title">
                        @if ($adminLoggedIn)
                            Login Admin
                        @else
                            RuangSuara
                        @endif
                    </h1>
                    <p class="ev-form-desc">
                        @if ($adminLoggedIn)
                            Masuk ke panel pengelolaan pemilihan KPUM.
                        @else
                            Masuk menggunakan akun mahasiswa untuk memberikan suara.
                        @endif
                    </p>
                </div>

                <form action="{{ route('post.login', $adminLoggedIn) }}" method="POST" class="form-with-loading">
                    @csrf

                    <div class="ev-field">
                        <label for="username" class="ev-label">Username</label>
                        <div class="ev-input-wrap">
                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                class="ev-input"
                                placeholder="Masukkan username Anda"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="ev-field">
                        <label for="password" class="ev-label">Password</label>
                        <div class="ev-input-wrap">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="ev-input has-toggle"
                                placeholder="Masukkan password Anda"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                type="button"
                                class="ev-toggle-password"
                                id="togglePasswordBtn"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <i class="fas fa-eye" id="togglePasswordIcon" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="ev-alert" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="ev-alert" role="alert">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <button type="submit" class="ev-submit-btn btn-loading">
                        <span class="btn-text">{{ $adminLoggedIn ? 'Masuk sebagai Admin' : 'Masuk ke RuangSuara' }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </section>
        </div>

        <p class="ev-footer-note">
            RuangSuara KPUM &bull; Universitas Bina Sarana Informatika Kampus Kota Sukabumi
        </p>
    </main>
@endsection

@push('js')
    <script>
        (function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (!toggleBtn || !passwordInput || !toggleIcon) return;

            toggleBtn.addEventListener('click', function () {
                const isHidden = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                toggleBtn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                toggleIcon.classList.toggle('fa-eye', !isHidden);
                toggleIcon.classList.toggle('fa-eye-slash', isHidden);
            });
        })();
    </script>
@endpush
