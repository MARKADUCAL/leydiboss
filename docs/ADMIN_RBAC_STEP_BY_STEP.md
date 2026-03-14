# How the Admin Roles Actually Work (Plain Explanation + Code)

I’ll walk you through how the code decides what each admin can see and do, and show you the actual code so you can follow along.

---

## 1. Every admin has a role in the database

First thing: in the `admins` table there’s a `role` column. So when we load an admin, we know right away if they’re a manager, an admin, or a super_admin. That’s the only place we store “who is what.” When you create or edit an admin (as a super admin), you pick that role in the form, and we save it there.

**The code:** In the Admin model we have constants for the role values and we keep `role` in `$fillable` so it can be set when creating/updating:

```php
// app/Models/Admin.php

public const ROLE_MANAGER = 'manager';
public const ROLE_ADMIN = 'admin';
public const ROLE_SUPER_ADMIN = 'super_admin';

protected $fillable = [
    'name',
    'email',
    'phone_number',
    'password',
    'role',   // ← saved to DB when you create/edit an admin
];
```

---

## 2. When an admin logs in

They hit `/admin/login` and type email and password. We don’t use the normal “web” login for this; we use the **admin** guard. So Laravel checks the `admins` table, and if it’s correct, it puts that admin’s id in the session under the admin guard. Then we send them to the dashboard. So from that moment on, we know “this session is an admin” and which admin it is.

(That logic lives in your `Admin\Auth\LoginController` and the `config/auth.php` admin guard—no snippet here since we’re focusing on the RBAC pieces.)

---

## 3. When they open any admin page

Say they go to `/admin` or `/admin/customers`. Before anything else runs, our **RequireAdminAuth** middleware runs. It only does two things: “Is someone logged in with the admin guard?” If no, we redirect them to the login page. If yes, we do one extra important thing: we set the **default auth guard** to `admin` for the rest of that request. So everywhere in that request—controllers, views, Gates—when we say “the current user,” we mean the logged-in admin, not some other user. That’s why the sidebar and the Gates see the right person.

**The code:** `app/Http/Middleware/RequireAdminAuth.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // 1. Not logged in as admin? Send to login.
    if (!Auth::guard('admin')->check()) {
        return redirect()->guest(route('admin.login'));
    }

    // 2. From now on, "the current user" = this admin (for Gates, auth()->user(), etc.)
    Auth::setDefaultDriver('admin');

    return $next($request);
}
```

So `Auth::guard('admin')->check()` is “is there an admin in the session?” and `Auth::setDefaultDriver('admin')` makes sure that for this one request, any code that asks for “the user” gets the admin.

---

## 4. Then we check: is this role allowed to open this URL?

Some routes have an extra layer: **EnsureAdminRole**. So for example the customers routes say “only admin and super_admin,” and the admins routes say “only super_admin.” When you hit one of those URLs, the middleware looks at the current admin’s role. If their role is in the allowed list, the request continues. If not, we return 403 and they never see the page.

**The code:** `app/Http/Middleware/EnsureAdminRole.php`

```php
public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
{
    $admin = Auth::guard('admin')->user();

    if (!$admin) {
        return redirect()->guest(route('admin.login'));
    }

    // Is this admin's role in the list we got from the route (e.g. admin,super_admin)?
    if (!in_array($admin->role, $allowedRoles, true)) {
        abort(403, 'You do not have permission to access this area.');
    }

    return $next($request);
}
```

The `...$allowedRoles` part means the route can pass something like `admin.role:admin,super_admin` and Laravel turns that into an array `['admin', 'super_admin']`. We just check: is `$admin->role` in that array?

**Where we attach it:** `routes/web.php`. Customers are wrapped in one group, admins in another:

```php
// Customers: only admin and super_admin can hit these URLs
Route::middleware('admin.role:admin,super_admin')->group(function () {
    Route::get('/customers', ...);
    Route::post('/customers', ...);
    // ...
});

// Admins: only super_admin
Route::middleware('admin.role:super_admin')->group(function () {
    Route::get('/admins', ...);
    Route::post('/admins', ...);
    // ...
});
```

Dashboard and services routes don’t have this middleware, so any logged-in admin can open them.

---

## 5. The controller runs and gives you the page

