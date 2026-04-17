<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\UpdateCustomerProfileRequest;
use App\Http\Requests\UploadCustomerProfilePhotoRequest;
use App\Http\Resources\CustomerResource;
use App\Mail\WelcomeEmail;
use App\Models\Customer;
use App\Traits\RoleBasedAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CustomerAuthController extends Controller
{
    use RoleBasedAccess;
    /**
     * Get all customers.
     * Manager: Cannot access (403)
     * Admin & Super Admin: Can access
     */
    public function index(): JsonResponse
    {
        try {
            $admin = auth('sanctum')->user();

            // Check role-based access
            if ($admin->role === 'manager') {
                return $this->forbiddenResponse('Managers cannot access customer data', ['admin', 'super_admin']);
            }

            $customers = Customer::all();

            return response()->json([
                'message' => 'Customers retrieved successfully',
                'data' => CustomerResource::collection($customers),
                'count' => $customers->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve customers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific customer by ID.
     * Manager: Cannot access (403)
     * Admin & Super Admin: Can access
     */
    public function show($id): JsonResponse
    {
        try {
            $admin = auth('sanctum')->user();

            // Check role-based access
            if ($admin->role === 'manager') {
                return $this->forbiddenResponse('Managers cannot access customer data', ['admin', 'super_admin']);
            }

            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Customer retrieved successfully',
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register a new customer.
     */
    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        try {
            // Create the customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'balance' => 0, // Initial balance
            ]);

            // Send welcome email
            Mail::to($customer->email)->send(new WelcomeEmail($customer));

            // Create API token for the customer
            $token = $customer->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Customer registered successfully',
                'customer' => new CustomerResource($customer),
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
     * Login a customer.
     */
    public function login(CustomerLoginRequest $request): JsonResponse
    {
        try {
            // Find the customer by email
            $customer = Customer::where('email', $request->email)->first();

            // Check if customer exists and password is correct
            if (!$customer || !Hash::check($request->password, $customer->password)) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            // Create API token
            $token = $customer->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'customer' => new CustomerResource($customer),
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
     * Logout a customer (revoke token).
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
     * Get authenticated customer profile.
     */
    public function profile(): JsonResponse
    {
        try {
            $customer = auth('sanctum')->user();

            if (!$customer) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            return response()->json([
                'message' => 'Profile retrieved successfully',
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(UpdateCustomerProfileRequest $request): JsonResponse
    {
        try {
            $customer = auth('sanctum')->user();

            if (!$customer) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            $validated = $request->validated();
            $customer->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete customer profile.
     */
    public function deleteProfile(): JsonResponse
    {
        try {
            $customer = auth('sanctum')->user();

            if (!$customer) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Revoke all tokens
            $customer->tokens()->forcedelete();

            //  delete the customer in DB
            $customer->forcedelete();

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
     * Upload customer profile photo.
     */
    public function uploadProfilePhoto(UploadCustomerProfilePhotoRequest $request): JsonResponse
    {
        try {
            $customer = auth('sanctum')->user();

            if (!$customer) {
                return response()->json([
                    'message' => 'Unauthorized - Please login first',
                ], 401);
            }

            // Delete old profile photo if it exists
            if ($customer->profile_photo_path && Storage::disk('public')->exists($customer->profile_photo_path)) {
                Storage::disk('public')->delete($customer->profile_photo_path);
            }

            // Store new profile photo
            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');

            // Update customer's profile photo path
            $customer->update(['profile_photo_path' => $photoPath]);

            return response()->json([
                'message' => 'Profile photo uploaded successfully',
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload profile photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
