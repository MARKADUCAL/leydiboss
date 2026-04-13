<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adminId = auth('sanctum')->id();

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:admins,email,' . $adminId,
            'phone_number' => 'sometimes|required|string|unique:admins,phone_number,' . $adminId,
            'profile_photo_path' => 'sometimes|nullable|string',
            'role' => 'sometimes|nullable|string|in:manager,admin,super_admin',
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
