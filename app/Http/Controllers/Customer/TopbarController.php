<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopbarController extends Controller
{
    /**
     * Share topbar view data with every customer page.
     *
     * Intended to be called from a View Composer registered in
     * App\Providers\AppServiceProvider (or a dedicated ComposerServiceProvider):
     *
     *   View::composer('customer.*', fn($v) => $v->with(
     *       app(TopbarController::class)->topbarData()
     *   ));
     *
     * @return array{
     *     pageTitle:    string,
     *     userName:     string,
     *     userInitials: string,
     *     userAvatar:   string|null
     * }
     */
    public function topbarData(): array
    {
        $user = Auth::user();

        return [
            'pageTitle'    => $this->resolvePageTitle(),
            'userName'     => $user?->name     ?? 'Guest',
            'userInitials' => $this->initials($user?->name ?? 'G'),
            'userAvatar'   => $user?->avatar_url ?? null,   // null → show initials circle
        ];
    }

    // ---------------------------------------------------------------
    //  Private helpers
    // ---------------------------------------------------------------

    /**
     * Map the current route name to a human-readable page title.
     */
    private function resolvePageTitle(): string
    {
        $titles = [
            'customer.booking.index'      => 'Booking',
            'customer.booking.create'     => 'New Booking',
            'customer.appointment.index'  => 'Appointments',
            'customer.transactions.index' => 'Transaction History',
            'customer.profile.index'      => 'My Profile',
        ];

        $current = request()->route()?->getName() ?? '';

        return $titles[$current] ?? 'Customer Dashboard';
    }

    /**
     * Generate up to two uppercase initials from a full name.
     *
     * "Mark Aducal" → "MA"
     * "Madonna"     → "MA" (first two chars, uppercased)
     */
    private function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));

        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }

        return strtoupper(mb_substr($words[0], 0, 2));
    }
}