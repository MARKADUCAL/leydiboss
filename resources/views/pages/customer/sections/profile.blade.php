@extends('layouts.customer')

@section('title', 'My Profile')

@push('styles')
    @vite(['resources/css/auth/customer/profile.css'])
@endpush

@section('content')
    <div class="cp-wrap">

        @if (session('success'))
            <div class="cp-alert cp-alert--success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="cp-alert cp-alert--error">
                Please fix the errors below.
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="cp-card">
            <div class="cp-card__header">
                <h2 class="cp-card__title">Customer Profile</h2>
                <button type="button" class="cp-btn cp-btn--primary" onclick="openModal('editProfileModal')">
                    Edit Profile
                </button>
            </div>

            @php
                $parts = array_values(array_filter(explode(' ', $customer->name ?? '')));
                $firstName = $parts[0] ?? ($customer->name ?? '');
                $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
                $initials = strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName ?: $firstName, 0, 1));
            @endphp

            <div class="cp-profile">
                <button type="button" id="cpProfileAvatarBtn" class="cp-avatar cp-avatar--clickable"
                    data-photo-url="{{ $customer->profile_photo_url ?? '' }}"
                    @if (empty($customer->profile_photo_url)) aria-disabled="true" @endif>
                    @if (!empty($customer->profile_photo_url))
                        <img class="cp-avatar__img" src="{{ $customer->profile_photo_url }}"
                            alt="{{ $customer->name ?? 'Customer' }} profile photo">
                    @else
                        <span class="cp-avatar__initials" aria-hidden="true">{{ $initials }}</span>
                    @endif
                </button>

                <div class="cp-profile__fields">
                    <div class="cp-field">
                        <div class="cp-field__label">First Name:</div>
                        <div class="cp-field__value">{{ $firstName ?: '—' }}</div>
                    </div>
                    <div class="cp-field">
                        <div class="cp-field__label">Last Name:</div>
                        <div class="cp-field__value">{{ $lastName ?: '—' }}</div>
                    </div>
                    <div class="cp-field">
                        <div class="cp-field__label">Email:</div>
                        <div class="cp-field__value">{{ $customer->email ?? '—' }}</div>
                    </div>
                    <div class="cp-field">
                        <div class="cp-field__label">Phone:</div>
                        <div class="cp-field__value">{{ $customer->phone_number ?? '—' }}</div>
                    </div>
                    <div class="cp-field">
                        <div class="cp-field__label">Membership:</div>
                        <div class="cp-pill">• Active Customer</div>
                    </div>
                    <div class="cp-field">
                        <div class="cp-field__label">Account Type:</div>
                        <div class="cp-field__value">Customer</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saved Vehicles --}}
        <div class="cp-section">
            <div class="cp-section__header">
                <div>
                    <h2 class="cp-section__title">Saved Vehicles</h2>
                    <p class="cp-section__sub">Store your car details for faster bookings.</p>
                </div>
                <button type="button" class="cp-btn cp-btn--accent" onclick="openModal('addVehicleModal')">
                    Add Vehicle
                </button>
            </div>

            <div class="cp-vehicles">
                @forelse($vehicles as $vehicle)
                    <div class="cp-vehicle-card">
                        <div class="cp-vehicle-card__top">
                            <div class="cp-vehicle-card__name">{{ $vehicle->nickname }}</div>
                            <form method="POST" action="{{ route('customer.profile.vehicles.destroy', $vehicle) }}">
                                @csrf
                                @method('DELETE')
                                <button class="cp-link-danger" type="submit"
                                    onclick="return confirm('Remove this vehicle?')">
                                    Remove
                                </button>
                            </form>
                        </div>

                        <div class="cp-vehicle-type">
                            <span class="cp-vehicle-type__badge">
                                {{ $vehicle->vehicleType?->code ?? '—' }} -
                                {{ $vehicle->vehicleType?->description ?? 'Unknown type' }}
                            </span>
                        </div>

                        <div class="cp-vehicle-meta">
                            <div class="cp-meta-row">
                                <span class="cp-meta-label">Model</span>
                                <span class="cp-meta-value">{{ $vehicle->model ?: '—' }}</span>
                            </div>
                            <div class="cp-meta-row">
                                <span class="cp-meta-label">Plate Number</span>
                                <span class="cp-meta-value">{{ $vehicle->plate_number ?: '—' }}</span>
                            </div>
                            <div class="cp-meta-row">
                                <span class="cp-meta-label">Color</span>
                                <span class="cp-meta-value">{{ $vehicle->color ?: '—' }}</span>
                            </div>
                            <div class="cp-meta-row">
                                <span class="cp-meta-label">Added</span>
                                <span class="cp-meta-value">{{ $vehicle->created_at?->format('M j, Y') ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="cp-empty">
                        No vehicles saved yet. Click <strong>Add Vehicle</strong> to create one.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Edit Profile Modal --}}
    <div class="cp-modal-backdrop" id="editProfileModal">
        <div class="cp-modal">
            <div class="cp-modal__header">
                <h3>Edit Profile</h3>
                <button type="button" class="cp-modal__close" onclick="closeModal('editProfileModal')">✕</button>
            </div>

            <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_name" value="edit_profile">

                <div class="cp-modal__body">
                    <div class="cp-form-group">
                        <label>Profile Photo</label>
                        <div class="cp-photo-field">
                            <button type="button" class="cp-photo-field__preview cp-avatar cp-photo-field__preview-btn"
                                style="width:64px;height:64px;margin-left:0;"
                                id="cpEditPhotoPreviewBtn"
                                @if (empty($customer->profile_photo_url)) aria-disabled="true" @endif>
                                <img id="cp_edit_photo_preview" class="cp-avatar__img"
                                    alt="{{ $customer->name ?? 'Customer' }} profile photo"
                                    @if (empty($customer->profile_photo_url)) style="display:none;" @endif
                                    @if (!empty($customer->profile_photo_url)) src="{{ $customer->profile_photo_url }}" @endif>
                                <span id="cp_edit_photo_fallback" class="cp-avatar__initials" aria-hidden="true"
                                    @if (!empty($customer->profile_photo_url)) style="display:none;" @endif>
                                    {{ $initials }}
                                </span>
                            </button>

                            <input id="cp_edit_profile_photo" type="file" name="profile_photo"
                                class="cp-input @error('profile_photo') is-invalid @enderror"
                                accept="image/png,image/jpeg,image/webp">
                        </div>
                        @error('profile_photo')
                            <div class="cp-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cp-form-group">
                        <label>Full Name <span class="cp-req">*</span></label>
                        <input type="text" name="name" class="cp-input @error('name') is-invalid @enderror"
                            value="{{ old('name', $customer->name) }}" required>
                        @error('name')
                            <div class="cp-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cp-form-grid">
                        <div class="cp-form-group">
                            <label>Email <span class="cp-req">*</span></label>
                            <input type="email" name="email" class="cp-input @error('email') is-invalid @enderror"
                                value="{{ old('email', $customer->email) }}" required>
                            @error('email')
                                <div class="cp-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="cp-form-group">
                            <label>Phone</label>
                            <input type="text" name="phone_number"
                                class="cp-input @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', $customer->phone_number) }}">
                            @error('phone_number')
                                <div class="cp-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="cp-form-grid" style="margin-top: 6px;">
                        <div class="cp-form-group">
                            <label>New Password</label>
                            <input type="password" name="password"
                                class="cp-input @error('password') is-invalid @enderror"
                                placeholder="Leave blank to keep current">
                            @error('password')
                                <div class="cp-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="cp-form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="cp-input"
                                placeholder="Repeat new password">
                        </div>
                    </div>
                </div>

                <div class="cp-modal__footer">
                    <button type="button" class="cp-btn cp-btn--ghost" onclick="closeModal('editProfileModal')">Cancel</button>
                    <button type="submit" class="cp-btn cp-btn--accent">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Vehicle Modal --}}
    <div class="cp-modal-backdrop" id="addVehicleModal">
        <div class="cp-modal">
            <div class="cp-modal__header">
                <h3>Add Vehicle</h3>
                <button type="button" class="cp-modal__close" onclick="closeModal('addVehicleModal')">✕</button>
            </div>

            <form method="POST" action="{{ route('customer.profile.vehicles.store') }}">
                @csrf
                <input type="hidden" name="form_name" value="add_vehicle">
                <div class="cp-modal__body">
                    <div class="cp-form-group">
                        <label>Nickname <span class="cp-req">*</span></label>
                        <input type="text" name="nickname" class="cp-input @error('nickname') is-invalid @enderror"
                            value="{{ old('nickname') }}" placeholder="e.g. mtb" required>
                        @error('nickname')
                            <div class="cp-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cp-form-group">
                        <label>Vehicle Type <span class="cp-req">*</span></label>
                        <select name="vehicle_type_id"
                            class="cp-input @error('vehicle_type_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Select vehicle type</option>
                            @foreach ($vehicleTypes as $vt)
                                <option value="{{ $vt->id }}" @selected((int) old('vehicle_type_id') === $vt->id)>
                                    {{ $vt->code }} - {{ $vt->description ?? $vt->label ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_type_id')
                            <div class="cp-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="cp-form-grid">
                        <div class="cp-form-group">
                            <label>Model</label>
                            <input type="text" name="model" class="cp-input" value="{{ old('model') }}"
                                placeholder="e.g. Vios">
                        </div>
                        <div class="cp-form-group">
                            <label>Plate Number</label>
                            <input type="text" name="plate_number" class="cp-input" value="{{ old('plate_number') }}"
                                placeholder="123-123">
                        </div>
                        <div class="cp-form-group" style="grid-column: 1 / -1;">
                            <label>Color</label>
                            <input type="text" name="color" class="cp-input" value="{{ old('color') }}"
                                placeholder="e.g. Red">
                        </div>
                    </div>
                </div>

                <div class="cp-modal__footer">
                    <button type="button" class="cp-btn cp-btn--ghost" onclick="closeModal('addVehicleModal')">Cancel</button>
                    <button type="submit" class="cp-btn cp-btn--accent">Save Vehicle</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        {{-- Full image viewer --}}
        <div class="cp-photo-viewer" id="cpPhotoViewer" aria-hidden="true">
            <div class="cp-photo-viewer__backdrop" onclick="cpClosePhotoViewer()"></div>
            <div class="cp-photo-viewer__dialog" role="dialog" aria-modal="true" aria-label="Profile photo viewer">
                <button type="button" class="cp-photo-viewer__close" onclick="cpClosePhotoViewer()"
                    aria-label="Close photo viewer">✕</button>
                <img id="cpPhotoViewerImg" class="cp-photo-viewer__img" alt="Profile photo">
            </div>
        </div>

        <script>
            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }

            function cpOpenPhotoViewerFrom(src) {
                const viewer = document.getElementById('cpPhotoViewer');
                const viewerImg = document.getElementById('cpPhotoViewerImg');
                if (!src) return;

                viewerImg.src = src;
                viewer.classList.add('open');
                viewer.setAttribute('aria-hidden', 'false');
            }

            function cpClosePhotoViewer() {
                const viewer = document.getElementById('cpPhotoViewer');
                const viewerImg = document.getElementById('cpPhotoViewerImg');
                if (!viewer) return;
                viewer.classList.remove('open');
                viewer.setAttribute('aria-hidden', 'true');
                if (viewerImg) viewerImg.removeAttribute('src');
            }

            document.querySelectorAll('.cp-modal-backdrop').forEach(el => {
                el.addEventListener('click', e => {
                    if (e.target === el) el.classList.remove('open');
                });
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.cp-modal-backdrop.open').forEach(el => el.classList.remove('open'));
                    cpClosePhotoViewer();
                }
            });

            const cpPhotoInput = document.getElementById('cp_edit_profile_photo');
            if (cpPhotoInput) cpPhotoInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                const preview = document.getElementById('cp_edit_photo_preview');
                const fallback = document.getElementById('cp_edit_photo_fallback');
                if (!file) return;

                const url = URL.createObjectURL(file);
                preview.src = url;
                preview.style.display = 'block';
                fallback.style.display = 'none';

                const btn = document.getElementById('cpEditPhotoPreviewBtn');
                if (btn) btn.setAttribute('aria-disabled', 'false');
            });

            const cpProfileAvatarBtn = document.getElementById('cpProfileAvatarBtn');
            if (cpProfileAvatarBtn) {
                cpProfileAvatarBtn.addEventListener('click', function() {
                    const url = this.getAttribute('data-photo-url');
                    if (!url) return;
                    cpOpenPhotoViewerFrom(url);
                });
            }

            const cpEditPhotoPreviewBtn = document.getElementById('cpEditPhotoPreviewBtn');
            if (cpEditPhotoPreviewBtn) {
                cpEditPhotoPreviewBtn.addEventListener('click', function() {
                    const img = document.getElementById('cp_edit_photo_preview');
                    const url = img ? img.getAttribute('src') : null;
                    if (!url) return;
                    cpOpenPhotoViewerFrom(url);
                });
            }

            @if ($errors->any())
                @if (old('form_name') === 'edit_profile')
                    openModal('editProfileModal');
                @else
                    openModal('addVehicleModal');
                @endif
            @endif
        </script>
    @endpush
@endsection

