<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class AdminApiMiddleware
{
    /**
     * Check if the authenticated user is an Admin.
     * Used for Sanctum API authentication.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof Admin)) {
            return response()->json([
                'message' => 'Unauthorized - Admin access required',
            ], 403);
        }

        return $next($request);
    }
}
