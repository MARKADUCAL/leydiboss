<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerVehicleRequest;
use App\Http\Requests\UpdateCustomerVehicleRequest;
use App\Http\Resources\CustomerVehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class CustomerVehicleController extends Controller
{
    /**
     * Get all vehicles for the authenticated customer.
     */
    public function index(): JsonResponse
    {
        try {
            $customerId = auth('sanctum')->id();

            if (!$customerId) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $vehicles = Vehicle::where('customer_id', $customerId)
                ->with('vehicleType')
                ->get();

            return response()->json([
                'message' => 'Vehicles retrieved successfully',
                'data' => CustomerVehicleResource::collection($vehicles),
                'count' => $vehicles->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve vehicles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single vehicle by ID.
     */
    public function show($id): JsonResponse
    {
        try {
            $customerId = auth('sanctum')->id();

            if (!$customerId) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $vehicle = Vehicle::where('customer_id', $customerId)
                ->with('vehicleType')
                ->find($id);

            if (!$vehicle) {
                return response()->json([
                    'message' => 'Vehicle not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Vehicle retrieved successfully',
                'data' => new CustomerVehicleResource($vehicle),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new vehicle for the authenticated customer.
     */
    public function store(StoreCustomerVehicleRequest $request): JsonResponse
    {
        try {
            $customerId = auth('sanctum')->id();

            if (!$customerId) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $validated = $request->validated();
            $validated['customer_id'] = $customerId;

            $vehicle = Vehicle::create($validated);
            $vehicle->load('vehicleType');

            return response()->json([
                'message' => 'Vehicle created successfully',
                'data' => new CustomerVehicleResource($vehicle),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a vehicle.
     */
    public function update(UpdateCustomerVehicleRequest $request, $id): JsonResponse
    {
        try {
            $customerId = auth('sanctum')->id();

            if (!$customerId) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $vehicle = Vehicle::where('customer_id', $customerId)->find($id);

            if (!$vehicle) {
                return response()->json([
                    'message' => 'Vehicle not found or you do not have permission to update it',
                ], 404);
            }

            $validated = $request->validated();
            $vehicle->update($validated);
            $vehicle->load('vehicleType');

            return response()->json([
                'message' => 'Vehicle updated successfully',
                'data' => new CustomerVehicleResource($vehicle),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a vehicle.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $customerId = auth('sanctum')->id();

            if (!$customerId) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $vehicle = Vehicle::where('customer_id', $customerId)->find($id);

            if (!$vehicle) {
                return response()->json([
                    'message' => 'Vehicle not found or you do not have permission to delete it',
                ], 404);
            }

            $vehicle->forcedelete();

            return response()->json([
                'message' => 'Vehicle deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete vehicle',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
