<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerVehicleResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'vehicle_type_id' => $this->vehicle_type_id,
            'nickname' => $this->nickname,
            'model' => $this->model,
            'plate_number' => $this->plate_number,
            'color' => $this->color,
            'vehicle_type' => new VehicleTypeResource($this->whenLoaded('vehicleType')),
        ];
    }
}
