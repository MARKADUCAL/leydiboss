<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SidebarController extends Controller
{
    /**
     * Return the data needed to render the customer sidebar.
     *
     * Called by the shared layout / view composer so every customer
     * page has access to navigation state without duplicating logic.
     *
     * @return array{
     *     brandName: string,
     *     logoUrl:   string,
     *     navItems:  array<int, array{label: string, route: string, icon: string, active: bool}>
     * }
     */
    public function sidebarData(): array
    {
        return [
            'brandName' => config('app.business_name', 'Leydi Boss'),
            'logoUrl'   => asset('logo.png'),
            'navItems'  => $this->buildNavItems(),
        ];
    }

    // ---------------------------------------------------------------
    //  Private helpers
    // ---------------------------------------------------------------

    /**
     * Build the ordered list of navigation items, marking the active
     * one based on the current route name.
     *
     * @return array<int, array{label: string, route: string, icon: string, active: bool}>
     */
    private function buildNavItems(): array
    {
        $items = [
            [
                'label' => 'Booking',
                'route' => 'customer.booking.index',
                'icon'  => 'tag',           // maps to SVG in the blade partial
            ],
            [
                'label' => 'Appointment',
                'route' => 'customer.appointment.index',
                'icon'  => 'calendar',
            ],
            [
                'label' => 'Transaction History',
                'route' => 'customer.transactions.index',
                'icon'  => 'clock',
            ],
        ];

        // Resolve the current route name once
        $currentRoute = request()->route()?->getName() ?? '';

        return array_map(function (array $item) use ($currentRoute): array {
            // Active when the current route starts with the item's base route prefix
            // e.g. "customer.booking.create" is still under the Booking nav item.
            $prefix = rtrim($item['route'], '.index');
            $item['active'] = str_starts_with($currentRoute, rtrim($prefix, '.'));
            return $item;
        }, $items);
    }
}