<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $pageData = [
            'welcome'  => 'Welcome Back, Admin!',
            'tagline'  => 'Manage bookings, staff, and services all in one place.',
            'promo'    => '🔒 This is a restricted area. Authorized personnel only.',
            'stats'    => [
                ['icon' => '📋', 'label' => 'Bookings'],
                ['icon' => '👥', 'label' => 'Customers'],
                ['icon' => '🚗', 'label' => 'Services'],
                ['icon' => '📊', 'label' => 'Reports'],
            ],
            'hours' => [
                'weekdays' => 'Mon – Fri: 8:00 AM – 6:00 PM',
                'saturday' => 'Saturday:  8:00 AM – 5:00 PM',
                'sunday'   => 'Sunday:    Closed',
            ],
        ];

        return view('auth.admin.login', compact('pageData'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}