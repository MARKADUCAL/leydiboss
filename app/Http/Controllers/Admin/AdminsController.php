<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminsController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Admin::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        // Get per_page from request, default to 10, max 100
        $perPage = min((int) $request->input('per_page', 10), 100);

        $admins = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        return view('pages.admin.sections.admins', [
            'title'  => 'Admin Management — Admin',
            'admins' => $admins,
            'search' => $search,
        ]);
    }

    // ── Store (Create) ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:admins,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
        if ($request->user('admin')->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', Rule::in(array_keys(Admin::roles()))];
        }
        $data = $request->validate($rules);

        $payload = [
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password'     => Hash::make($data['password']),
        ];
        if ($request->user('admin')->isSuperAdmin() && isset($data['role'])) {
            $payload['role'] = $data['role'];
        } else {
            $payload['role'] = Admin::ROLE_MANAGER;
        }
        $admin = Admin::create($payload);

        if ($request->hasFile('profile_photo')) {
            $newPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $admin->profile_photo_path = $newPath;
            $admin->save();
        }

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin created successfully.');
    }

    // ── Update (Edit) ─────────────────────────────────────────────────────────
    public function update(Request $request, Admin $admin)
    {
        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
        if ($request->user('admin')->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', Rule::in(array_keys(Admin::roles()))];
        }
        $data = $request->validate($rules);

        $admin->name         = $data['name'];
        $admin->email        = $data['email'];
        $admin->phone_number = $data['phone_number'] ?? $admin->phone_number;
        if ($request->user('admin')->isSuperAdmin() && isset($data['role'])) {
            $admin->role = $data['role'];
        }
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

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin updated successfully.');
    }

    // ── Destroy (Delete) ──────────────────────────────────────────────────────
    public function destroy(Admin $admin)
    {
        $admin->forceDelete();

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin deleted.');
    }
}
