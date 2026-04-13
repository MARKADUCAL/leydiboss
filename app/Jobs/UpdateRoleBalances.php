<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRoleBalances implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $role One of: customer, admin, manager, super_admin
     */
    public function __construct(
        public readonly string $role,
    ) {}

    public function handle(): void
    {
        $users = $this->getUsers();

        foreach ($users as $user) {
            [$idColumn, $table] = $this->resolveColumns();

            // Sum all credits for this user
            $totalCredit = WalletTransaction::where($idColumn, $user->id)
                ->where('type', 'credit')
                ->sum('value');

            // Sum all debits for this user
            $totalDebit = WalletTransaction::where($idColumn, $user->id)
                ->where('type', 'debit')
                ->sum('value');

            // balance = credits − debits
            $balance = round($totalCredit - $totalDebit, 2);

            // Update balance directly on the table
            DB::table($table)
                ->where('id', $user->id)
                ->update(['balance' => $balance]);

            Log::info(
                "[UpdateRoleBalances] {$this->role} ID {$user->id} → " .
                "credit: {$totalCredit}, debit: {$totalDebit}, balance: {$balance}"
            );
        }

        Log::info("[UpdateRoleBalances] Completed balance update for role: {$this->role}");
    }

    /**
     * Returns [id_column, table] for the current role.
     */
    private function resolveColumns(): array
    {
        return $this->role === 'customer'
            ? ['customer_id', 'customers']
            : ['admin_id',    'admins'];
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
