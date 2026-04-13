{{-- pages/admin/sections/admins.blade.php --}}
@extends('layouts.admin')

@push('styles')
    @vite(['resources/css/auth/admin/customer.css'])
@endpush
@section('content')
    <div class="admin-admins">

        {{-- ── Page Header ──────────────────────────────────────── --}}
        <div class="admins-header">
            <div>
                <h1>Admin Management</h1>
                <p>Manage and monitor admin accounts.</p>
            </div>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Admin
            </button>
        </div>

        {{-- ── Flash Messages ───────────────────────────────────── --}}
        @if (session('success'))
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any() && !session('modal'))
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Please fix the errors below.
            </div>
        @endif

        {{-- ── Toolbar ───────────────────────────────────────────── --}}
        <div class="admins-toolbar">
            <form method="GET" action="{{ route('admin.admins.index') }}" class="search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                </svg>
                <input type="text" name="search" class="search-input" placeholder="Search by ID, name, email or phone…"
                    value="{{ $search ?? '' }}" autocomplete="off">
            </form>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size:.8rem;color:#9ca3af;">
                    {{ $admins->total() }} admin{{ $admins->total() !== 1 ? 's' : '' }}
                </span>

                {{-- Per-page limit selector --}}
                <form method="GET" action="{{ route('admin.admins.index') }}" class="limit-form"
                    style="display: flex; align-items: center; gap: 0.5rem;">
                    @if ($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <label for="per_page" style="font-size:.8rem;color:#6b7280; margin: 0;">Show:</label>
                    <select id="per_page" name="per_page"
                        style="padding: 0.4rem 0.6rem; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.8rem; cursor: pointer;"
                        onchange="this.form.submit()">
                        <option value="10" @selected(request('per_page') == 10)>10</option>
                        <option value="25" @selected(request('per_page') == 25)>25</option>
                        <option value="50" @selected(request('per_page') == 50)>50</option>
                        <option value="100" @selected(request('per_page') == 100)>100</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- ── Table Card ───────────────────────────────────────── --}}
        <div class="table-card">
            <div style="overflow-x: auto;">
                <table class="admins-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Admin</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Balance</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td style="color:#9ca3af;font-size:.8rem;">{{ $admin->id }}</td>
                                <td>
                                    <div class="admin-name-cell">
                                        <div class="admin-avatar">
                                            @if (!empty($admin->profile_photo_url))
                                                <img class="admin-avatar__img" src="{{ $admin->profile_photo_url }}"
                                                    alt="{{ $admin->name }} profile photo">
                                            @else
                                                <span
                                                    aria-hidden="true">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <span class="admin-name">{{ $admin->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->phone_number ?? '—' }}</td>
                                <td><span
                                        class="role-badge role-badge--{{ $admin->role }}">{{ \App\Models\Admin::roles()[$admin->role] ?? $admin->role }}</span>
                                </td>
                                <td>
                                    <span
                                        style="font-weight: 600; color: #1e40af;">₱{{ number_format($admin->balance, 2) }}</span>
                                </td>
                                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        {{-- Edit --}}
                                        <button class="btn btn-ghost btn-sm"
                                            onclick='openEditModal({{ $admin->id }}, @json($admin->name), @json($admin->email), @json($admin->phone_number ?? ''), @json($admin->role), @json($admin->profile_photo_url))'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                                         m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>

                                        {{-- Delete --}}
                                        <button class="btn btn-danger btn-sm"
                                            onclick='openDeleteModal({{ $admin->id }}, @json($admin->name))'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                                                         L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2
                                                                     c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857
                                                                     M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0
                                                                     019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p>No admins found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($admins->hasPages())
                <div class="pagination-wrap">
                    <span class="pagination-info">
                        Showing {{ $admins->firstItem() }}–{{ $admins->lastItem() }} of {{ $admins->total() }} results
                    </span>
                    <div class="pagination-nav">
                        {{ $admins->links('components.admin.pagination') }}
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- ============================================================
     CREATE Modal
