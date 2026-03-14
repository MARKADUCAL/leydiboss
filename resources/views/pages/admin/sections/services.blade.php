@extends('layouts.admin')

@push('styles')
    @vite(['resources/css/auth/admin/services.css'])
@endpush

@section('page-title', 'Services Management')

@section('content')
    <div class="admin-services">

        {{-- Flash Messages --}}
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

        @if ($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Please fix the errors below.
            </div>
        @endif

        <div class="services-grid">

            {{-- Left column --}}
            <div class="services-col">

                {{-- Add Pricing Entry --}}
                <div class="card">
                    <div class="card__header">
                        <h2>Add New Pricing Entry</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.services.pricing.store') }}" class="card__body">
                        @csrf

                        <div class="form-group">
                            <label>Vehicle Type <span class="req">*</span></label>
                            <select name="vehicle_type_id" class="form-control @error('vehicle_type_id') is-invalid @enderror"
                                required>
                                <option value="" disabled selected>Select vehicle type</option>
                                @foreach ($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}"
                                        @selected((int) old('vehicle_type_id') === $vt->id)>
                                        {{ $vt->code }}{{ $vt->description ? ' — ' . $vt->description : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Service Package <span class="req">*</span></label>
                            <select name="service_package_id"
                                class="form-control @error('service_package_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select service package</option>
                                @foreach ($packages as $pkg)
                                    <option value="{{ $pkg->id }}"
                                        @selected((int) old('service_package_id') === $pkg->id)>
                                        {{ $pkg->code }} — {{ $pkg->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_package_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Price (₱) <span class="req">*</span></label>
                            <input type="number" step="0.01" min="0" name="price"
                                class="form-control @error('price') is-invalid @enderror" value="{{ old('price') ?? 0 }}"
                                required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="checkbox">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') == '1')>
                            Active (available for booking)
                        </label>

                        <button type="submit" class="btn btn-primary btn-full">Add Pricing Entry</button>
                    </form>
                </div>

                {{-- Service Packages --}}
                <div class="card">
                    <div class="card__header">
                        <h2>Service Packages</h2>
                        <p>Admins can add, edit, and delete service types here.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.services.packages.store') }}" class="card__body">
                        @csrf

                        <div class="form-group">
                            <label>Package Code <span class="req">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                placeholder="e.g. p1" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="e.g. Package 1" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Short description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="checkbox">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') == '1')>
                            Active (available for pricing)
                        </label>

                        <button type="submit" class="btn btn-primary btn-full">Add Package</button>
                    </form>

                    <div class="table-card table-card--compact">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $pkg)
                                    <tr>
                                        <td><span class="code-chip">{{ $pkg->code }}</span></td>
                                        <td>
                                            <div class="cell-title">{{ $pkg->name }}</div>
                                            <div class="cell-sub">{{ $pkg->description ?: '—' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $pkg->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                {{ $pkg->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <button class="icon-btn"
                                                    onclick='openEditPackageModal({{ $pkg->id }}, @json($pkg->code), @json($pkg->name), @json($pkg->description ?? ""), {{ $pkg->is_active ? "true" : "false" }})'
                                                    type="button" title="Edit">
                                                    ✎
                                                </button>
                                                <button class="icon-btn icon-btn--danger"
                                                    onclick='openDeletePackageModal({{ $pkg->id }}, @json($pkg->name))'
                                                    type="button" title="Delete">
                                                    🗑
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-td">No packages yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Vehicle Types --}}
                <div class="card">
                    <div class="card__header">
                        <h2>Vehicle Types</h2>
                        <p>Used as the pricing matrix rows (S, M, L, XL).</p>
                    </div>
                    <form method="POST" action="{{ route('admin.services.vehicle-types.store') }}" class="card__body">
                        @csrf

                        <div class="form-row">
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Code <span class="req">*</span></label>
                                <input type="text" name="code"
                                    class="form-control @error('code') is-invalid @enderror" placeholder="e.g. S"
                                    value="{{ old('code') }}" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Label</label>
                                <input type="text" name="label" class="form-control" placeholder="e.g. Small"
                                    value="{{ old('label') }}">
                            </div>
                        </div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-group" style="margin-top: 1rem;">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control"
                                placeholder="e.g. Sedans (all sedan types)" value="{{ old('description') }}">
                        </div>

                        <label class="checkbox">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') == '1')>
                            Active
                        </label>

                        <button type="submit" class="btn btn-primary btn-full">Add Vehicle Type</button>
                    </form>

                    <div class="table-card table-card--compact">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicleTypes as $vt)
                                    <tr>
                                        <td><span class="code-chip">{{ $vt->code }}</span></td>
                                        <td>
                                            <div class="cell-title">{{ $vt->label ?: '—' }}</div>
                                            <div class="cell-sub">{{ $vt->description ?: '—' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $vt->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                {{ $vt->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <button class="icon-btn"
                                                    onclick='openEditVehicleModal({{ $vt->id }}, @json($vt->code), @json($vt->label ?? ""), @json($vt->description ?? ""), {{ $vt->is_active ? "true" : "false" }})'
                                                    type="button" title="Edit">
                                                    ✎
                                                </button>
                                                <button class="icon-btn icon-btn--danger"
                                                    onclick='openDeleteVehicleModal({{ $vt->id }}, @json($vt->code))'
                                                    type="button" title="Delete">
                                                    🗑
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-td">No vehicle types yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Right column --}}
            <div class="services-col services-col--wide">

                {{-- Pricing Matrix --}}
                <div class="card">
                    <div class="card__header">
                        <h2>Pricing Matrix</h2>
                        <p>Current pricing structure for all vehicle types and service packages</p>
                    </div>

                    <div class="matrix-wrap">
                        <table class="matrix-table">
                            <thead>
                                <tr>
                                    <th class="matrix-th matrix-th--vehicle">Vehicle Type</th>
                                    @foreach ($packages as $pkg)
                                        <th class="matrix-th">{{ strtoupper($pkg->code) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicleTypes as $vt)
                                    <tr>
                                        <td class="matrix-td matrix-td--vehicle">
                                            <div class="vehicle-cell">
                                                <div class="vehicle-badge">{{ $vt->code }}</div>
                                                <div class="vehicle-meta">
                                                    <div class="vehicle-title">{{ $vt->label ?: '—' }}</div>
                                                    <div class="vehicle-sub">{{ $vt->description ?: '—' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($packages as $pkg)
                                            @php
                                                $entry = data_get($matrix, "{$vt->id}.{$pkg->id}.0");
                                            @endphp
                                            <td class="matrix-td">
                                                <div class="price-cell">
                                                    <div class="price">
                                                        {{ $entry ? '₱' . number_format($entry->price, 2) : '—' }}
                                                    </div>
                                                    <button type="button" class="icon-btn icon-btn--tiny"
                                                        title="Edit price"
                                                        onclick='openPricingModal(
                                                            {{ $vt->id }},
                                                            {{ $pkg->id }},
                                                            {{ $entry?->id ?? "null" }},
                                                            @json($entry?->price ?? 0),
                                                            {{ ($entry?->is_active ?? true) ? "true" : "false" }}
                                                        )'>
                                                        ✎
                                                    </button>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="empty-td" colspan="{{ 1 + max(1, $packages->count()) }}">
                                            Add vehicle types and packages to start building your matrix.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pricing Entries --}}
                <div class="card">
                    <div class="card__header card__header--row">
                        <div>
                            <h2>Pricing Entries</h2>
                            <p>All stored price combinations</p>
                        </div>
                    </div>

                    <div class="table-card">
                        <table class="entries-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">ID</th>
                                    <th>Vehicle Type</th>
                                    <th>Service Package</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pricingEntries as $pe)
                                    <tr>
                                        <td class="muted">{{ $pe->id }}</td>
                                        <td>
                                            <span class="code-chip">{{ $pe->vehicleType?->code ?? '—' }}</span>
                                            <span class="muted">{{ $pe->vehicleType?->description ?? '' }}</span>
                                        </td>
                                        <td>
                                            <span class="code-chip">{{ $pe->servicePackage?->code ?? '—' }}</span>
                                            <span class="muted">{{ $pe->servicePackage?->name ?? '' }}</span>
                                        </td>
                                        <td class="price">₱{{ number_format($pe->price, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $pe->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                {{ $pe->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <button class="btn btn-ghost btn-sm" type="button"
                                                    onclick='openEditPricingEntryModal({{ $pe->id }}, @json($pe->price), {{ $pe->is_active ? "true" : "false" }})'>
                                                    Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm" type="button"
                                                    onclick='openDeletePricingModal({{ $pe->id }})'>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-td">No pricing entries yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($pricingEntries->hasPages())
                            <div class="pagination-wrap">
                                <span class="pagination-info">
                                    Showing {{ $pricingEntries->firstItem() }}–{{ $pricingEntries->lastItem() }}
                                    of {{ $pricingEntries->total() }}
                                </span>
                                {{ $pricingEntries->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- ============================================================
      Modals
    ============================================================ --}}

    {{-- Edit Package --}}
    <div class="modal-backdrop" id="editPackageModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Package</h2>
                <button class="modal-close" type="button" onclick="closeModal('editPackageModal')">✕</button>
            </div>
            <form method="POST" id="editPackageForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Package Code <span class="req">*</span></label>
                        <input type="text" id="edit_pkg_code" name="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Name <span class="req">*</span></label>
                        <input type="text" id="edit_pkg_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="edit_pkg_description" name="description" rows="2" class="form-control"></textarea>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" id="edit_pkg_active" name="is_active" value="1">
                        Active
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editPackageModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Package --}}
    <div class="modal-backdrop" id="deletePackageModal">
        <div class="modal delete-modal">
            <div class="modal-body">
                <h3>Delete Package?</h3>
                <p id="deletePackageName" style="margin-top:.35rem;"></p>
                <p style="margin-top:.5rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('deletePackageModal')">Cancel</button>
                <form method="POST" id="deletePackageForm" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Vehicle Type --}}
    <div class="modal-backdrop" id="editVehicleModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Vehicle Type</h2>
                <button class="modal-close" type="button" onclick="closeModal('editVehicleModal')">✕</button>
            </div>
            <form method="POST" id="editVehicleForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Code <span class="req">*</span></label>
                            <input type="text" id="edit_vt_code" name="code" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Label</label>
                            <input type="text" id="edit_vt_label" name="label" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Description</label>
                        <input type="text" id="edit_vt_description" name="description" class="form-control">
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" id="edit_vt_active" name="is_active" value="1">
                        Active
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editVehicleModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Vehicle Type --}}
    <div class="modal-backdrop" id="deleteVehicleModal">
        <div class="modal delete-modal">
            <div class="modal-body">
                <h3>Delete Vehicle Type?</h3>
                <p id="deleteVehicleName" style="margin-top:.35rem;"></p>
                <p style="margin-top:.5rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('deleteVehicleModal')">Cancel</button>
                <form method="POST" id="deleteVehicleForm" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Pricing (Matrix Quick Edit) --}}
    <div class="modal-backdrop" id="pricingModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="pricingModalTitle">Edit Price</h2>
                <button class="modal-close" type="button" onclick="closeModal('pricingModal')">✕</button>
            </div>

            <form method="POST" id="pricingModalForm" action="">
                @csrf
                <input type="hidden" id="pricing_vehicle_type_id" name="vehicle_type_id">
                <input type="hidden" id="pricing_service_package_id" name="service_package_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Price (₱) <span class="req">*</span></label>
                        <input type="number" step="0.01" min="0" id="pricing_price" name="price"
                            class="form-control" required>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" id="pricing_active" name="is_active" value="1">
                        Active
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('pricingModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Pricing Entry (list) --}}
    <div class="modal-backdrop" id="editPricingEntryModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Edit Pricing Entry</h2>
                <button class="modal-close" type="button" onclick="closeModal('editPricingEntryModal')">✕</button>
            </div>
            <form method="POST" id="editPricingEntryForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Price (₱) <span class="req">*</span></label>
                        <input type="number" step="0.01" min="0" id="edit_pe_price" name="price"
                            class="form-control" required>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" id="edit_pe_active" name="is_active" value="1">
                        Active
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editPricingEntryModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Pricing Entry --}}
    <div class="modal-backdrop" id="deletePricingModal">
        <div class="modal delete-modal">
            <div class="modal-body">
                <h3>Delete Pricing Entry?</h3>
                <p style="margin-top:.5rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('deletePricingModal')">Cancel</button>
                <form method="POST" id="deletePricingForm" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }

            document.querySelectorAll('.modal-backdrop').forEach(el => {
                el.addEventListener('click', e => {
                    if (e.target === el) el.classList.remove('open');
                });
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-backdrop.open').forEach(el => el.classList.remove('open'));
                }
            });

            // Packages
            function openEditPackageModal(id, code, name, description, isActive) {
                const updateUrlTemplate = @json(route('admin.services.packages.update', ['package' => '__ID__']));
                document.getElementById('editPackageForm').action = updateUrlTemplate.replace('__ID__', id);
                document.getElementById('edit_pkg_code').value = code;
                document.getElementById('edit_pkg_name').value = name;
                document.getElementById('edit_pkg_description').value = description;
                document.getElementById('edit_pkg_active').checked = !!isActive;
                openModal('editPackageModal');
            }

            function openDeletePackageModal(id, name) {
                const deleteUrlTemplate = @json(route('admin.services.packages.destroy', ['package' => '__ID__']));
                document.getElementById('deletePackageForm').action = deleteUrlTemplate.replace('__ID__', id);
                document.getElementById('deletePackageName').textContent =
                    `You are about to permanently delete "${name}".`;
                openModal('deletePackageModal');
            }

            // Vehicle types
            function openEditVehicleModal(id, code, label, description, isActive) {
                const updateUrlTemplate = @json(route('admin.services.vehicle-types.update', ['vehicleType' => '__ID__']));
                document.getElementById('editVehicleForm').action = updateUrlTemplate.replace('__ID__', id);
                document.getElementById('edit_vt_code').value = code;
                document.getElementById('edit_vt_label').value = label;
                document.getElementById('edit_vt_description').value = description;
                document.getElementById('edit_vt_active').checked = !!isActive;
                openModal('editVehicleModal');
            }

            function openDeleteVehicleModal(id, code) {
                const deleteUrlTemplate = @json(route('admin.services.vehicle-types.destroy', ['vehicleType' => '__ID__']));
                document.getElementById('deleteVehicleForm').action = deleteUrlTemplate.replace('__ID__', id);
                document.getElementById('deleteVehicleName').textContent =
                    `You are about to permanently delete "${code}".`;
                openModal('deleteVehicleModal');
            }

            // Pricing (matrix)
            function openPricingModal(vehicleTypeId, packageId, entryId, price, isActive) {
                document.getElementById('pricing_vehicle_type_id').value = vehicleTypeId;
                document.getElementById('pricing_service_package_id').value = packageId;
                document.getElementById('pricing_price').value = price ?? 0;
                document.getElementById('pricing_active').checked = !!isActive;

                // If entry exists, update endpoint; else create endpoint.
                const createUrl = @json(route('admin.services.pricing.store'));
                const updateUrlTemplate = @json(route('admin.services.pricing.update', ['pricingEntry' => '__ID__']));

                if (entryId) {
                    document.getElementById('pricingModalForm').action = updateUrlTemplate.replace('__ID__', entryId);
                    setMethod('pricingModalForm', 'PUT');
                } else {
                    document.getElementById('pricingModalForm').action = createUrl;
                    setMethod('pricingModalForm', 'POST');
                }

                openModal('pricingModal');
            }

            function setMethod(formId, method) {
                const form = document.getElementById(formId);
                let methodInput = form.querySelector('input[name="_method"]');
                if (method === 'POST') {
                    if (methodInput) methodInput.remove();
                    return;
                }
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = method;
            }

            // Pricing list
            function openEditPricingEntryModal(id, price, isActive) {
                const updateUrlTemplate = @json(route('admin.services.pricing.update', ['pricingEntry' => '__ID__']));
                document.getElementById('editPricingEntryForm').action = updateUrlTemplate.replace('__ID__', id);
                document.getElementById('edit_pe_price').value = price ?? 0;
                document.getElementById('edit_pe_active').checked = !!isActive;
                openModal('editPricingEntryModal');
            }

            function openDeletePricingModal(id) {
                const deleteUrlTemplate = @json(route('admin.services.pricing.destroy', ['pricingEntry' => '__ID__']));
                document.getElementById('deletePricingForm').action = deleteUrlTemplate.replace('__ID__', id);
                openModal('deletePricingModal');
            }
        </script>
    @endpush
@endsection

