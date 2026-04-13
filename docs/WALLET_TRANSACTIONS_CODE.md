# Wallet Transactions (Jobs UI) — Code Documentation

This document explains every relevant code piece used by the **Wallet Transactions** admin tool:
generating mock wallet transactions in the background and recalculating stored balances from that history.

---

## 1. Routes (entry points)
Defined in `routes/web.php` under the `admin` prefix:

- `GET /admin/wallet-transactions` → `CreateUserWalletTransactionsController@index`
- `POST /admin/wallet-transactions/generate` → `CreateUserWalletTransactionsController@generate`
- `POST /admin/wallet-transactions/update-balances` → `CreateUserWalletTransactionsController@updateBalances`

These routes only **dispatch queue jobs**. Actual DB updates happen when the queue worker runs.

---

## 2. UI (Blade view)
Main view:
- `resources/views/pages/admin/sections/wallet-transactions.blade.php`

What it contains:
- Two forms (two separate jobs):
  - `#form-generate` posts to `admin.wallet-transactions.generate`
    - fields:
      - `role` (`customer`, `admin`, `manager`, `super_admin`)
      - `max_txn` (max transactions per user; job picks a random number from `1..max_txn`)
  - `#form-update` posts to `admin.wallet-transactions.update-balances`
    - fields:
      - `role` (`customer`, `admin`, `manager`, `super_admin`)
- Uses `@push('styles')` and `@push('scripts')` so `layouts.admin` can render them.

---

## 3. Styling and scripts
CSS (separated from the Blade):
- `resources/css/auth/admin/wallet-transactions.css`

Loaded from the Blade via:
- `@vite(['resources/css/auth/admin/wallet-transactions.css'])`

Scripts (kept inside the Blade via `@push('scripts')`):
- Auto-dismiss flash message
- Button “loading/disabled” state when forms submit

---

## 4. Controller (dispatch logic)
File:
- `app/Http/Controllers/Admin/CreateUserWalletTransactionsController.php`

Methods:
- `index()`
  - renders `pages.admin.sections.wallet-transactions`
  - requires an authenticated admin (`Auth::guard('admin')->user()`)
- `generate(Request $request)`
  - validates:
    - `role` is one of: `customer,admin,manager,super_admin`
    - `max_txn` is an integer `>= 1`
  - dispatches:
    - `GenerateMockWalletTransactions::dispatch($data['role'], (int) $data['max_txn']);`
  - redirects back with a success message
- `updateBalances(Request $request)`
  - validates `role`
  - dispatches:
    - `UpdateRoleBalances::dispatch($data['role']);`
  - redirects back with a success message

Note: `dispatch()` only enqueues. The database changes happen when queue workers execute the jobs.

---

## 5. Queue Jobs (background work)
Both jobs implement `Illuminate\Contracts\Queue\ShouldQueue`.

### 5.1 Job 1: GenerateMockWalletTransactions
File:
- `app/Jobs/GenerateMockWalletTransactions.php`

Constructor payload:
- `role` (customer/admin/manager/super_admin)
- `maxPerUser` (max transactions per user)

Execution (`handle()`):
1. Builds a list of descriptions for fake transactions.
2. Loads target users via `getUsers()`:
   - `customer` → `Customer::all()`
   - `admin` → `Admin::where('role', Admin::ROLE_ADMIN)->get()`
   - `manager` → `Admin::where('role', Admin::ROLE_MANAGER)->get()`
   - `super_admin` → `Admin::where('role', Admin::ROLE_SUPER_ADMIN)->get()`
3. For each user:
   - chooses `count = rand(1, max(1, $maxPerUser))`
   - creates `$count` `WalletTransaction` rows:
     - `description`: random string from the array
     - `type`: random `debit` or `credit`
     - `value`: random float (rounded to 2 decimals)
     - sets the relation FK:
       - customer transactions:
         - `customer_id = $user->id`
         - `admin_id = null`
       - admin/manager/super_admin transactions:
         - `admin_id = $user->id`
         - `customer_id = null`
     - then calls `WalletTransaction::create($payload)`

If the DB schema does not have columns like `description`, `type`, `value`, this job will fail.

---

### 5.2 Job 2: UpdateRoleBalances
File:
- `app/Jobs/UpdateRoleBalances.php`

Constructor payload:
- `role` (customer/admin/manager/super_admin)

Execution (`handle()`):
1. Loads target users via `getUsers()` (same logic as Job 1).
2. For each user:
   - `resolveColumns()` chooses which table/foreign key to sum:
     - role `customer` → `['customer_id', 'customers']`
     - otherwise → `['admin_id', 'admins']`
   - sums credits and debits from `wallet_transactions`:
     - `totalCredit = sum(value) where type='credit'`
     - `totalDebit  = sum(value) where type='debit'`
   - calculates:
     - `balance = credits - debits`
   - updates stored balance on the appropriate table:
     - `DB::table($table)->where('id', $user->id)->update(['balance' => $balance])`

---

## 6. Models involved
### 6.1 `app/Models/Admin.php`
- Role constants:
  - `ROLE_MANAGER = 'manager'`
  - `ROLE_ADMIN = 'admin'`
  - `ROLE_SUPER_ADMIN = 'super_admin'`
- `roles()` provides a label map for UI dropdowns.

### 6.2 `app/Models/Customer.php`
- `customers` table model
- relationship `walletTransactions()` (used by the system elsewhere; Job calculations use direct queries via `WalletTransaction`)

### 6.3 `app/Models/WalletTransaction.php`
- `$fillable` includes:
  - `customer_id`, `admin_id`, `description`, `type`, `value`
- `value` casting:
  - `decimal:2`

---

## 7. Migrations (DB schema)
Wallet balances and transaction history rely on these migrations:

1. `database/migrations/2026_03_30_045538_add_balance_to_customers_and_admins_tables.php`
   - adds `balance` column to:
     - `customers`
     - `admins`
2. `database/migrations/2026_03_30_050117_create_wallet_transactions_table.php`
   - intended to create `wallet_transactions` with:
     - `customer_id`, `admin_id`
     - `description`, `type`, `value`
3. `database/migrations/2026_03_30_060500_fix_wallet_transactions_table.php`
   - ensures the existing `wallet_transactions` table actually contains all required columns
   - **required** so `GenerateMockWalletTransactions` can insert transactions successfully

---

## 8. WalletTransactionFactory (optional)
File:
- `database/factories/WalletTransactionFactory.php`

This factory can generate `WalletTransaction` rows for tests/seeders. The UI jobs currently create transactions directly via `WalletTransaction::create(...)`.

---

## 9. Queue configuration
Configured in `.env`:
- `QUEUE_CONNECTION=database`

Required operational command:
- Run a queue worker (`php artisan queue:work`) so jobs execute and balances update.

