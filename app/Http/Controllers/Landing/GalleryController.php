<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class GalleryController extends Controller
{
public function getData()
{
    return [
        [
            'image' => '1.jpg',
            'title' => 'image1',
            'alt' => 'image1'
        ],
        [
            'image' => '2.jpg',
            'title' => 'image2',
            'alt' => 'image2'
        ],
        [
            'image' => '3.jpg',
            'title' => 'image3',
            'alt' => 'image3'
        ],
    ];
}

    public function index()
    {
        $galleries = $this->getData();
        return view('pages.landing.sections.gallery', compact('galleries'));
    }
}
