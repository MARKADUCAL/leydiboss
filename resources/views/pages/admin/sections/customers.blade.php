{{-- pages/admin/sections/customers.blade.php --}}
@extends('layouts.admin')

@push('styles')
    @vite(['resources/css/auth/admin/customer.css'])
@endpush
@section('content')
    <div class="admin-customers">

        {{-- ── Page Header ──────────────────────────────────────── --}}
        <div class="customers-header">
            <div>
                <h1>User Management</h1>
                <p>Manage and monitor customer accounts.</p>
            </div>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Customer
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
        <div class="customers-toolbar">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="search-wrap">
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
                    {{ $customers->total() }} customer{{ $customers->total() !== 1 ? 's' : '' }}
                </span>

                {{-- Per-page limit selector --}}
                <form method="GET" action="{{ route('admin.customers.index') }}" class="limit-form"
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
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td style="color:#9ca3af;font-size:.8rem;">{{ $customer->id }}</td>
                                <td>
                                    <div class="customer-name-cell">
                                        <div class="customer-avatar">
                                            @if (!empty($customer->profile_photo_url))
                                                <img class="customer-avatar__img" src="{{ $customer->profile_photo_url }}"
                                                    alt="{{ $customer->name }} profile photo">
                                            @else
                                                <span
                                                    aria-hidden="true">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <span class="customer-name">{{ $customer->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone_number ?? '—' }}</td>
                                <td>
                                    <span
                                        style="font-weight: 600; color: #1e40af;">₱{{ number_format($customer->balance, 2) }}</span>
                                </td>
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        {{-- Edit --}}
                                        <button class="btn btn-ghost btn-sm"
                                            onclick='openEditModal({{ $customer->id }}, @json($customer->name), @json($customer->email), @json($customer->phone_number ?? ''), @json($customer->profile_photo_url))'>
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
                                            onclick='openDeleteModal({{ $customer->id }}, @json($customer->name))'>
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
                                <td colspan="7">
                                    <div class="empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2
                                                                     c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857
                                                                     M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0
                                                                     019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p>No customers found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($customers->hasPages())
                <div class="pagination-wrap">
                    <span class="pagination-info">
                        Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }}
                        results
                    </span>
                    <div class="pagination-nav">
                        {{ $customers->links('components.admin.pagination') }}
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
                <h2>Add New Customer</h2>
                <button class="modal-close" onclick="closeModal('createModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.customers.store') }}">
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
                    <button type="submit" class="btn btn-primary">Create Customer</button>
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
                <h2>Edit Customer</h2>
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
                    <input type="hidden" id="edit_customer_id" name="customer_id" value="{{ old('customer_id') }}">

                    <div class="form-group">
                        <label>Profile Photo</label>
                        <div style="display:flex; align-items:center; gap: .75rem;">
                            <button type="button" class="customer-avatar" id="customerEditPhotoPreviewBtn"
                                style="width:44px;height:44px; border:none; cursor: zoom-in; overflow:hidden;"
                                aria-disabled="true">
                                <img id="edit_photo_preview" class="customer-avatar__img" alt="Profile preview"
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
                <h3>Delete Customer?</h3>
                <p id="deleteCustomerName" style="margin-top:.35rem;"></p>
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
        {{-- Full image viewer (customers page) --}}
        <div class="lb-photo-viewer" id="lbCustomersPhotoViewer" aria-hidden="true">
            <div class="lb-photo-viewer__backdrop" onclick="lbCloseCustomersPhotoViewer()"></div>
            <div class="lb-photo-viewer__dialog" role="dialog" aria-modal="true" aria-label="Profile photo viewer">
                <button type="button" class="lb-photo-viewer__close" onclick="lbCloseCustomersPhotoViewer()"
                    aria-label="Close photo viewer">✕</button>
                <img id="lbCustomersPhotoViewerImg" class="lb-photo-viewer__img" alt="Profile photo">
            </div>
        </div>

        <script>
            function lbOpenCustomersPhotoViewer(src, name) {
                const viewer = document.getElementById('lbCustomersPhotoViewer');
                const img = document.getElementById('lbCustomersPhotoViewerImg');
                if (!viewer || !img || !src) return;
                img.src = src;
                img.alt = (name || 'Customer') + ' profile photo';
                viewer.classList.add('open');
                viewer.setAttribute('aria-hidden', 'false');
            }

            function lbCloseCustomersPhotoViewer() {
                const viewer = document.getElementById('lbCustomersPhotoViewer');
                const img = document.getElementById('lbCustomersPhotoViewerImg');
                if (!viewer) return;
                viewer.classList.remove('open');
                viewer.setAttribute('aria-hidden', 'true');
                if (img) img.removeAttribute('src');
            }
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
                    lbCloseCustomersPhotoViewer();
                }
            });

            // ── Create ───────────────────────────────────────────────
            function openCreateModal() {
                openModal('createModal');
            }

            // ── Edit ─────────────────────────────────────────────────
            function openEditModal(id, name, email, phone, photoUrl) {
                const updateUrlTemplate = @json(route('admin.customers.update', ['customer' => '__ID__']));
                document.getElementById('editForm').action = updateUrlTemplate.replace('__ID__', id);
                document.getElementById('edit_customer_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_phone').value = phone;
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
                const previewBtn = document.getElementById('customerEditPhotoPreviewBtn');

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

                const previewBtn = document.getElementById('customerEditPhotoPreviewBtn');
                if (previewBtn) previewBtn.setAttribute('aria-disabled', 'false');
            });

            (function() {
                const btn = document.getElementById('customerEditPhotoPreviewBtn');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    const disabled = btn.getAttribute('aria-disabled') === 'true';
                    if (disabled) return;
                    const img = document.getElementById('edit_photo_preview');
                    const src = img ? img.getAttribute('src') : null;
                    const name = document.getElementById('edit_name') ? document.getElementById('edit_name').value :
                        'Customer';
                    if (!src) return;
                    lbOpenCustomersPhotoViewer(src, name);
                });
            })();

            // ── Delete ───────────────────────────────────────────────
            function openDeleteModal(id, name) {
                const deleteUrlTemplate = @json(route('admin.customers.destroy', ['customer' => '__ID__']));
                document.getElementById('deleteForm').action = deleteUrlTemplate.replace('__ID__', id);
                document.getElementById('deleteCustomerName').textContent =
                    `You are about to permanently delete "${name}".`;
                openModal('deleteModal');
            }

            // ── Re-open modals on validation errors ─────────────────
            @if ($errors->any())
                @if (old('_method') === 'PUT' && old('customer_id'))
                    openEditModal(
                        @json(old('customer_id')),
                        @json(old('name')),
                        @json(old('email')),
                        @json(old('phone_number')),
                        @json(null)
                    );
                @else
                    openModal('createModal');
                @endif
            @endif
        </script>
    @endpush
@endsection
