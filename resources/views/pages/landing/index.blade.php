@extends('layouts.main')

@section('title', 'Leydi Boss - Car Wash Booking System')

@section('content')

    @include('pages.landing.sections.home-hero')


    @include('pages.landing.sections.services')

    @include('pages.landing.sections.gallery')

    @include('pages.landing.sections.contact')

@endsection

@push('styles')
    @vite(['resources/css/landing/home.css', 'resources/css/landing/gallery.css', 'resources/css/landing/services-pricing.css', 'resources/css/landing/contact.css'])
@endpush
