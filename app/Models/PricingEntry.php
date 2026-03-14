<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pricing_entries';

    protected $fillable = [
        'vehicle_type_id',
        'service_package_id',
        'price_cents',
        'is_active',
    ];

    protected $appends = [
        'price',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price_cents' => 'integer',
        ];
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function getPriceAttribute(): float
    {
        return round(($this->price_cents ?? 0) / 100, 2);
    }
}

