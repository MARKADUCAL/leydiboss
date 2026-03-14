<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function getData()
    {
        return [
            [
                'icon' => 'fa-map-marker-alt',
                'title' => 'Address',
                'content' => '123 Main Street, City, State 12345'
            ],
            [
                'icon' => 'fa-phone',
                'title' => 'Phone',
                'content' => '+1 (555) 123-4567'
            ],
            [
                'icon' => 'fa-envelope',
                'title' => 'Email',
                'content' => 'info@leydiboss.com'
            ],
            [
                'icon' => 'fa-clock',
                'title' => 'Hours',
                'content' => 'Mon-Fri: 8:00 AM - 6:00 PM<br>Sat-Sun: 9:00 AM - 5:00 PM'
            ]
        ];
    }

    public function index()
    {
        $contactInfo = $this->getData();
        return view('pages.landing.sections.contact', compact('contactInfo'));
    }

    public function store()
    {
        // Handle form submission
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'required|string'
        ]);

        // TODO: Save to database or send email
        
        return back()->with('success', 'Thank you for your message!');
    }
}
