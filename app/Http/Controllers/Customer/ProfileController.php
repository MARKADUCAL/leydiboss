<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $vehicleTypes = VehicleType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('pages.customer.sections.profile', [
            'customer' => $customer,
            'vehicleTypes' => $vehicleTypes,
            'vehicles' => $vehicles,
            'pageTitle' => 'My Profile',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'form_name' => ['nullable', 'string'],
        ]);

        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->phone_number = $data['phone_number'] ?? null;

        if (!empty($data['password'])) {
            // Customer model has 'password' => 'hashed' cast
            $customer->password = $data['password'];
        }

        if ($request->hasFile('profile_photo')) {
            $newPath = $request->file('profile_photo')->store('profile-photos', 'public');

            if (!empty($customer->profile_photo_path)) {
                Storage::disk('public')->delete($customer->profile_photo_path);
            }

            $customer->profile_photo_path = $newPath;
        }

        $customer->save();

        return redirect()->route('customer.profile.index')->with('success', 'Profile updated.');
    }

    public function storeVehicle(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'form_name' => ['nullable', 'string'],
            'nickname' => ['required', 'string', 'max:80'],
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'model' => ['nullable', 'string', 'max:120'],
            'plate_number' => ['nullable', 'string', 'max:40'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => (int) $data['vehicle_type_id'],
            'nickname' => $data['nickname'],
            'model' => $data['model'] ?? null,
            'plate_number' => $data['plate_number'] ?? null,
            'color' => $data['color'] ?? null,
        ]);

        return redirect()->route('customer.profile.index')->with('success', 'Vehicle added.');
    }

    public function destroyVehicle(Vehicle $vehicle)
    {
        $customerId = Auth::guard('customer')->id();

        abort_unless($vehicle->customer_id === $customerId, 403);

        $vehicle->delete();

        return redirect()->route('customer.profile.index')->with('success', 'Vehicle removed.');
    }
}