If they passed the checks above, the normal controller runs—like the one for the dashboard or for customers. It loads whatever data it needs and returns the Blade view. So at this point we’re just rendering the page they’re allowed to see. Nothing special in the controller for RBAC; the middleware already made sure they’re allowed.

---

## 6. The sidebar: why you only see some menu items

The layout includes the sidebar. For “Dashboard” and “Services” we don’t do any special check—every logged-in admin always sees those two. For “Customers” and “Admins” we wrap the links in `@can('accessAdminCustomers')` and `@can('accessAdminAdmins')`. So when the view is rendered, Laravel runs those Gates. Because we set the default guard to admin back in step 3, the “current user” for the Gate is your logged-in admin. The Gate then just asks that admin: “Can you access the customers area?” or “Can you access the admins area?” That’s done by calling `$admin->canAccessArea('customers')` or `canAccessArea('admins')` on the Admin model. Inside the Admin model we have a simple map: for each area we have a list of roles that are allowed. The method just checks: “Is my role in the list for this area?” If yes, show the link; if no, don’t render it.

**The code (sidebar):** `resources/views/components/admin/sidebar.blade.php`

- Dashboard and Services are plain links (no `@can`).
- Customers and Admins are wrapped so they only show when the Gate returns true:

```blade
@can('accessAdminCustomers')
<a href="{{ route('admin.customers.index') }}" ...>Customers</a>
@endcan

@can('accessAdminAdmins')
<a href="{{ route('admin.admins.index') }}" ...>Admins</a>
@endcan
```

When Blade hits `@can('accessAdminCustomers')`, Laravel runs the Gate with the same name and uses the current user (the admin, because we set the default guard). If the Gate returns true, the block is rendered; otherwise it’s skipped.

**The code (Gates):** `app/Providers/AppServiceProvider.php`

We define one Gate per area. Each Gate gets the admin (from the user Laravel passes in, or from the admin guard) and calls `canAccessArea`:

```php
private function registerAdminGates(): void
{
    $areas = ['dashboard', 'services', 'customers', 'admins'];

    foreach ($areas as $area) {
        Gate::define('accessAdmin' . ucfirst($area), function (?Admin $user = null) use ($area) {
            $admin = $user instanceof Admin ? $user : Auth::guard('admin')->user();
            return $admin && $admin->canAccessArea($area);
        });
    }
}
```

So `accessAdminCustomers` becomes “get the admin, then return whether that admin can access the `customers` area.”

**The code (the actual rule):** `app/Models/Admin.php`

The “who can access what” map and the check live in the model:

```php
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

So for “customers” we only allow `admin` and `super_admin`; for “admins” we only allow `super_admin`. The Gates and the sidebar both rely on this one method.

---

## 7. Putting it all in one flow

So when you use the app: your role is in the database. You log in and we remember you on the admin guard. When you open a page, we first make sure you’re an admin and switch the default user to you (RequireAdminAuth). Then we check if your role is allowed to open that URL (EnsureAdminRole on some routes); if not, 403. If yes, the controller runs and returns the view. When the view draws the sidebar, it uses the Gates (`@can`), which call `$admin->canAccessArea($area)` and only show the links you’re allowed. So the same idea—role in the DB and the AREA_ROLES map—is what blocks wrong URLs and hides menu items you’re not supposed to see.

---

## 8. Where the “who can do what” rules actually live

All of that comes from one place in code: the **AREA_ROLES** array in the Admin model (and the `canAccessArea` method that uses it). The middleware in `web.php` is set up to match that (we only let those roles hit those URLs). The Gates just call `canAccessArea`. So if you ever want to change who can see “Customers” or “Admins,” you change that array and keep the route middleware in line with it. Everything else (sidebar, 403s) follows from there.

---

## Quick reference: what each role can do

- **Manager:** Can open dashboard and services; sidebar shows only Dashboard and Services. Customers and Admins URLs give 403, and those links don’t show.
- **Admin:** Can open dashboard, services, and customers; sidebar shows Dashboard, Services, and Customers. Admins URL gives 403, and the Admins link doesn’t show.
- **Super Admin:** Can open everything; sidebar shows all four (Dashboard, Services, Customers, Admins).

That’s how the code actually works end to end, with the real code that does it.
