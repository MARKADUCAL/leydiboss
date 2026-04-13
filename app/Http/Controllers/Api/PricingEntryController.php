<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePricingEntryRequest;
use App\Http\Requests\UpdatePricingEntryRequest;
use App\Http\Resources\PricingEntryResource;
use App\Models\PricingEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PricingEntryController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $query = PricingEntry::query();

            // Optional filters
            if ($request->has('vehicle_type_id')) {
                $query->where('vehicle_type_id', $request->input('vehicle_type_id'));
            }

            if ($request->has('service_package_id')) {
                $query->where('service_package_id', $request->input('service_package_id'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $entries = $query->with(['vehicleType', 'servicePackage'])->get();

            if ($entries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'message' => 'No pricing entries found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => PricingEntryResource::collection($entries),
                'message' => 'Pricing entries retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pricing entries',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $entry = PricingEntry::with(['vehicleType', 'servicePackage'])->find($id);

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pricing entry not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PricingEntryResource($entry),
                'message' => 'Pricing entry retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pricing entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(StorePricingEntryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Check for uniqueness (vehicle_type_id + service_package_id combination)
            $exists = PricingEntry::where('vehicle_type_id', $validated['vehicle_type_id'])
                ->where('service_package_id', $validated['service_package_id'])
                ->first();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A pricing entry already exists for this vehicle type and service package combination'
                ], 422);
            }

            $validated['is_active'] = $request->boolean('is_active', true);
            // Convert price (decimal) to price_cents (integer)
            $validated['price_cents'] = (int) round($validated['price'] * 100);
            unset($validated['price']);

            $entry = PricingEntry::create($validated);
            $entry->load(['vehicleType', 'servicePackage']);

            return response()->json([
                'success' => true,
                'data' => new PricingEntryResource($entry),
                'message' => 'Pricing entry created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pricing entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdatePricingEntryRequest $request, $id): JsonResponse
    {
        try {
            $entry = PricingEntry::find($id);

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pricing entry not found'
                ], 404);
            }

            $validated = $request->validated();

            // Check for uniqueness if vehicle_type_id or service_package_id is being updated
            if (
                ($request->has('vehicle_type_id') && $validated['vehicle_type_id'] != $entry->vehicle_type_id) ||
                ($request->has('service_package_id') && $validated['service_package_id'] != $entry->service_package_id)
            ) {
                $vehicleTypeId = $validated['vehicle_type_id'] ?? $entry->vehicle_type_id;
                $servicePackageId = $validated['service_package_id'] ?? $entry->service_package_id;

                $exists = PricingEntry::where('vehicle_type_id', $vehicleTypeId)
                    ->where('service_package_id', $servicePackageId)
                    ->where('id', '!=', $id)
                    ->first();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A pricing entry already exists for this vehicle type and service package combination'
                    ], 422);
                }
            }

            // Convert price (decimal) to price_cents (integer) if provided
            if ($request->has('price')) {
                $validated['price_cents'] = (int) round($validated['price'] * 100);
                unset($validated['price']);
            }

            $entry->update($validated);
            $entry->load(['vehicleType', 'servicePackage']);

            return response()->json([
                'success' => true,
                'data' => new PricingEntryResource($entry),
                'message' => 'Pricing entry updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $entry = PricingEntry::find($id);

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pricing entry not found'
                ], 404);
            }

            $deletedData = $entry->toArray();

            $entry->forceDelete();

            return response()->json([
                'success' => true,
                'data' => $deletedData,
                'message' => 'Pricing entry deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pricing entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
