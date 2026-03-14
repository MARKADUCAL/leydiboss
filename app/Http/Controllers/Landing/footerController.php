<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    public function getData()
    {
        return [
        'quickLinks' => [
    ['label' => 'Home',     'route' => 'landing.index'],
    ['label' => 'Services', 'route' => 'landing.services'],
    ['label' => 'Gallery',  'route' => 'landing.gallery'],
    ['label' => 'Contact',  'route' => 'landing.contact'],
],

            'contact' => [
                'address' => '21st Elicaño Street, Olongapo, Philippines',
                'phone'   => '0916 430 7531',
                'email'   => 'leydiboss@gmail.com',
                'buttonText' => 'Book Now',
            ],

            'socialLinks' => [
                ['label' => 'Facebook', 'url' => 'https://www.facebook.com/'],
            ],

            'copyrightText' => '&copy; ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.',
        ];
    }
}