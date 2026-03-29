<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_MANAGER = 'manager';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    /** Roles that can access each area. Super admin can access all. */
    private const AREA_ROLES = [
        'dashboard' => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
        'services'  => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
        'customers' => [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
        'admins'    => [self::ROLE_SUPER_ADMIN],
    ];

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'profile_photo_path',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo_path) {
            return null;
        }
        return asset('storage/' . ltrim($this->profile_photo_path, '/'));
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if this admin can access an area: dashboard, services, customers, admins.
     */
    public function canAccessArea(string $area): bool
    {
        $allowed = self::AREA_ROLES[$area] ?? [];

        return in_array($this->role, $allowed, true);
    }

    /**
     * All role values for dropdowns.
     */
    public static function roles(): array
    {
        return [
            self::ROLE_MANAGER     => 'Manager',
            self::ROLE_ADMIN       => 'Admin',
            self::ROLE_SUPER_ADMIN => 'Super Admin',
        ];
    }
}

