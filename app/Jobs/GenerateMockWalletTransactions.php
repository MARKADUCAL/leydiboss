<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateMockWalletTransactions implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $role       One of: customer, admin, manager, super_admin
     * @param int    $maxPerUser Maximum number of transactions to create per user (min 1)
     */
    public function __construct(
        public readonly string $role,
        public readonly int    $maxPerUser,
    ) {}

    public function handle(): void
    {
        $descriptions = [
            'Service payment received',
            'Refund processed',
            'Balance top-up',
            'Withdrawal request',
            'Bonus credit applied',
            'Penalty deduction',
            'Monthly subscription fee',
            'Promotional credit',
            'Cashback reward',
            'Adjustment correction',
        ];

        $users = $this->getUsers();

        foreach ($users as $user) {
            // Random count between 1 and $maxPerUser
            $count = rand(1, max(1, $this->maxPerUser));

            for ($i = 0; $i < $count; $i++) {
                $type  = fake()->randomElement(['debit', 'credit']);
                $value = round(fake()->randomFloat(2, 10.00, 5000.00), 2);

                $payload = [
                    'description' => fake()->randomElement($descriptions),
                    'type'        => $type,
                    'value'       => $value,
                ];

                if ($this->role === 'customer') {
                    $payload['customer_id'] = $user->id;
                    $payload['admin_id']    = null;
                } else {
                    $payload['admin_id']    = $user->id;
                    $payload['customer_id'] = null;
                }

                WalletTransaction::create($payload);
            }

            Log::info("[GenerateMockWalletTransactions] Created {$count} transactions for {$this->role} ID {$user->id}");
        }

        Log::info("[GenerateMockWalletTransactions] Completed for role: {$this->role}");
    }

    /**
     * Resolve the list of user models based on role.
     */
    private function getUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return match ($this->role) {
            'customer'    => Customer::all(),
            'admin'       => Admin::where('role', Admin::ROLE_ADMIN)->get(),
            'manager'     => Admin::where('role', Admin::ROLE_MANAGER)->get(),
            'super_admin' => Admin::where('role', Admin::ROLE_SUPER_ADMIN)->get(),
            default       => collect(),
        };
    }
}
