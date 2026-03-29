# Admin Roles, Permissions, Providers & Gates

This document explains how the admin permission system works through three core concepts:

- **Permissions:** Who can access what (defined in the Admin model)
- **Providers:** Where authorization rules are registered (service providers)
- **Gates:** How rules are checked in views (Laravel authorization directives)

---

## 1. Permissions: The Foundation (Role-Based Access Control)

### Admin Roles

Every admin has a single **role** stored in the `admins` table. There are three roles:

- **Manager:** Can access Dashboard and Services only
- **Admin:** Can access Dashboard, Services, and Customers
- **Super Admin:** Can access everything (Dashboard, Services, Customers, Admins)

**The code:** `app/Models/Admin.php`

```php
public const ROLE_MANAGER = 'manager';
public const ROLE_ADMIN = 'admin';
public const ROLE_SUPER_ADMIN = 'super_admin';

protected $fillable = [
    'name',
    'email',
    'phone_number',
    'password',
    'role',   // ← saved to DB
];
```

### The Permission Map: AREA_ROLES

The **single source of truth** for all permissions is the `AREA_ROLES` array in the Admin model. It defines which roles can access which areas:

```php
// app/Models/Admin.php

/** Roles that can access each area. */
private const AREA_ROLES = [
    'dashboard' => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'services'  => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'customers' => [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'admins'    => [self::ROLE_SUPER_ADMIN],
];

public function canAccessArea(string $area): bool
{
    $allowed = self::AREA_ROLES[$area] ?? [];
    return in_array($this->role, $allowed, true);
}
```

**That's it.** One method. One map. Everything else (Gates, middleware, sidebar) uses this.

---

## 2. Providers: Where Gates Are Registered

A **Service Provider** is where Laravel sets up application-level services when it boots. In our case, `AppServiceProvider` is where we **register all the Gates** that enforce permissions.

### What's a Gate?

A Gate is a simple authorization check function. You define a Gate by giving it a name and a callback function that returns true or false. When code uses `@can('gateName')` in a view or `Gate::allows('gateName')` in PHP, Laravel runs that callback to decide whether to allow access.

### Registering Gates

**The code:** `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    $this->registerAdminGates();
}

private function registerAdminGates(): void
{
    // Define one Gate per area
    $areas = ['dashboard', 'services', 'customers', 'admins'];

    foreach ($areas as $area) {
        Gate::define('accessAdmin' . ucfirst($area), function (?Admin $user = null) use ($area) {
            // Get the admin (either passed in or from the admin guard)
            $admin = $user instanceof Admin ? $user : Auth::guard('admin')->user();

            // Use the permission check from the Admin model
            return $admin && $admin->canAccessArea($area);
        });
    }
}
```

### How Gates Connect to Permissions

When you define `Gate::define('accessAdminCustomers', ...)`, you're creating a Gate that:

1. Receives the current user (the admin)
2. Calls `$admin->canAccessArea('customers')`
3. That method checks if the admin's role is in `AREA_ROLES['customers']`
4. Returns true or false

**Key point:** The Gate **does not define the rules**. It just **runs the permission check** from the Admin model. The actual rules live in `AREA_ROLES`.

---

## 3. Gates in Action: Using Permissions in Views

Once Gates are registered in the Provider, you use them in Blade views with the `@can` directive. When Blade encounters `@can('accessAdminCustomers')`, it runs the Gate and only renders the block if it returns true.

### Sidebar Example

**The code:** `resources/views/components/admin/sidebar.blade.php`

```blade
<!-- Always visible (no @can check needed) -->
<a href="{{ route('admin.dashboard') }}" ...>Dashboard</a>
<a href="{{ route('admin.services.index') }}" ...>Services</a>

<!-- Only visible if the Gate returns true -->
@can('accessAdminCustomers')
<a href="{{ route('admin.customers.index') }}" ...>Customers</a>
@endcan

@can('accessAdminAdmins')
<a href="{{ route('admin.admins.index') }}" ...>Admins</a>
@endcan
```

### Flow When Rendering the Sidebar

1. Blade hits `@can('accessAdminCustomers')`
2. Laravel looks up the Gate named `accessAdminCustomers` (registered in the Provider)
3. Laravel runs the Gate's callback with the current user (the logged-in admin)
4. The callback calls `$admin->canAccessArea('customers')`
5. That method checks: "Is my role in `AREA_ROLES['customers']`?"
6. If yes, render the link; if no, skip it

**Result:** A manager doesn't see the "Customers" link; an admin does see it.

### Key Point: The Default Auth Guard

The current user for the Gate is determined by the **default auth guard**. In the admin system, the `RequireAdminAuth` middleware sets the default to `admin`, so the user in the Gate is always the logged-in admin (not a regular customer).

---

## 4. Route Middleware: Protecting URLs with Permissions

The second layer of protection is at the **route level**. Routes can apply the `EnsureAdminRole` middleware to check permissions **before the controller even runs**.

### The Middleware

**The code:** `app/Http/Middleware/EnsureAdminRole.php`

```php
public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
{
    $admin = Auth::guard('admin')->user();

    if (!$admin) {
        return redirect()->guest(route('admin.login'));
    }

    // Is this admin's role in the allowed list?
    if (!in_array($admin->role, $allowedRoles, true)) {
        abort(403, 'You do not have permission to access this area.');
    }

    return $next($request);
}
```

### Applying It to Routes

**The code:** `routes/web.php`

