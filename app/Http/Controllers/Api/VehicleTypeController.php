<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;
use App\Http\Resources\VehicleTypeResource;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;

class VehicleTypeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $vehicleTypes = VehicleType::all();

            return response()->json([
                'success' => true,
                'data' => VehicleTypeResource::collection($vehicleTypes),
                'message' => 'Vehicle types retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id): JsonResponse
    {
        try {
            $vehicleType = VehicleType::find($id);

            if (!$vehicleType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new VehicleTypeResource($vehicleType),
                'message' => 'Vehicle type retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreVehicleTypeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);

            $vehicleType = VehicleType::create($validated);

            return response()->json([
                'success' => true,
                'data' => new VehicleTypeResource($vehicleType),
                'message' => 'Vehicle type created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create vehicle type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateVehicleTypeRequest $request, $id): JsonResponse
    {
        try {
            $vehicleType = VehicleType::find($id);

            if (!$vehicleType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            $vehicleType->update($request->validated());

            return response()->json([
                'success' => true,
                'data' => new VehicleTypeResource($vehicleType),
                'message' => 'Vehicle type updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vehicle type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $vehicleType = VehicleType::find($id);

            if (!$vehicleType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle type not found'
                ], 404);
            }

            $deletedData = $vehicleType->toArray();

            $vehicleType->forceDelete();

            return response()->json([
                'success' => true,
                'data'    => $deletedData,
                'message' => 'Vehicle type deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vehicle type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
