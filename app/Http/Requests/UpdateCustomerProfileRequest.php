<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = auth('sanctum')->id();

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $customerId,
            'phone_number' => 'sometimes|required|string|unique:customers,phone_number,' . $customerId,
            'profile_photo_path' => 'sometimes|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already in use.',
            'phone_number.unique' => 'This phone number is already in use.',
        ];
    }
}
