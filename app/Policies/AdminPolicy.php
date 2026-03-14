<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    /**
     * Determine whether the admin can view the list of admins (super_admin only).
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->canAccessArea('admins');
    }

    /**
     * Determine whether the admin can view another admin (super_admin only).
     */
    public function view(Admin $admin, Admin $model): bool
    {
        return $admin->canAccessArea('admins');
    }

    /**
     * Determine whether the admin can create other admins (super_admin only).
     */
    public function create(Admin $admin): bool
    {
        return $admin->canAccessArea('admins');
    }

    /**
     * Determine whether the admin can update another admin (super_admin only).
     */
    public function update(Admin $admin, Admin $model): bool
    {
        return $admin->canAccessArea('admins');
    }

    /**
     * Determine whether the admin can delete another admin (super_admin only).
     */
    public function delete(Admin $admin, Admin $model): bool
    {
        return $admin->canAccessArea('admins');
    }
}
