<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        $pageData = [
            'welcome'  => 'Create Admin Account',
            'tagline'  => 'Only authorized personnel may register an admin account.',
            'promo'    => '🛡️ Admin accounts require approval before activation.',
            'perks'    => [
                ['icon' => '⚙️',  'label' => 'Full Control'],
                ['icon' => '📈',  'label' => 'Analytics'],
                ['icon' => '🔔',  'label' => 'Alerts'],
                ['icon' => '🛡️', 'label' => 'Secure Access'],
            ],
            'hours' => [
                'weekdays' => 'Mon – Fri: 8:00 AM – 6:00 PM',
                'saturday' => 'Saturday:  8:00 AM – 5:00 PM',
                'sunday'   => 'Sunday:    Closed',
            ],
        ];

        return view('auth.admin.register', compact('pageData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'phone_number' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->intended(route('admin.dashboard'));
    }
}