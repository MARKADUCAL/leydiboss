<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminAuth
{
    /**
     * Redirect unauthenticated admins to admin login.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->guest(route('admin.login'));
        }

        // Use admin as the default guard for this request so Gates/Policies see the admin user
        Auth::setDefaultDriver('admin');

        return $next($request);
    }
}

