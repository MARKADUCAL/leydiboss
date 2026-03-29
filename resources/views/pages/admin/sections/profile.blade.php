{{-- pages/admin/sections/profile.blade.php --}}
@extends('layouts.admin')

@section('title', 'My Profile')

@push('styles')
    @vite(['resources/css/auth/admin/profile.css'])
@endpush

@section('content')
    <div class="ap-wrap">

        @if (session('success'))
            <div class="ap-alert ap-alert--success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="ap-alert ap-alert--error">
                Please fix the errors below.
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="ap-card">
            <div class="ap-card__header">
                <h2 class="ap-card__title">Admin Profile</h2>
                <button type="button" class="ap-btn ap-btn--primary" onclick="openModal('editProfileModal')">
                    Edit Profile
                </button>
            </div>

            @php
                $parts = array_values(array_filter(explode(' ', $admin->name ?? '')));
                $firstName = $parts[0] ?? ($admin->name ?? '');
                $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
                $initials = strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName ?: $firstName, 0, 1));

                // Get role display name
                $roleLabels = [
                    'manager' => 'Manager',
                    'admin' => 'Administrator',
                    'super_admin' => 'Super Administrator',
                ];
                $roleDisplay = $roleLabels[$admin->role] ?? ucfirst($admin->role);
            @endphp

            <div class="ap-profile">
                <button type="button" id="apProfileAvatarBtn" class="ap-avatar ap-avatar--clickable"
                    data-photo-url="{{ $admin->profile_photo_url ?? '' }}"
                    @if (empty($admin->profile_photo_url)) aria-disabled="true" @endif>
                    @if (!empty($admin->profile_photo_url))
                        <img class="ap-avatar__img" src="{{ $admin->profile_photo_url }}"
                            alt="{{ $admin->name ?? 'Admin' }} profile photo">
                    @else
                        <span class="ap-avatar__initials" aria-hidden="true">{{ $initials }}</span>
                    @endif
                </button>

                <div class="ap-profile__fields">
                    <div class="ap-field">
                        <div class="ap-field__label">First Name:</div>
                        <div class="ap-field__value">{{ $firstName ?: '—' }}</div>
                    </div>
                    <div class="ap-field">
                        <div class="ap-field__label">Last Name:</div>
                        <div class="ap-field__value">{{ $lastName ?: '—' }}</div>
                    </div>
                    <div class="ap-field">
                        <div class="ap-field__label">Email:</div>
                        <div class="ap-field__value">{{ $admin->email ?? '—' }}</div>
                    </div>
                    <div class="ap-field">
                        <div class="ap-field__label">Phone:</div>
                        <div class="ap-field__value">{{ $admin->phone_number ?? '—' }}</div>
                    </div>
                    <div class="ap-field">
                        <div class="ap-field__label">Status:</div>
                        <div class="ap-pill">• Active Admin</div>
                    </div>
                    <div class="ap-field">
                        <div class="ap-field__label">Role:</div>
                        <div class="ap-field__value">{{ $roleDisplay }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Edit Profile Modal --}}
    <div class="ap-modal-backdrop" id="editProfileModal">
        <div class="ap-modal">
            <div class="ap-modal__header">
                <h3>Edit Profile</h3>
                <button type="button" class="ap-modal__close" onclick="closeModal('editProfileModal')">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_name" value="edit_profile">

                <div class="ap-modal__body">
                    <div class="ap-form-group">
                        <label>Profile Photo</label>
                        <div class="ap-photo-field">
                            <button type="button" class="ap-photo-field__preview ap-avatar ap-photo-field__preview-btn"
                                id="apEditPhotoPreviewBtn"
                                style="width:64px;height:64px;margin-left:0;"
                                @if (empty($admin->profile_photo_url)) aria-disabled="true" @endif>
                                <img id="ap_edit_photo_preview" class="ap-avatar__img"
                                    alt="{{ $admin->name ?? 'Admin' }} profile photo"
                                    @if (empty($admin->profile_photo_url)) style="display:none;" @endif
                                    @if (!empty($admin->profile_photo_url)) src="{{ $admin->profile_photo_url }}" @endif>
                                <span id="ap_edit_photo_fallback" class="ap-avatar__initials" aria-hidden="true"
                                    @if (!empty($admin->profile_photo_url)) style="display:none;" @endif>
                                    {{ $initials }}
                                </span>
                            </button>

                            <input id="ap_edit_profile_photo" type="file" name="profile_photo"
                                class="ap-input @error('profile_photo') is-invalid @enderror"
                                accept="image/png,image/jpeg,image/webp">
                        </div>
                        @error('profile_photo')
                            <div class="ap-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ap-form-group">
                        <label>Full Name <span class="ap-req">*</span></label>
                        <input type="text" name="name" class="ap-input @error('name') is-invalid @enderror"
                            value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <div class="ap-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ap-form-grid">
                        <div class="ap-form-group">
                            <label>Email <span class="ap-req">*</span></label>
                            <input type="email" name="email" class="ap-input @error('email') is-invalid @enderror"
                                value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="ap-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="ap-form-group">
                            <label>Phone</label>
                            <input type="text" name="phone_number"
                                class="ap-input @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', $admin->phone_number) }}">
                            @error('phone_number')
                                <div class="ap-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="ap-form-grid" style="margin-top: 6px;">
                        <div class="ap-form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="ap-input @error('password') is-invalid @enderror"
                                placeholder="Leave blank to keep current">
                            @error('password')
                                <div class="ap-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="ap-form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="ap-input"
                                placeholder="Repeat new password">
                        </div>
                    </div>
                </div>

                <div class="ap-modal__footer">
                    <button type="button" class="ap-btn ap-btn--ghost"
                        onclick="closeModal('editProfileModal')">Cancel</button>
                    <button type="submit" class="ap-btn ap-btn--accent">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Full image viewer --}}
    <div class="ap-photo-viewer" id="apPhotoViewer" aria-hidden="true">
        <div class="ap-photo-viewer__backdrop" onclick="apClosePhotoViewer()"></div>
        <div class="ap-photo-viewer__dialog" role="dialog" aria-modal="true" aria-label="Profile photo viewer">
            <button type="button" class="ap-photo-viewer__close" onclick="apClosePhotoViewer()"
                aria-label="Close photo viewer">✕</button>
            <img id="apPhotoViewerImg" class="ap-photo-viewer__img" alt="Profile photo">
        </div>
    </div>

    {{-- Modal Scripts --}}
    <script>
        function apOpenPhotoViewerFrom(src) {
            const viewer = document.getElementById('apPhotoViewer');
            const viewerImg = document.getElementById('apPhotoViewerImg');
            if (!src || !viewer || !viewerImg) return;

            viewerImg.src = src;
            viewer.classList.add('open');
            viewer.setAttribute('aria-hidden', 'false');
        }

        function apClosePhotoViewer() {
            const viewer = document.getElementById('apPhotoViewer');
            const viewerImg = document.getElementById('apPhotoViewerImg');
            if (!viewer) return;
            viewer.classList.remove('open');
            viewer.setAttribute('aria-hidden', 'true');
            if (viewerImg) viewerImg.removeAttribute('src');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal when clicking on backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList && e.target.classList.contains('ap-modal-backdrop')) {
                e.target.classList.remove('open');
                document.body.style.overflow = 'auto';
            }
        });

        // Close modal when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.ap-modal-backdrop').forEach(modal => {
                    modal.classList.remove('open');
                });
                apClosePhotoViewer();
                document.body.style.overflow = 'auto';
            }
        });

        const apProfileAvatarBtn = document.getElementById('apProfileAvatarBtn');
        if (apProfileAvatarBtn) {
            apProfileAvatarBtn.addEventListener('click', function() {
                const url = apProfileAvatarBtn.getAttribute('data-photo-url');
                if (!url) return;
                apOpenPhotoViewerFrom(url);
            });
        }

        const apEditPhotoPreviewBtn = document.getElementById('apEditPhotoPreviewBtn');
        if (apEditPhotoPreviewBtn) {
            apEditPhotoPreviewBtn.addEventListener('click', function() {
                const img = document.getElementById('ap_edit_photo_preview');
                const url = img ? img.getAttribute('src') : null;
                if (!url) return;
                apOpenPhotoViewerFrom(url);
            });
        }

        const apPhotoInput = document.getElementById('ap_edit_profile_photo');
        if (apPhotoInput) apPhotoInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            const preview = document.getElementById('ap_edit_photo_preview');
            const fallback = document.getElementById('ap_edit_photo_fallback');
            if (!file || !preview || !fallback) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.style.display = 'block';
            fallback.style.display = 'none';
            apEditPhotoPreviewBtn.setAttribute('aria-disabled', 'false');
        });
    </script>
@endsection
