<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'vehicle_type_id' => 'sometimes|required|exists:vehicle_types,id',
            'nickname' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'plate_number' => 'sometimes|required|string|unique:vehicles,plate_number,' . $vehicleId,
            'color' => 'sometimes|required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_type_id.exists' => 'Selected vehicle type does not exist.',
            'plate_number.unique' => 'This plate number is already registered.',
        ];
    }
}
