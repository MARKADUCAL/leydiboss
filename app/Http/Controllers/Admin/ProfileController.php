<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        return view('pages.admin.sections.profile', [
            'admin' => $admin,
            'pageTitle' => 'My Profile',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'form_name' => ['nullable', 'string'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->phone_number = $data['phone_number'] ?? null;

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $newPath = $request->file('profile_photo')->store('profile-photos', 'public');

            if (!empty($admin->profile_photo_path)) {
                Storage::disk('public')->delete($admin->profile_photo_path);
            }

            $admin->profile_photo_path = $newPath;
        }

        $admin->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully.');
    }
}
