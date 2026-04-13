<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'nickname' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'color' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_type_id.required' => 'Vehicle type is required.',
            'vehicle_type_id.exists' => 'Selected vehicle type does not exist.',
            'nickname.required' => 'Vehicle nickname is required.',
            'model.required' => 'Vehicle model is required.',
            'plate_number.required' => 'Plate number is required.',
            'plate_number.unique' => 'This plate number is already registered.',
            'color.required' => 'Vehicle color is required.',
        ];
    }
}