```php
// Customers: only admin and super_admin can access these URLs
Route::middleware('admin.role:admin,super_admin')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    // ... more routes
});

// Admins: only super_admin
Route::middleware('admin.role:super_admin')->group(function () {
    Route::get('/admins', [AdminController::class, 'index']);
    Route::post('/admins', [AdminController::class, 'store']);
    // ... more routes
});

// Dashboard and services (no role middleware, accessible to any logged-in admin)
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
```

### How It Works

- Route middleware checks the admin's role before the controller runs
- If the role is in the allowed list, the request continues
- If not, a 403 error is returned immediately
- This uses the same permission rules (through `AREA_ROLES`) as the sidebar

---

## 5. The Admin Guard: Session Management

When an admin logs in, Laravel authenticates them using the **admin guard** (separate from the regular "web" guard used for customers). This guard stores the admin's ID in the session.

### Setting the Default Guard

Before any admin page is accessed, the `RequireAdminAuth` middleware ensures the admin is logged in and sets the default auth guard:

**The code:** `app/Http/Middleware/RequireAdminAuth.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // Is there an admin logged in with the admin guard?
    if (!Auth::guard('admin')->check()) {
        return redirect()->guest(route('admin.login'));
    }

    // Set the default guard to 'admin' for this request
    // This makes auth()->user() and Gates refer to the admin, not a regular user
    Auth::setDefaultDriver('admin');

    return $next($request);
}
```

**Why this matters:**

- Gates rely on `auth()->user()` to get the current user
- Without setting the default guard, Gates would get a regular customer, not the admin
- With the default set to `admin`, all authorization checks use the logged-in admin

---

## 6. How It All Flows Together

Here's the complete flow when an admin visits the app:

1. **Admin logs in**
    - Credentials are checked against the `admins` table
    - Admin ID is stored in the admin guard session

2. **Admin visits `/admin/customers`**
    - `RequireAdminAuth` middleware verifies they're logged in with the admin guard
    - Default auth guard is set to `admin` for this request

3. **Route middleware checks role**
    - `EnsureAdminRole` middleware checks if their role is in the allowed list (using `AREA_ROLES`)
    - If not allowed, 403 is returned

4. **Controller runs**
    - If role check passed, the controller loads data and returns a view

5. **Sidebar renders with Gates**
    - Sidebar uses `@can('accessAdminCustomers')` and `@can('accessAdminAdmins')`
    - Each `@can` directive triggers a Gate

6. **Gates check permissions**
    - Each Gate calls `$admin->canAccessArea($area)`
    - That reads from `AREA_ROLES` to check if the role is allowed

7. **Menu items appear or disappear**
    - Only links the admin can access are rendered
    - Other links are skipped

### The Single Source of Truth

The **entire permission system** is controlled by one thing: the `AREA_ROLES` constant in the Admin model. That one map controls:

- Who can access which URLs (via route middleware)
- Which sidebar items are visible (via Gates)
- All permission checks throughout the application

---

## 7. Permission Decision Tree

When an admin tries to access something, here's what gets checked:

```
Admin visits /customers
     ↓
RequireAdminAuth middleware: Is admin logged in?
     ↓ (yes)
EnsureAdminRole middleware: Is role in allowed list?
     ↓ (yes)
Controller runs
     ↓
View renders
     ↓
@can('accessAdminCustomers'): Is role in AREA_ROLES['customers']?
     ↓ (yes)
Render link
```

Every check uses the same source: `AREA_ROLES['customers'] = [admin, super_admin]`

---

## 8. Changing Permissions

To change what roles can access:

**Step 1:** Update `AREA_ROLES` in `app/Models/Admin.php`

```php
private const AREA_ROLES = [
    'dashboard' => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'services'  => [self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'customers' => [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN],
    'admins'    => [self::ROLE_SUPER_ADMIN],
];
```

**Step 2:** Update route middleware in `routes/web.php` to match

```php
Route::middleware('admin.role:admin,super_admin')->group(function () {
    // Routes for customers
});
```

**Step 3:** Done!

- Gates will automatically use the new permissions
- Sidebar will automatically hide/show links
- Middleware will automatically block/allow URLs

---

## Quick Reference Table

| Component           | Location                                  | Purpose             | Uses                    |
| ------------------- | ----------------------------------------- | ------------------- | ----------------------- |
| **AREA_ROLES**      | `app/Models/Admin.php`                    | Defines rules       | Single source of truth  |
| **canAccessArea()** | `app/Models/Admin.php`                    | Checks rules        | Gates, middleware       |
| **Gates**           | `app/Providers/AppServiceProvider.php`    | Registers checks    | Views, Blade directives |
| **EnsureAdminRole** | `app/Http/Middleware/EnsureAdminRole.php` | Blocks URLs         | Routes                  |
| **@can**            | Blade views                               | Shows/hides content | Sidebar links           |

---

## Role Permissions Summary

| Role            | Dashboard | Services | Customers | Admins | Sidebar Shows                          |
| --------------- | --------- | -------- | --------- | ------ | -------------------------------------- |
| **Manager**     | ✓         | ✓        | ✗         | ✗      | Dashboard, Services                    |
| **Admin**       | ✓         | ✓        | ✓         | ✗      | Dashboard, Services, Customers         |
| **Super Admin** | ✓         | ✓        | ✓         | ✓      | Dashboard, Services, Customers, Admins |

(✓ = can access, ✗ = blocked with 403 error)
