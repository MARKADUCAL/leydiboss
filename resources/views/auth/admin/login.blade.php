{{-- resources/views/auth/admin/login.blade.php --}}
@extends('layouts.admin-auth')

@push('styles')
    @vite('resources/css/auth/admin/login.css')
@endpush

@section('content')

    <div class="top-bar">
        <a href="{{ url('/') }}" class="btn-back">← Back to Landing Page</a>
    </div>

    <div class="page-center">
        <div class="login-wrapper">

            {{-- ── Alert Banner ── --}}
            @if (!empty($pageData['promo']))
                <div class="promo-banner">{{ $pageData['promo'] }}</div>
            @endif

            <div class="login-card">

                {{-- ── Left Brand Panel ── --}}
                <div class="brand-panel">
                    <div class="brand-logo-wrap">
                        <img src="{{ asset('images/logo.png') }}" alt="Leydi Boss Logo"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <svg style="display:none; width:90px; height:90px;" viewBox="0 0 90 90" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="45" cy="32" rx="18" ry="20" stroke="#e879b0"
                                stroke-width="2.5" />
                            <path d="M20 75 Q45 50 70 75" stroke="#e879b0" stroke-width="2.5" fill="none" />
                            <text x="50%" y="88" text-anchor="middle" font-size="9" fill="#e879b0" font-family="serif"
                                font-style="italic">Leydi Boss</text>
                        </svg>
                    </div>

                    <span class="brand-name">Leydi Boss</span>
                    <span class="brand-sub">Admin Portal</span>

                    @if (!empty($pageData['tagline']))
                        <p class="brand-tagline">{{ $pageData['tagline'] }}</p>
                    @endif

                    {{-- Dashboard Modules --}}
                    @if (!empty($pageData['stats']))
                        <p class="section-heading">✦ Dashboard Modules</p>
                        <div class="stats-list">
                            @foreach ($pageData['stats'] as $stat)
                                <div class="stat-item">
                                    <span class="stat-icon">{{ $stat['icon'] }}</span>
                                    <span class="stat-label">{{ $stat['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Business Hours --}}
                    @if (!empty($pageData['hours']))
                        <div class="hours-block">
                            <p class="hours-title">🕐 Business Hours</p>
                            @foreach ($pageData['hours'] as $hours)
                                <p class="hours-line">{{ $hours }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Right Form Panel ── --}}
                <div class="form-panel">

                    @if (!empty($pageData['welcome']))
                        <p class="form-welcome">{{ $pageData['welcome'] }}</p>
                    @endif

                    <h2 class="form-title">Sign In</h2>

                    @if (session('error'))
                        <div class="alert-error">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="admin@leydiboss.com" required autofocus>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrap">
                                <input type="password" id="password" name="password" placeholder="••••••••" required>
                                <button type="button" class="toggle-pw" onclick="togglePassword('password', 'eye-pw')"
                                    aria-label="Show/hide password">
                                    <svg id="eye-pw" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-submit">Sign In</button>
                    </form>

                    <p class="register-link">
                        Need an admin account? <a href="{{ route('admin.register') }}">Request Access</a>
                    </p>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
            }
        }
    </script>
@endsection
