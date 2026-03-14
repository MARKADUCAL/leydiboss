<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Restrict admin routes by role. Usage: ->middleware('admin.role:admin,super_admin')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->guest(route('admin.login'));
        }

        if (!in_array($admin->role, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
