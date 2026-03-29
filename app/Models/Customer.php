<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $keyType = 'int';

    public $incrementing = false;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-assign the next available ID on creation
        static::creating(function ($customer) {
            // Find the lowest available ID starting from 1
            $nextId = 1;
            while (static::withoutGlobalScopes()->find($nextId)) {
                $nextId++;
            }
            $customer->id = $nextId;
        });

        // Renumber IDs when a customer is deleted
        static::deleted(function ($customer) {
            $deletedId = $customer->id;

            // Shift down all customer IDs greater than the deleted one
            DB::table('customers')
                ->where('id', '>', $deletedId)
                ->decrement('id');

            // Also update any vehicles that reference this customer
            DB::table('vehicles')
                ->where('customer_id', '>', $deletedId)
                ->decrement('customer_id');
        });
    }

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

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'customer_id');
    }
}

