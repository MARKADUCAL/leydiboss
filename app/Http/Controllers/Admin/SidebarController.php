<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SidebarController extends Controller
{
    /**
     * Resolve the active sidebar link key based on the current route.
     *
     * Returns one of: 'dashboard' | 'customers'
     */
    public static function activeLink(): string
    {
        $routeName = request()->route()?->getName() ?? '';

        return match (true) {
            str_starts_with($routeName, 'admin.customers') => 'customers',
            default                                         => 'dashboard',
        };
    }

    /**
     * Provide shared sidebar data (injected via View::composer or called directly).
     *
     * Usage in AppServiceProvider:
     *   View::composer('partials.sidebar', SidebarController::class);
     */
    public function compose(\Illuminate\View\View $view): void
    {
        $view->with([
            'sidebarActive'        => self::activeLink(),
            'totalCustomers'       => $this->getTotalCustomers(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────

    /**
     * Count all registered customers (role = 'customer').
     * Adjust the query to match your User model's structure.
     */
    private function getTotalCustomers(): int
    {
        return User::where('role', 'customer')->count();
    }
}