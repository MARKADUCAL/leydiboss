<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TopbarController extends Controller
{
    public function getData()
    {
        return [
            'logo' => config('app.name', 'Leydi Boss'),
            'navLinks' => [
                [
                    'label' => 'Home',
                    'route' => 'landing.index',
                    'anchor' => '#home'
                ],
                [
                    'label' => 'Services & Pricing',
                    'route' => 'landing.index',
                    'anchor' => '#services'
                ],
                [
                    'label' => 'Gallery',
                    'route' => 'landing.index',
                    'anchor' => '#gallery'
                ],
                [
                    'label' => 'Contact',
                    'route' => 'landing.index',
                    'anchor' => '#contact'
                ]
            ],
            'buttonLabel' => 'Get Started'
        ];
    }
}
