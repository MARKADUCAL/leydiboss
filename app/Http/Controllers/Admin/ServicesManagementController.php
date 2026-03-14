<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServicesManagementController extends Controller
{
    public function index()
    {
        $title = 'Services Management — Admin';

        $vehicleTypes = VehicleType::query()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $packages = ServicePackage::query()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $pricingEntries = PricingEntry::query()
            ->with(['vehicleType', 'servicePackage'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $matrix = PricingEntry::query()
            ->get()
            ->groupBy(['vehicle_type_id', 'service_package_id']);

        return view('pages.admin.sections.services', compact(
            'title',
            'vehicleTypes',
            'packages',
            'pricingEntries',
            'matrix',
        ));
    }

    // ── Vehicle Types ──────────────────────────────────────────────────────────
    public function storeVehicleType(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:vehicle_types,code'],
            'label' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        VehicleType::create([
            'code' => strtoupper(trim($data['code'])),
            'label' => $data['label'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Vehicle type added.');
    }

    public function updateVehicleType(Request $request, VehicleType $vehicleType)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('vehicle_types', 'code')->ignore($vehicleType->id)],
            'label' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $vehicleType->update([
            'code' => strtoupper(trim($data['code'])),
            'label' => $data['label'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Vehicle type updated.');
    }

    public function destroyVehicleType(VehicleType $vehicleType)
    {
        $vehicleType->delete();

        return redirect()->route('admin.services.index')->with('success', 'Vehicle type deleted.');
    }

    // ── Service Packages ───────────────────────────────────────────────────────
    public function storePackage(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:service_packages,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ServicePackage::create([
            'code' => strtolower(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service package added.');
    }

    public function updatePackage(Request $request, ServicePackage $package)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('service_packages', 'code')->ignore($package->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $package->update([
            'code' => strtolower(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service package updated.');
    }

    public function destroyPackage(ServicePackage $package)
    {
        $package->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service package deleted.');
    }

    // ── Pricing ────────────────────────────────────────────────────────────────
    public function storePricing(Request $request)
    {
        $data = $request->validate([
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'service_package_id' => ['required', 'integer', 'exists:service_packages,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        PricingEntry::updateOrCreate(
            [
                'vehicle_type_id' => $data['vehicle_type_id'],
                'service_package_id' => $data['service_package_id'],
            ],
            [
                'price_cents' => (int) round(((float)$data['price']) * 100),
                'is_active' => (bool)($data['is_active'] ?? false),
            ]
        );

        return redirect()->route('admin.services.index')->with('success', 'Pricing saved.');
    }

    public function updatePricing(Request $request, PricingEntry $pricingEntry)
    {
        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $pricingEntry->update([
            'price_cents' => (int) round(((float)$data['price']) * 100),
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Pricing updated.');
    }

    public function destroyPricing(PricingEntry $pricingEntry)
    {
        $pricingEntry->delete();

        return redirect()->route('admin.services.index')->with('success', 'Pricing entry deleted.');
    }
}

