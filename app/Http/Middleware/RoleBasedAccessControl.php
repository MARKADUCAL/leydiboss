<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedAccessControl
{
    /**
     * Define role-based access for routes.
     * Role hierarchy: manager < admin < super_admin
     */
    private $rolePermissions = [
        // Customer access routes
        'customers.index' => ['admin', 'super_admin'],
        'customers.show' => ['admin', 'super_admin'],
        
        // Admin management routes (Super Admin only)
        'admins.index' => ['super_admin'],
        'admins.show' => ['super_admin'],
        'admins.update' => ['super_admin'],
        'admins.delete' => ['super_admin'],
        
        // Service management (all admin roles)
        'services.manage' => ['manager', 'admin', 'super_admin'],
    ];

    /**
     * Check if the authenticated user is an Admin and has role-based access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = auth('sanctum')->user();

        // Check if authenticated user is an Admin
        if (!$user || !($user instanceof Admin)) {
            return response()->json([
                'message' => 'Unauthorized - Admin access required',
            ], 403);
        }

        // If a specific permission is requested, check if user has it
        if ($permission) {
            $allowedRoles = $this->rolePermissions[$permission] ?? [];
            
            if (!in_array($user->role, $allowedRoles, true)) {
                return response()->json([
                    'message' => 'Forbidden - Insufficient permissions for this action',
                    'required_role' => implode(', ', $allowedRoles),
                    'your_role' => $user->role,
                ], 403);
            }
        }

        return $next($request);
    }
}
