<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('customer')->user();

        $packages = [];
        $vehicleTypes = [];
        $prices = [];

        $howToBook = [
            'Add a vehicle to your profile',
            'Identify your vehicle type from the classification above',
            'Choose your preferred service package',
            'Click "Book Now" for your selected combination',
            'Complete your appointment details',
        ];

        if (
            Schema::hasTable('service_packages') &&
            Schema::hasTable('vehicle_types') &&
            Schema::hasTable('pricing_entries')
        ) {
            $packageModels = ServicePackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'description']);

            $vehicleTypeModels = VehicleType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get(['id', 'code', 'description']);

            $packages = $packageModels->map(fn ($p) => [
                'code' => $p->code,
                'label' => $p->name,
                'desc' => $p->description,
            ])->values()->all();

            $vehicleTypes = $vehicleTypeModels->map(fn ($t) => [
                'code' => $t->code,
                'desc' => $t->description,
            ])->values()->all();

            if ($packageModels->isNotEmpty() && $vehicleTypeModels->isNotEmpty()) {
                $entries = PricingEntry::query()
                    ->where('is_active', true)
                    ->whereIn('service_package_id', $packageModels->pluck('id'))
                    ->whereIn('vehicle_type_id', $vehicleTypeModels->pluck('id'))
                    ->get(['vehicle_type_id', 'service_package_id', 'price_cents']);

                $vtIdToCode = $vehicleTypeModels->pluck('code', 'id')->all();
                $pkgIdToCode = $packageModels->pluck('code', 'id')->all();

                foreach ($entries as $e) {
                    $vtCode = $vtIdToCode[$e->vehicle_type_id] ?? null;
                    $pkgCode = $pkgIdToCode[$e->service_package_id] ?? null;
                    if (!$vtCode || !$pkgCode) {
                        continue;
                    }
                    $prices[$vtCode][$pkgCode] = '₱' . number_format(($e->price_cents ?? 0) / 100, 2);
                }
            }
        }

        return view('pages.customer.sections.dashboard', compact(
            'user',
            'packages',
            'vehicleTypes',
            'prices',
            'howToBook'
        ));
    }
}