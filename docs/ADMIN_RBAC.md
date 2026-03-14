# Admin RBAC (Role-Based Access Control) — Explanation

This document explains how admin roles and permissions work in the Leydi Boss project.

---

## 1. Roles

There are **three admin roles**:

| Role         | Can access |
|-------------|------------|
| **Manager** | Dashboard, Services |
| **Admin**   | Dashboard, Services, **Customer Management** |
| **Super Admin** | **All**: Dashboard, Services, Customer Management, **Admin Management** |

Each admin has a `role` stored in the `admins` table (`manager`, `admin`, or `super_admin`).

---

## 2. How It Works (Overview)

Access is enforced in **three places**:

1. **Routes (middleware)** — Stops unauthorized users from opening a URL (e.g. `/admin/customers`, `/admin/admins`).
2. **Gates** — Used in Blade (e.g. sidebar) to **show or hide** menu items by role.
3. **Policy** — Used in code to decide if an admin can **view / create / update / delete** another admin.

The **Admin model** defines who can access which area via `canAccessArea('dashboard'|'services'|'customers'|'admins')`. Gates and middleware both rely on this.

---

## 3. Key Files and What They Do

### 3.1 Database: `role` on admins

- **Migration:** `database/migrations/2026_03_14_000000_add_role_to_admins_table.php`  
  Adds a `role` column (default `manager`).

### 3.2 Model: `App\Models\Admin`

- **Constants:** `ROLE_MANAGER`, `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`
- **Method:** `canAccessArea($area)`  
  Returns `true` only if the admin’s role is allowed for that area (e.g. only `admin` and `super_admin` can access `customers`).
- **Helpers:** `isManager()`, `isAdmin()`, `isSuperAdmin()`
- **Static:** `Admin::roles()`  
  Returns labels for dropdowns: `['manager' => 'Manager', 'admin' => 'Admin', 'super_admin' => 'Super Admin']`

This is the **single source of truth** for “who can access what.”

### 3.3 Middleware

**`RequireAdminAuth`** (`app/Http/Middleware/RequireAdminAuth.php`)

- Ensures the user is logged in with the **admin** guard.
- Sets the **default auth guard to `admin`** for the request so that `auth()->user()` and Gates see the logged-in admin (fixes sidebar/permission checks).

**`EnsureAdminRole`** (`app/Http/Middleware/EnsureAdminRole.php`)

- Used on routes as: `admin.role:admin,super_admin` or `admin.role:super_admin`.
- Checks that the current admin’s role is in the list; if not, returns **403 Forbidden**.

### 3.4 Routes (`routes/web.php`)

- **Dashboard** — All authenticated admins (no role middleware).
- **Services** — All authenticated admins (no role middleware).
- **Customers** — Wrapped in `admin.role:admin,super_admin` (Manager cannot access).
- **Admins (CRUD)** — Wrapped in `admin.role:super_admin` (only Super Admin).

So: **routes** enforce “can this role open this URL?”

### 3.5 Gates (`app/Providers/AppServiceProvider.php`)

Gates are registered in `registerAdminGates()`:

- `accessAdminDashboard`
- `accessAdminServices`
- `accessAdminCustomers`
- `accessAdminAdmins`

Each gate:

1. Gets the current admin (from the user Laravel passes in, or from `Auth::guard('admin')->user()`).
2. Returns `$admin && $admin->canAccessArea($area)`.

Used in Blade to **show/hide** menu items, e.g.:

```blade
@can('accessAdminCustomers')
    <a href="{{ route('admin.customers.index') }}">Customers</a>
@endcan
@can('accessAdminAdmins')
    <a href="{{ route('admin.admins.index') }}">Admins</a>
@endcan
```

So: **Gates** answer “should we show this link to this admin?”

### 3.6 Policy: `App\Policies\AdminPolicy`

- Used for **model-level** authorization on `Admin` (view list, view one, create, update, delete).
- Every method checks `$admin->canAccessArea('admins')` (i.e. only **Super Admin** can do these).

Example in a controller (when you want to authorize explicitly):

```php
$this->authorizeForUser(Auth::guard('admin')->user(), 'update', $admin);
```

Laravel discovers this policy by convention (`Admin` model → `AdminPolicy`).

### 3.7 Sidebar (`resources/views/components/admin/sidebar.blade.php`)

- **Dashboard** and **Services** — Shown to all logged-in admins.
- **Customers** — Wrapped in `@can('accessAdminCustomers')`.
- **Admins** — Wrapped in `@can('accessAdminAdmins')`.

So the sidebar reflects the same rules as the Gates (and thus the Admin model).

---

## 4. Flow Summary

1. Admin logs in → session stores admin guard.
2. On each admin request, **RequireAdminAuth** runs → sets default guard to `admin` so `auth()->user()` is the admin.
3. **EnsureAdminRole** runs on protected routes → allows or blocks the request by role.
4. In the view, **@can('accessAdmin...')** uses the Gates → Gates use `canAccessArea()` on the current admin → sidebar shows only allowed links.
5. When creating/editing admins, **AdminsController** uses the role from the form; only Super Admin can open that page (middleware) and only Super Admin can set/change roles (controller logic).

---

## 5. Seeded Accounts (after `php artisan db:seed --class=AdminSeeder`)

| Email                   | Password  | Role         |
|-------------------------|-----------|-------------|
| superadmin@example.com  | password  | Super Admin |
| admin@example.com       | password  | Admin       |
| manager@example.com     | password  | Manager     |

You can use these to test each role’s access (sidebar and URLs).

---

## 6. Quick Reference

- **Add a new area:**  
  Add the area to `Admin::AREA_ROLES`, create a gate `accessAdmin<Area>`, add sidebar link inside `@can('accessAdmin<Area>')`, and protect the route with `admin.role:...` if needed.
- **Change who can access what:**  
  Edit the arrays in `Admin::AREA_ROLES` in `App\Models\Admin`.
- **Why Super Admin saw only Dashboard/Services before:**  
  Default guard was `web`, so Gates saw no user; we fixed it by setting the default guard to `admin` in **RequireAdminAuth** for admin requests.
