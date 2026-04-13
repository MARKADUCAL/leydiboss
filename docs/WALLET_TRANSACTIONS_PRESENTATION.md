# Wallet Transactions — How It Works (Presentation Doc)

This is the “talk track” for the Wallet Transactions admin screen:
it uses queued jobs to create transaction history and then recalculate balances from that history.

---

## What problem this solves
Instead of manually computing balances every time, the system:
1. Generates (or would generate in real life) debit/credit transactions
2. Recomputes each user’s balance from the transaction table

The UI dispatches work to the queue so the admin request stays fast.

---

## Demo flow (step-by-step)

### 1. Open the page
Go to:
- `Admin → Wallet Transactions`

This page contains **two job buttons**:
`Generate Transactions` and `Update Balances`.

### 2. Dispatch “Generate Transactions”
In the left card:
1. Choose a `Target Role`:
   - `CUSTOMER`, `ADMIN`, `MANAGER`, or `SUPER ADMIN`
2. Enter `Max Transactions per User`
3. Click `Dispatch Job`

What happens next:
- A queue job is added (does not run instantly in the browser)
- Your queue worker (`php artisan queue:work`) will run it
- That job inserts random rows into `wallet_transactions`
  - customers: uses `customer_id`
  - admins/manager/super_admin: uses `admin_id`

### 3. Dispatch “Update Balances”
In the right card:
1. Choose the same (or different) `Target Role`
2. Click `Dispatch Job`

What happens next:
- Another queued job is added
- It recalculates:
  - `balance = Σ credits − Σ debits`
- It writes the computed balance to:
  - `customers.balance` for `customer`
  - `admins.balance` for all other admin roles

### 4. Verify results
After jobs complete:
- you should see balances change in the database
- and/or whatever admin pages display balances.

---

## Key idea to explain
The buttons do NOT directly update balances on click.
They **dispatch jobs**.
Only the queue worker updates the database.

---

## What code to point at (speaker notes)

When you say “the left button creates transactions”, show:
- `app/Jobs/GenerateMockWalletTransactions.php`

When you say “the right button recalculates balances”, show:
- `app/Jobs/UpdateRoleBalances.php`

When you say “the UI dispatches jobs”, show:
- `app/Http/Controllers/Admin/CreateUserWalletTransactionsController.php`

When you say “this is queued”, mention:
- `.env`: `QUEUE_CONNECTION=database`
- command: `php artisan queue:work`

---

## Troubleshooting (what to say if it doesn’t update)
1. Check the worker is running:
   - `php artisan queue:work`
2. If transactions aren’t being created, check `storage/logs/laravel.log`
3. If balances don’t change, confirm:
   - `wallet_transactions` contains rows for the selected role
   - and has columns the job needs (`description`, `type`, `value`, `customer_id`/`admin_id`)

---

## Suggested “closing line”
“Wallet Transactions is a job-driven demo tool:
it generates transaction history in the background and then recomputes balances from that source of truth.”

