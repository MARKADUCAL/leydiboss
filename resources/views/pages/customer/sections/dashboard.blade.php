{{-- pages/customer/sections/dashboard.blade.php --}}
@extends('layouts.customer')

@section('title', 'Customer Dashboard')

@push('styles')
    @vite(['resources/css/auth/customer/dashboard.css'])
@endpush

@section('content')

    {{-- ── Welcome Hero Banner ── --}}
    <div class="db-hero">
        <div class="db-hero__left">
            <h1 class="db-hero__title">Welcome, {{ $user->name ?? 'Customer' }}</h1>
        </div>
        <div class="db-hero__center">
            <p class="db-hero__subtitle">
                View our comprehensive car wash service pricing based on your vehicle type and service package.
            </p>
            <a href="{{ route('customer.appointment.index') }}" class="db-hero__btn">Book a Service Now</a>
        </div>
    </div>

    {{-- ── Service Packages & Pricing ── --}}
    <div class="db-pricing">

        <div class="db-pricing__header">
            <h2 class="db-pricing__title">Service Packages & Pricing</h2>
            <p class="db-pricing__subtitle">Choose the perfect service package for your vehicle</p>
        </div>

        {{-- Service Package Details --}}
        <div class="db-pricing__card">
            <h3 class="db-pricing__section-title">Service Package Details</h3>
            <div class="db-pkg-grid">
                @foreach ($packages as $package)
                    <div class="db-pkg-item">
                        <span class="db-pkg-item__name">{{ $package['label'] }}</span>
                        <span class="db-pkg-item__desc">{{ $package['desc'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Vehicle Type Classification --}}
        <div class="db-pricing__card db-pricing__card--blue">
            <h3 class="db-pricing__section-title db-pricing__section-title--blue">Vehicle Type Classification</h3>
            <div class="db-vtype-grid">
                @foreach ($vehicleTypes as $type)
                    <div class="db-vtype-item">
                        <span class="db-vtype-item__label">{{ $type['code'] }}</span>
                        <span class="db-vtype-item__desc">{{ $type['desc'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pricing Matrix --}}
        <div class="db-pricing__card">
            <h3 class="db-pricing__section-title">Pricing Matrix</h3>
            <div class="db-matrix-wrap">
                <table class="db-matrix">
                    <thead>
                        <tr>
                            <th class="db-matrix__vtype-head">Vehicle Type</th>
                            @foreach ($packages as $package)
                                <th>
                                    <span class="db-matrix__pkg-name">{{ $package['label'] }}</span>
                                    <span class="db-matrix__pkg-desc">{{ $package['desc'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicleTypes as $type)
                            <tr>
                                <td class="db-matrix__vtype">
                                    <span class="db-matrix__vtype-label">{{ $type['code'] }}</span>
                                    <span class="db-matrix__vtype-desc">{{ $type['desc'] }}</span>
                                </td>
                                @foreach ($packages as $package)
                                    <td class="db-matrix__price">
                                        {{ $prices[$type['code']][$package['code']] ?? '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- How to Book --}}
        <div class="db-pricing__card db-howto">
            <h3 class="db-howto__title">How to Book</h3>
            <ol class="db-howto__list">
                @foreach ($howToBook as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </div>

    </div>

@endsection
