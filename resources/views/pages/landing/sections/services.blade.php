{{-- Services & Pricing Section --}}
<section class="services-pricing" id="services">
    <div class="sp-container">

        {{-- Section Header --}}
        <div class="sp-header">
            <h2 class="sp-title">Service Packages &amp; Pricing</h2>
            <p class="sp-subtitle">Choose the perfect service package for your vehicle</p>
        </div>

        {{-- Service Package Details --}}
        <div class="sp-block">
            <h3 class="sp-block-title">Service Package Details</h3>
            <div class="sp-cards-grid">
                @foreach ($services['packages'] as $package)
                    <div class="sp-card">
                        <h4 class="sp-card-name">{{ $package['name'] }}</h4>
                        <p class="sp-card-desc">{{ $package['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Vehicle Type Classification --}}
        <div class="sp-block sp-block--accent">
            <h3 class="sp-block-title sp-block-title--accent">Vehicle Type Classification</h3>
            <div class="sp-cards-grid">
                @foreach ($services['vehicle_types'] as $type)
                    <div class="sp-card">
                        <h4 class="sp-card-size">{{ $type['size'] }}</h4>
                        <p class="sp-card-desc">{{ $type['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pricing Matrix --}}
        <div class="sp-matrix-wrap">
            <h3 class="sp-title sp-matrix-heading">Pricing Matrix</h3>
            <div class="sp-table-wrap">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th class="sp-th sp-th--vehicle">Vehicle</th>
                            @foreach ($services['packages'] as $package)
                                <th class="sp-th">
                                    <span class="sp-th-name">{{ $package['name'] }}</span>
                                    <span class="sp-th-desc">{{ $package['description'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services['pricing_matrix'] as $row)
                            <tr class="sp-tr">
                                <td class="sp-td sp-td--vehicle">
                                    <span class="sp-vehicle-badge">{{ $row['vehicle'] }}</span>
                                </td>
                                @foreach ($services['packages'] as $package)
                                    <td class="sp-td">
                                        <span class="sp-price">{{ $row['pkg' . ($loop->index + 1)] ?? '—' }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- How to Book --}}
        <div class="sp-how-to-book">
            <h4 class="sp-htb-title">How to Book</h4>
            <ol class="sp-htb-list">
                @foreach ($services['how_to_book'] as $step)
                    <li class="sp-htb-item">{{ $step }}</li>
                @endforeach
            </ol>
        </div>

    </div>
</section>
