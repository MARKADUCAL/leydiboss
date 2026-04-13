<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateMockWalletTransactions;
use App\Jobs\UpdateRoleBalances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateUserWalletTransactionsController extends Controller
{
    /** GET /admin/wallet-transactions */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        return view('pages.admin.sections.wallet-transactions', [
            'title' => 'Wallet Transactions — Admin',
            'admin' => $admin,
        ]);
    }

    /** POST /admin/wallet-transactions/generate */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'role'    => ['required', 'string', 'in:customer,admin,manager,super_admin'],
            'max_txn' => ['required', 'integer', 'min:1', 'max:' . $request->input('max_txn_limit', 100)],
        ]);

        GenerateMockWalletTransactions::dispatch($data['role'], (int) $data['max_txn']);

        return redirect()
            ->route('admin.wallet-transactions.index')
            ->with('success', "✅ Job dispatched: generating mock transactions for role [{$data['role']}] (max {$data['max_txn']} per user).");
    }

    /** POST /admin/wallet-transactions/update-balances */
    public function updateBalances(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'in:customer,admin,manager,super_admin'],
        ]);

        UpdateRoleBalances::dispatch($data['role']);

        return redirect()
            ->route('admin.wallet-transactions.index')
            ->with('success', "✅ Job dispatched: recalculating balances for role [{$data['role']}].");
    }
}
