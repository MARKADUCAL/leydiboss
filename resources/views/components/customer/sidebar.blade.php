{{-- sidebar.blade.php --}}
<aside class="lb-sidebar">

    {{-- Brand / Logo --}}
    <div class="lb-sidebar__brand">
        <div class="lb-sidebar__logo-wrap">
            <img src="{{ asset('logo.png') }}" alt="Leydi Boss Logo" class="lb-sidebar__logo">
        </div>
        <span class="lb-sidebar__brand-name">Leydi Boss</span>
    </div>

    {{-- Navigation --}}
    <nav class="lb-sidebar__nav">
        <ul class="lb-sidebar__menu">

            {{-- Booking → Dashboard --}}
            <li class="lb-sidebar__item {{ request()->routeIs('customer.index') ? 'lb-sidebar__item--active' : '' }}">
                <a href="{{ route('customer.index') }}" class="lb-sidebar__link">
                    <span class="lb-sidebar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20"
                            height="20">
                            <path fill-rule="evenodd"
                                d="M5.25 2.25a3 3 0 00-3 3v4.318a3 3 0 00.879 2.121l9.58 9.581c.92.92 2.39.974 3.332.12a18.61 18.61 0 005.3-7.528 2.25 2.25 0 00-.12-2.002L13.24 3.16a3 3 0 00-2.46-1.282H5.25zM6.375 7.5a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span class="lb-sidebar__label">Booking</span>
                </a>
            </li>

            {{-- Appointment --}}
            <li
                class="lb-sidebar__item {{ request()->routeIs('customer.appointment*') ? 'lb-sidebar__item--active' : '' }}">
                <a href="{{ route('customer.appointment.index') }}" class="lb-sidebar__link">
                    <span class="lb-sidebar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20"
                            height="20">
                            <path fill-rule="evenodd"
                                d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span class="lb-sidebar__label">Appointment</span>
                </a>
            </li>

            {{-- Transaction History --}}
            <li
                class="lb-sidebar__item {{ request()->routeIs('customer.transactions*') ? 'lb-sidebar__item--active' : '' }}">
                <a href="{{ route('customer.transactions.index') }}" class="lb-sidebar__link">
                    <span class="lb-sidebar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20"
                            height="20">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span class="lb-sidebar__label">Transaction History</span>
                </a>
            </li>

            <!-- {{-- My Profile --}}
            <li class="lb-sidebar__item {{ request()->routeIs('customer.profile*') ? 'lb-sidebar__item--active' : '' }}">
                <a href="{{ route('customer.profile.index') }}" class="lb-sidebar__link">
                    <span class="lb-sidebar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20"
                            height="20">
                            <path fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span class="lb-sidebar__label">My Profile</span>
                </a>
            </li> -->

        </ul>
    </nav>

</aside>
