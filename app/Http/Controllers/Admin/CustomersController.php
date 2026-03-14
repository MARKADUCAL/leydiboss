<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomersController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Get per_page from request, default to 10, max 100
        $perPage = min((int) $request->input('per_page', 10), 100);

        $customers = $query->latest()->paginate($perPage)->withQueryString();

        return view('pages.admin.sections.customers', [
            'title'     => 'User Management — Admin',
            'customers' => $customers,
            'search'    => $search,
        ]);
    }

    // ── Store (Create) ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:customers,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Customer::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    // ── Update (Edit) ─────────────────────────────────────────────────────────
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $customer->name  = $data['name'];
        $customer->email = $data['email'];
        $customer->phone_number = $data['phone_number'] ?? $customer->phone_number;

        if (!empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }

        $customer->save();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    // ── Destroy (Delete) ──────────────────────────────────────────────────────
    public function destroy(Customer $customer)
    {
        $customer->forceDelete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted.');
    }
}