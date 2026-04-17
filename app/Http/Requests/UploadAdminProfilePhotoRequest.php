<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAdminProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.required' => 'Profile photo is required.',
            'profile_photo.image' => 'File must be an image.',
            'profile_photo.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
            'profile_photo.max' => 'Image size must not exceed 5MB.',
        ];
    }
}
