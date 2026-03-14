{{-- resources/views/auth/admin/register.blade.php --}}
@extends('layouts.admin-auth')

@push('styles')
    @vite('resources/css/auth/admin/register.css')
@endpush

@section('content')

    <div class="top-bar">
        <a href="{{ url('/') }}" class="btn-back">← Back to Landing Page</a>
    </div>

    <div class="page-center">
        <div class="register-wrapper">

            {{-- ── Alert Banner ── --}}
            @if (!empty($pageData['promo']))
                <div class="promo-banner">{{ $pageData['promo'] }}</div>
            @endif

            <div class="register-card">

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

                    {{-- Admin Perks --}}
                    @if (!empty($pageData['perks']))
                        <p class="section-heading">✦ Admin Access</p>
                        <div class="perks-list">
                            @foreach ($pageData['perks'] as $perk)
                                <div class="perk-item">
                                    <span class="perk-icon">{{ $perk['icon'] }}</span>
                                    <span class="perk-label">{{ $perk['label'] }}</span>
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

                    <h2 class="form-title">Create Account</h2>

                    @if (session('error'))
                        <div class="alert-error">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.register') }}">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                    placeholder="Juan" required autofocus>
                                @error('first_name')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                    placeholder="Dela Cruz" required>
                                @error('last_name')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="admin@leydiboss.com" required>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                placeholder="+63 912 345 6789">
                            @error('phone')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
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

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-wrap">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="••••••••" required>
                                    <button type="button" class="toggle-pw"
                                        onclick="togglePassword('password_confirmation', 'eye-confirm')"
                                        aria-label="Show/hide confirm password">
                                        <svg id="eye-confirm" xmlns="http://www.w3.org/2000/svg" width="18"
                                            height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">Request Access</button>
                    </form>

                    <p class="login-link">
                        Already have an account? <a href="{{ route('admin.login') }}">Sign In</a>
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
