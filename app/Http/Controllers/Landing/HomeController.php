<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Hero Data
        $hero = [
            'title' => 'Welcome to Leydi Boss',
            'description' => 'Your trusted car wash booking system.',
            'buttonText' => 'Book Now',
            'buttonLink' => '#services',
            'backgroundImage' => asset('3.jpg')
        ];



        // Get data from other controllers
        $galleries = (new GalleryController())->getData();
        $services = (new ServicesController())->getData();
        $contactInfo = (new ContactController())->getData();
        $topbarData = (new TopbarController())->getData();
        $footerData = (new FooterController())->getData();

        return view('pages.landing.index', compact('hero',  'galleries', 'services', 'contactInfo', 'topbarData', 'footerData'));
    }
}