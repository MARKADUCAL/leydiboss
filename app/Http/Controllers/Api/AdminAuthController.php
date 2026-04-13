<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Traits\RoleBasedAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    use RoleBasedAccess;
    /**
     * Register a new admin.
     */
    public function register(AdminRegisterRequest $request): JsonResponse
    {
        try {
            // Create the admin
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? Admin::ROLE_MANAGER, // Default role
                'balance' => 0, // Initial balance
            ]);

            // Create API token for the admin
            $token = $admin->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Admin registered successfully',
                'admin' => new AdminResource($admin),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login an admin.
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        try {
            // Find the admin by email
            $admin = Admin::where('email', $request->email)->first();

            // Check if admin exists and password is correct
            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            // Create API token
            $token = $admin->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'admin' => new AdminResource($admin),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout an admin (revoke token).
     */
    public function logout(): JsonResponse
    {
        try {
            auth('sanctum')->user()?->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated admin profile.
     */
    public function profile(): JsonResponse
    {
        try {
            $admin = auth('sanctum')->user();

            if (!$admin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            return response()->json([
                'message' => 'Profile retrieved successfully',
                'data' => new AdminResource($admin),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(UpdateAdminProfileRequest $request): JsonResponse
    {
        try {
            $admin = auth('sanctum')->user();

            if (!$admin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $validated = $request->validated();

            // Only super admins can update the role
            if ($request->has('role') && !$this->isSuperAdmin()) {
                return $this->forbiddenResponse('Forbidden - Only super admins can update role', 'super_admin');
            }

            $admin->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new AdminResource($admin),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete admin profile.
     */
    public function deleteProfile(): JsonResponse
    {
        try {
            $admin = auth('sanctum')->user();

            if (!$admin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Revoke all tokens
            $admin->tokens()->forceDelete();

            // Delete the admin in DB
            $admin->forceDelete();

            return response()->json([
                'message' => 'Profile deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update another admin's profile (Super Admin Only).
     */
    public function updateAdmin(UpdateAdminProfileRequest $request, $id): JsonResponse
    {
        try {
            $authenticatedAdmin = auth('sanctum')->user();

            if (!$authenticatedAdmin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Only super admins can update other admins
            if (!$this->isSuperAdmin()) {
                return $this->forbiddenResponse('Forbidden - Only super admins can update other admins', 'super_admin');
            }

            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json([
                    'message' => 'Admin not found',
                ], 404);
            }

            $validated = $request->validated();
            $admin->update($validated);

            return response()->json([
                'message' => 'Admin profile updated successfully',
                'data' => new AdminResource($admin),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update admin profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete another admin (Super Admin Only).
     */
    public function deleteAdmin($id): JsonResponse
    {
        try {
            $authenticatedAdmin = auth('sanctum')->user();

            if (!$authenticatedAdmin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Only super admins can delete other admins
            if (!$this->isSuperAdmin()) {
                return $this->forbiddenResponse('Forbidden - Only super admins can delete other admins', 'super_admin');
            }

            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json([
                    'message' => 'Admin not found',
                ], 404);
            }

            // Prevent super admin from deleting themselves
            if ($admin->id === $authenticatedAdmin->id) {
                return response()->json([
                    'message' => 'Cannot delete your own account',
                ], 400);
            }

            // Revoke all tokens
            $admin->tokens()->forceDelete();

            // Delete the admin
            $admin->forceDelete();

            return response()->json([
                'message' => 'Admin deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all admins (Super Admin Only).
     */
    public function index(): JsonResponse
    {
        try {
            $authenticatedAdmin = auth('sanctum')->user();

            if (!$authenticatedAdmin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Only super admins can view all admins
            if (!$this->isSuperAdmin()) {
                return $this->forbiddenResponse('Forbidden - Only super admins can view all admins', 'super_admin');
            }

            $admins = Admin::all();

            return response()->json([
                'message' => 'Admins retrieved successfully',
                'data' => AdminResource::collection($admins),
                'count' => $admins->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve admins',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific admin (Super Admin Only).
     */
    public function show($id): JsonResponse
    {
        try {
            $authenticatedAdmin = auth('sanctum')->user();

            if (!$authenticatedAdmin) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Only super admins can view other admin details
            if (!$this->isSuperAdmin()) {
                return $this->forbiddenResponse('Forbidden - Only super admins can view other admin details', 'super_admin');
            }

            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json([
                    'message' => 'Admin not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Admin retrieved successfully',
                'data' => new AdminResource($admin),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
