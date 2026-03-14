<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        $pageData = [
            'welcome'  => 'Join Us Today!',
            'tagline'  => 'Create an account and start booking premium car care services.',
            'promo'    => '🎁 New members get 15% off their first booking — sign up now!',
            'perks'    => [
                ['icon' => '📅', 'label' => 'Easy Booking'],
                ['icon' => '🔔', 'label' => 'Reminders'],
                ['icon' => '💳', 'label' => 'Exclusive Deals'],
                ['icon' => '⭐', 'label' => 'Loyalty Points'],
            ],
            'hours' => [
                'weekdays' => 'Mon – Fri: 8:00 AM – 6:00 PM',
                'saturday' => 'Saturday:  8:00 AM – 5:00 PM',
                'sunday'   => 'Sunday:    Closed',
            ],
        ];

        return view('auth.customer.register', compact('pageData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'phone_number' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->intended(route('customer.index'));
    }
}