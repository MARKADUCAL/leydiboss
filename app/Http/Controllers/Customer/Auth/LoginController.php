<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $pageData = [
            'welcome'  => 'Welcome Back!',
            'tagline'  => 'Your car deserves the best — sign in to book your next detail.',
            'promo'    => '🎉 This month: 20% off Full Detail packages for returning customers!',
            'services' => [
                ['icon' => '🚿', 'label' => 'Basic Wash'],
                ['icon' => '✨', 'label' => 'Full Detail'],
                ['icon' => '🪟', 'label' => 'Window Tint'],
                ['icon' => '🛡️', 'label' => 'Paint Protection'],
            ],
            'hours' => [
                'weekdays' => 'Mon – Fri: 8:00 AM – 6:00 PM',
                'saturday' => 'Saturday:  8:00 AM – 5:00 PM',
                'sunday'   => 'Sunday:    Closed',
            ],
        ];

        return view('auth.customer.login', compact('pageData'));
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('customer.index'))
            ->with('login_success', true);
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
    Auth::guard('customer')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('customer.login')
        ->with('logout_success', true); 
}
    
}