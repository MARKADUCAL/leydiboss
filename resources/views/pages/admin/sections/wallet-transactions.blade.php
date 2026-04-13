{{-- resources/views/pages/admin/sections/wallet-transactions.blade.php --}}
@extends('layouts.admin')

@section('title', $title ?? 'Wallet Transactions')

@push('styles')
    @vite(['resources/css/auth/admin/wallet-transactions.css'])
@endpush

@section('content')
    <div class="wt-page">
        <div class="wt-page__heading">
            <h1>Wallet Transactions</h1>
            <span class="wt-badge">Jobs</span>
        </div>

        <p class="wt-page__sub">
            Select a role and dispatch background jobs to generate mock transactions or recalculate balances.
            Jobs run asynchronously — make sure a queue worker is running.
        </p>

        {{-- ── Flash alerts ─────────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="wt-alert wt-alert--success" id="wt-flash">
                <span class="wt-alert__icon">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="wt-alert wt-alert--error">
                <span class="wt-alert__icon">⚠️</span>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="wt-cards">
            {{-- ── Job 1 · Generate Mock Transactions ────────────────────────── --}}
            <div class="wt-card">
                <div class="wt-card__header">
                    <div class="wt-card__icon wt-card__icon--purple">🎲</div>
                    <div>
                        <p class="wt-card__title">Generate Transactions</p>
                        <p class="wt-card__desc">Create random debit &amp; credit entries</p>
                    </div>
                </div>

                <div class="wt-card__body">
                    <form id="form-generate"
                          action="{{ route('admin.wallet-transactions.generate') }}"
                          method="POST">
                        @csrf

                        {{-- Role picker --}}
                        <div class="wt-form-group">
                            <label>Target Role</label>
                            <div class="wt-role-group">
                                @php
                                    $roles = [
                                        'customer'    => ['🧑', 'Customer'],
                                        'admin'       => ['🛡️', 'Admin'],
                                        'manager'     => ['📋', 'Manager'],
                                        'super_admin' => ['👑', 'Super Admin'],
                                    ];
                                @endphp

                                @foreach ($roles as $value => [$icon, $label])
                                    <div>
                                        <input type="radio"
                                               name="role"
                                               id="gen-role-{{ $value }}"
                                               value="{{ $value }}"
                                               {{ old('role', 'customer') === $value ? 'checked' : '' }}>
                                        <label class="wt-role-pill" for="gen-role-{{ $value }}">
                                            {{ $icon }} {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('role') <p style="color:#dc2626;font-size:.78rem;margin-top:.25rem;">{{ $message }}</p> @enderror
                        </div>

                        {{-- Max transactions per user --}}
                        <div class="wt-form-group">
                            <label for="max_txn">Max Transactions per User</label>
                            <div class="wt-input-row">
                                <input id="max_txn"
                                       type="number"
                                       name="max_txn"
                                       class="wt-form-control"
                                       min="1"
                                       max="100"
                                       value="{{ old('max_txn', 5) }}"
                                       placeholder="e.g. 5">
                                <span class="wt-max-label">min 1 · max given</span>
                            </div>

                            <p class="wt-hint" style="margin-top:.4rem;">
                                Each user will receive a <strong>random</strong> number of transactions
                                between 1 and this value.
                            </p>

                            @error('max_txn') <p style="color:#dc2626;font-size:.78rem;">{{ $message }}</p> @enderror
                        </div>

                        <hr class="wt-divider">

                        <button type="submit" class="wt-btn wt-btn--purple" id="btn-generate">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Dispatch Job
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Job 2 · Update Role Balances ───────────────────────────────── --}}
            <div class="wt-card">
                <div class="wt-card__header">
                    <div class="wt-card__icon wt-card__icon--teal">⚖️</div>
                    <div>
                        <p class="wt-card__title">Update Balances</p>
                        <p class="wt-card__desc">Recalculate from transaction history</p>
                    </div>
                </div>

                <div class="wt-card__body">
                    <form id="form-update"
                          action="{{ route('admin.wallet-transactions.update-balances') }}"
                          method="POST">
                        @csrf

                        {{-- Role picker --}}
                        <div class="wt-form-group">
                            <label>Target Role</label>
                            <div class="wt-role-group">
                                @foreach ($roles as $value => [$icon, $label])
                                    <div>
                                        <input type="radio"
                                               name="role"
                                               id="upd-role-{{ $value }}"
                                               value="{{ $value }}"
                                               {{ old('role_upd', 'customer') === $value ? 'checked' : '' }}>
                                        <label class="wt-role-pill" for="upd-role-{{ $value }}">
                                            {{ $icon }} {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <p class="wt-hint">
                            Calculates each user's balance as:
                            <br><code style="background:#f3f4f6;padding:.1rem .35rem;border-radius:4px;font-size:.78rem;">
                                balance = Σ credits − Σ debits
                            </code>
                            <br>Updates the <strong>balance</strong> column on the corresponding table.
                        </p>

                        <hr class="wt-divider">

                        <button type="submit" class="wt-btn wt-btn--teal" id="btn-update">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Dispatch Job
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- {{-- ── Info strip ───────────────────────────────────────────────────────── --}}
        <div class="wt-info-strip">
            <p class="wt-info-strip__title">ℹ️ How it works</p>
            <ul class="wt-info-list">
                <li><span class="dot"></span> Both forms can be submitted multiple times independently.</li>
                <li><span class="dot"></span> Jobs are queued — run <code>php artisan queue:work</code> in a terminal.</li>
                <li><span class="dot"></span> <strong>Job 1</strong> creates random debit/credit transactions for every user matching the role.</li>
                <li><span class="dot"></span> <strong>Job 2</strong> reads all existing transactions and sets balance = credits − debits.</li>
                <li><span class="dot"></span> Customer transactions use <code>customer_id</code>; admin roles use <code>admin_id</code>.</li>
                <li><span class="dot"></span> Run Job 1 first, then Job 2 to reflect updated balances.</li>
            </ul>
        </div> -->
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-dismiss flash message after 5 s
        const flash = document.getElementById('wt-flash');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity .5s';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 5000);
        }

        // Button loading state
        ['form-generate', 'form-update'].forEach(id => {
            const form = document.getElementById(id);
            if (!form) return;

            form.addEventListener('submit', () => {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.style.opacity = '.7';
                btn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                         style="animation:spin 1s linear infinite;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Dispatching…`;
            });
        });
    </script>
@endpush

