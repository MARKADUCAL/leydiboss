<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_type_id' => $this->vehicle_type_id,
            'service_package_id' => $this->service_package_id,
            'price_cents' => $this->price_cents,
            'is_active' => $this->is_active,
            'vehicle_type' => new VehicleTypeResource($this->whenLoaded('vehicleType')),
            'service_package' => new ServicePackageResource($this->whenLoaded('servicePackage')),
        ];
    }
}
