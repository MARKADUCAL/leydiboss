<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Http\Resources\ServicePackageResource;
use App\Models\ServicePackage;
use Illuminate\Http\JsonResponse;

class ServicePackageController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $packages = ServicePackage::all();

            return response()->json([
                'success' => true,
                'data' => ServicePackageResource::collection($packages),
                'message' => 'Service packages retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service packages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $package = ServicePackage::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service package not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ServicePackageResource($package),
                'message' => 'Service package retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service package',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreServicePackageRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['sort_order'] = $request->input('sort_order', 0);

            $package = ServicePackage::create($validated);

            return response()->json([
                'success' => true,
                'data' => new ServicePackageResource($package),
                'message' => 'Service package created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service package',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateServicePackageRequest $request, $id): JsonResponse
    {
        try {
            $package = ServicePackage::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service package not found'
                ], 404);
            }

            $package->update($request->validated());

            return response()->json([
                'success' => true,
                'data' => new ServicePackageResource($package),
                'message' => 'Service package updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service package',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $package = ServicePackage::find($id);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service package not found'
                ], 404);
            }

            $deletedData = $package->toArray();

            $package->forceDelete();

            return response()->json([
                'success' => true,
                'data' => $deletedData,
                'message' => 'Service package deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service package',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