============================================================ --}}
    <div class="modal-backdrop" id="createModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Add New Admin</h2>
                <button class="modal-close" onclick="closeModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.admins.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label for="create_name">Full Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="create_name" name="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="John Doe" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_email">Email Address <span style="color:#ef4444">*</span></label>
                        <input type="email" id="create_email" name="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="john@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_phone">Phone</label>
                        <input type="text" id="create_phone" name="phone_number"
                            class="form-control @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number') }}" placeholder="+63 900 000 0000">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_role">Role <span style="color:#ef4444">*</span></label>
                        <select id="create_role" name="role" class="form-control @error('role') is-invalid @enderror"
                            required>
                            @foreach (\App\Models\Admin::roles() as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', 'manager') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="margin-bottom:0">
                            <label for="create_password">Password <span style="color:#ef4444">*</span></label>
                            <input type="password" id="create_password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Min. 8 characters" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label for="create_password_confirmation">Confirm Password <span
                                    style="color:#ef4444">*</span></label>
                            <input type="password" id="create_password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
     EDIT Modal
============================================================ --}}
    <div class="modal-backdrop" id="editModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Admin</h2>
                <button class="modal-close" onclick="closeModal('editModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" id="editForm" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_admin_id" name="admin_id" value="{{ old('admin_id') }}">

                    <div class="form-group">
                        <label>Profile Photo</label>
                        <div style="display:flex; align-items:center; gap: .75rem;">
                            <button type="button" class="admin-avatar" id="adminEditPhotoPreviewBtn"
                                style="width:44px;height:44px; border:none; cursor: zoom-in; overflow:hidden;"
                                aria-disabled="true">
                                <img id="edit_photo_preview" class="admin-avatar__img" alt="Profile preview"
                                    style="display:none;">
                                <span id="edit_photo_fallback" aria-hidden="true">--</span>
                            </button>
                            <input type="file" id="edit_profile_photo" name="profile_photo"
                                class="form-control @error('profile_photo') is-invalid @enderror"
                                accept="image/png,image/jpeg,image/webp">
                        </div>
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="edit_name">Full Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="edit_name" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('_method') === 'PUT' ? old('name') : '' }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="edit_email">Email Address <span style="color:#ef4444">*</span></label>
                        <input type="email" id="edit_email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('_method') === 'PUT' ? old('email') : '' }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="edit_phone">Phone</label>
                        <input type="text" id="edit_phone" name="phone_number"
                            class="form-control @error('phone_number') is-invalid @enderror"
                            value="{{ old('_method') === 'PUT' ? old('phone_number') : '' }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="edit_role">Role <span style="color:#ef4444">*</span></label>
                        <select id="edit_role" name="role" class="form-control @error('role') is-invalid @enderror"
                            required>
                            @foreach (\App\Models\Admin::roles() as $value => $label)
                                <option value="{{ $value }}" @selected((old('_method') === 'PUT' ? old('role') : '') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="margin-bottom:0">
                            <label for="edit_password">New Password</label>
                            <input type="password" id="edit_password" name="password" class="form-control"
                                placeholder="Leave blank to keep">
                            <div class="form-hint">Leave blank to keep current password.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label for="edit_password_confirmation">Confirm Password</label>
                            <input type="password" id="edit_password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Repeat new password">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
     DELETE Confirm Modal
============================================================ --}}
    <div class="modal-backdrop" id="deleteModal">
        <div class="modal delete-modal">
            <div class="modal-body">
                <div class="delete-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                                         L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3>Delete Admin?</h3>
                <p id="deleteAdminName" style="margin-top:.35rem;"></p>
                <p style="margin-top:.5rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('deleteModal')">Cancel</button>
                <form method="POST" id="deleteForm" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Full image viewer (admins page) --}}
        <div class="lb-photo-viewer" id="lbAdminsPhotoViewer" aria-hidden="true">
            <div class="lb-photo-viewer__backdrop" onclick="lbCloseAdminsPhotoViewer()"></div>
            <div class="lb-photo-viewer__dialog" role="dialog" aria-modal="true" aria-label="Profile photo viewer">
                <button type="button" class="lb-photo-viewer__close" onclick="lbCloseAdminsPhotoViewer()"
                    aria-label="Close photo viewer">✕</button>
                <img id="lbAdminsPhotoViewerImg" class="lb-photo-viewer__img" alt="Profile photo">
            </div>
        </div>

        <script>
            // ── Modal helpers ────────────────────────────────────────
            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }

            // Close on backdrop click
            document.querySelectorAll('.modal-backdrop').forEach(el => {
                el.addEventListener('click', e => {
                    if (e.target === el) el.classList.remove('open');
                });
            });

            // Close on ESC
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-backdrop.open').forEach(el => {
                        el.classList.remove('open');
                    });
                    lbCloseAdminsPhotoViewer();
                }
            });

            function lbOpenAdminsPhotoViewer(src, name) {
                const viewer = document.getElementById('lbAdminsPhotoViewer');
                const img = document.getElementById('lbAdminsPhotoViewerImg');
                if (!viewer || !img || !src) return;
                img.src = src;
                img.alt = (name || 'Admin') + ' profile photo';
                viewer.classList.add('open');
                viewer.setAttribute('aria-hidden', 'false');
            }

            function lbCloseAdminsPhotoViewer() {
                const viewer = document.getElementById('lbAdminsPhotoViewer');
                const img = document.getElementById('lbAdminsPhotoViewerImg');
                if (!viewer) return;
                viewer.classList.remove('open');
                viewer.setAttribute('aria-hidden', 'true');
                if (img) img.removeAttribute('src');
            }

            // ── Create ───────────────────────────────────────────────
            function openCreateModal() {
                openModal('createModal');
            }

            // ── Edit ─────────────────────────────────────────────────
            function openEditModal(id, name, email, phone, role, photoUrl) {
                const updateUrlTemplate = @json(route('admin.admins.update', ['admin' => '__ID__']));
                document.getElementById('editForm').action = updateUrlTemplate.replace('__ID__', id);
                document.getElementById('edit_admin_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_phone').value = phone || '';
                document.getElementById('edit_role').value = role || 'manager';
                document.getElementById('edit_password').value = '';
                document.getElementById('edit_password_confirmation').value = '';

                const initials = String(name || '')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(p => p[0])
                    .join('')
                    .toUpperCase()
                    .padEnd(2, '-')
                    .slice(0, 2);

                const preview = document.getElementById('edit_photo_preview');
                const fallback = document.getElementById('edit_photo_fallback');
                const fileInput = document.getElementById('edit_profile_photo');
                const previewBtn = document.getElementById('adminEditPhotoPreviewBtn');

                fallback.textContent = initials;
                fileInput.value = '';

                if (photoUrl) {
                    preview.src = photoUrl;
                    preview.style.display = 'block';
                    fallback.style.display = 'none';
                    if (previewBtn) previewBtn.setAttribute('aria-disabled', 'false');
                } else {
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                    fallback.style.display = 'inline';
                    if (previewBtn) previewBtn.setAttribute('aria-disabled', 'true');
                }

                openModal('editModal');
            }

            document.getElementById('edit_profile_photo')?.addEventListener('change', function() {
                const file = this.files?.[0];
                const preview = document.getElementById('edit_photo_preview');
                const fallback = document.getElementById('edit_photo_fallback');
                if (!file) return;

                const url = URL.createObjectURL(file);
                preview.src = url;
                preview.style.display = 'block';
                fallback.style.display = 'none';

                const previewBtn = document.getElementById('adminEditPhotoPreviewBtn');
                if (previewBtn) previewBtn.setAttribute('aria-disabled', 'false');
            });

            (function() {
                const btn = document.getElementById('adminEditPhotoPreviewBtn');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    const disabled = btn.getAttribute('aria-disabled') === 'true';
                    if (disabled) return;
                    const img = document.getElementById('edit_photo_preview');
                    const src = img ? img.getAttribute('src') : null;
                    const name = document.getElementById('edit_name') ? document.getElementById('edit_name').value :
                        'Admin';
                    if (!src) return;
                    lbOpenAdminsPhotoViewer(src, name);
                });
            })();

            // ── Delete ───────────────────────────────────────────────
            function openDeleteModal(id, name) {
                const deleteUrlTemplate = @json(route('admin.admins.destroy', ['admin' => '__ID__']));
                document.getElementById('deleteForm').action = deleteUrlTemplate.replace('__ID__', id);
                document.getElementById('deleteAdminName').textContent =
                    `You are about to permanently delete "${name}".`;
                openModal('deleteModal');
            }

            // ── Re-open modals on validation errors ─────────────────
            @if ($errors->any())
                @if (old('_method') === 'PUT' && old('admin_id'))
                    openEditModal(
                        @json(old('admin_id')),
                        @json(old('name')),
                        @json(old('email')),
                        @json(old('phone_number')),
                        @json(old('role')),
                        @json(null)
                    );
                @else
                    openModal('createModal');
                @endif
            @endif
        </script>
    @endpush
@endsection
