<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Schema;

class ServicesController extends Controller
{
    public function getData(): array
    {
        $services = [
            'packages' => [],
            'vehicle_types' => [],
            'how_to_book' => [
                'Add a vehicle to your customer profile',
                'Identify your vehicle type from the classification above',
                'Choose your preferred service package',
                'Click "Book Now" for your selected combination',
                'Complete your appointment details',
            ],
            'pricing_matrix' => [],
        ];

        if (!(
            Schema::hasTable('service_packages') &&
            Schema::hasTable('vehicle_types') &&
            Schema::hasTable('pricing_entries')
        )) {
            return $services;
        }

        $packages = ServicePackage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'description']);

        $vehicleTypes = VehicleType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get(['id', 'code', 'label', 'description']);

        if ($packages->isEmpty() || $vehicleTypes->isEmpty()) {
            return $services;
        }

        $entries = PricingEntry::query()
            ->where('is_active', true)
            ->whereIn('service_package_id', $packages->pluck('id'))
            ->whereIn('vehicle_type_id', $vehicleTypes->pluck('id'))
            ->get(['vehicle_type_id', 'service_package_id', 'price_cents']);

        $entryMap = [];
        foreach ($entries as $e) {
            $entryMap[$e->vehicle_type_id][$e->service_package_id] = '₱' . number_format(($e->price_cents ?? 0) / 100, 2);
        }

        return [
            'packages' => $packages->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'includes' => [],
            ])->values()->all(),

            'vehicle_types' => $vehicleTypes->map(fn ($t) => [
                'size' => $t->code,
                'label' => $t->label,
                'description' => $t->description,
            ])->values()->all(),

            'how_to_book' => $services['how_to_book'],

            'pricing_matrix' => $vehicleTypes->map(function ($t) use ($packages, $entryMap) {
                $row = ['vehicle' => $t->code];
                $i = 1;
                foreach ($packages as $p) {
                    $row['pkg' . $i] = $entryMap[$t->id][$p->id] ?? '—';
                    $i++;
                }
                return $row;
            })->values()->all(),
        ];
    }

    public function index()
    {
        $services = $this->getData();

        return view('pages.landing.sections.services', compact('services'));
    }
}