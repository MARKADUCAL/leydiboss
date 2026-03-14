<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        $servicePackages = ServicePackage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $pricingEntries = PricingEntry::query()
            ->where('is_active', true)
            ->get(['vehicle_type_id', 'service_package_id', 'price_cents']);

        $currentDate = Carbon::now();
        $currentTime = $currentDate->format('H:i'); // Format: 14:30

        return view('pages.customer.sections.appointment', [
            'vehicles' => $vehicles,
            'servicePackages' => $servicePackages,
            'pricingEntries' => $pricingEntries,
            'currentDate' => $currentDate,
            'currentTime' => $currentTime,
        ]);
    }

    public function show(int $id)
    {
        return view('pages.customer.sections.appointment', compact('id'));
    }
}