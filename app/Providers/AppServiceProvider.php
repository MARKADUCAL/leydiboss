<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerAdminGates();
    }

    /**
     * Gates for admin RBAC. Use with @can('accessAdminCustomers') or Gate::allows('accessAdminCustomers').
     * Resolves the current admin from the admin guard.
     */
    private function registerAdminGates(): void
    {
        $areas = ['dashboard', 'services', 'customers', 'admins'];

        foreach ($areas as $area) {
            Gate::define('accessAdmin' . ucfirst($area), function (?Admin $user = null) use ($area) {
                $admin = $user instanceof Admin ? $user : Auth::guard('admin')->user();

                return $admin && $admin->canAccessArea($area);
            });
        }
    }
}
