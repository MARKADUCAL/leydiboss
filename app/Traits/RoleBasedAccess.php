<?php

namespace App\Traits;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;

trait RoleBasedAccess
{
    /**
     * Check if authenticated admin has a specific role.
     */
    protected function hasRole($role): bool
    {
        $admin = auth('sanctum')->user();
        return $admin && $admin->role === $role;
    }

    /**
     * Check if authenticated admin has one of multiple roles.
     */
    protected function hasAnyRole($roles): bool
    {
        $admin = auth('sanctum')->user();
        return $admin && in_array($admin->role, (array) $roles, true);
    }

    /**
     * Check if authenticated admin is a super admin.
     */
    protected function isSuperAdmin(): bool
    {
        $admin = auth('sanctum')->user();
        return $admin && $admin->role === Admin::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if authenticated admin is at least an admin (not manager).
     */
    protected function isAdminOrHigher(): bool
    {
        $admin = auth('sanctum')->user();
        return $admin && in_array($admin->role, [Admin::ROLE_ADMIN, Admin::ROLE_SUPER_ADMIN], true);
    }

    /**
     * Return 403 Forbidden response with role information.
     */
    protected function forbiddenResponse($message, $requiredRole = null): JsonResponse
    {
        $admin = auth('sanctum')->user();
        $response = [
            'message' => $message,
            'your_role' => $admin?->role,
        ];

        if ($requiredRole) {
            $response['required_role'] = is_array($requiredRole) ? implode(', ', $requiredRole) : $requiredRole;
        }

        return response()->json($response, 403);
    }
}
