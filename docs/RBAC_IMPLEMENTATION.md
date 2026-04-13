# Role-Based Access Control (RBAC) Implementation

## Overview

The system implements three-tier role-based access control via the **RoleBasedAccess trait** in API controllers:

### Role Hierarchy

```
Manager (Level 1)
  ├─ Can manage services (vehicle types, packages, pricing)
  └─ CANNOT access customer or admin data

Admin (Level 2)
  ├─ Can manage services
  ├─ Can access customer data
  └─ CANNOT manage other admins

Super Admin (Level 3)
  ├─ Full access to everything
  ├─ Can manage services
  ├─ Can access customer data
  └─ Can manage (CRUD) other admins
```

---

## Implementation Architecture

### 1. RoleBasedAccess Trait

The trait (`app/Traits/RoleBasedAccess.php`) provides reusable methods for all controllers:

```php
use RoleBasedAccess;

// Check single role
$this->hasRole('manager')              // true/false

// Check multiple roles
$this->hasAnyRole(['admin', 'manager']) // true/false

// Quick checks
$this->isSuperAdmin()                  // true/false
$this->isAdminOrHigher()               // admin or super_admin

// Consistent error response (403)
$this->forbiddenResponse('Message', 'required_role')
```

### 2. Middleware Stack

```
Request
  ↓
[auth:sanctum] → Validate token, authenticate user
  ↓
[admin.api] → Check if user is Admin instance
  ↓
[Controller] → Role-based permission checks via trait
```

### 3. Controllers Using the Trait

- `AdminAuthController` - Admin management operations
- `CustomerAuthController` - Customer data access controls
- Other controllers can import and use the trait

---

## Access Control Rules

### Customer Data Endpoints

| Endpoint            | Manager | Admin  | Super Admin |
| ------------------- | ------- | ------ | ----------- |
| GET /customers      | ❌ 403  | ✅ 200 | ✅ 200      |
| GET /customers/{id} | ❌ 403  | ✅ 200 | ✅ 200      |

### Admin Management Endpoints

| Endpoint            | Manager | Admin  | Super Admin |
| ------------------- | ------- | ------ | ----------- |
| GET /admins         | ❌ 403  | ❌ 403 | ✅ 200      |
| GET /admins/{id}    | ❌ 403  | ❌ 403 | ✅ 200      |
| PUT /admins/{id}    | ❌ 403  | ❌ 403 | ✅ 200      |
| DELETE /admins/{id} | ❌ 403  | ❌ 403 | ✅ 200      |

### Service Endpoints (All admin roles)

| Endpoint                   | Manager | Admin  | Super Admin |
| -------------------------- | ------- | ------ | ----------- |
| GET /vehicle-types         | ✅ 200  | ✅ 200 | ✅ 200      |
| POST /vehicle-types        | ✅ 201  | ✅ 201 | ✅ 201      |
| PUT /vehicle-types/{id}    | ✅ 200  | ✅ 200 | ✅ 200      |
| DELETE /vehicle-types/{id} | ✅ 200  | ✅ 200 | ✅ 200      |
| GET /service-packages      | ✅ 200  | ✅ 200 | ✅ 200      |
| POST /service-packages     | ✅ 201  | ✅ 201 | ✅ 201      |
| GET /pricing-entries       | ✅ 200  | ✅ 200 | ✅ 200      |
| POST /pricing-entries      | ✅ 201  | ✅ 201 | ✅ 201      |

---

## Testing Scenarios in Postman

### Setup: Create Test Accounts

#### 1. Register Manager Account

**POST** `http://localhost:8000/api/admin/register`

```json
{
    "name": "Manager",
    "email": "manager@example.com",
    "phone_number": "1111111111",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "manager"
}
```

**Save token as:** `manager_token`

---

#### 2. Register Admin Account

**POST** `http://localhost:8000/api/admin/register`

```json
{
    "name": "Admin",
    "email": "admin@example.com",
    "phone_number": "2222222222",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}
```

**Save token as:** `admin_token`

---

#### 3. Register Super Admin Account

**POST** `http://localhost:8000/api/admin/register`

```json
{
    "name": "Super Admin",
    "email": "superadmin@example.com",
    "phone_number": "3333333333",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "super_admin"
}
```

**Save token as:** `super_admin_token`

---

### Test Case 1: Manager Tries to Access Customer Data

**GET** `http://localhost:8000/api/customers`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Expected Response (403):**

```json
{
    "message": "Forbidden - Managers cannot access customer data",
    "required_role": "admin or super_admin",
    "your_role": "manager"
}
```

---

### Test Case 2: Admin Can Access Customer Data

**GET** `http://localhost:8000/api/customers`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Expected Response (200):**

```json
{
    "message": "Customers retrieved successfully",
    "data": [],
    "count": 0
}
```

---

### Test Case 3: Manager Tries to Access Admin Management

**GET** `http://localhost:8000/api/admins`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Expected Response (403):**

```json
{
    "message": "Forbidden - Only super admins can view all admins",
    "your_role": "manager"
}
```

---

### Test Case 4: Admin Tries to Access Admin Management

**GET** `http://localhost:8000/api/admins`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Expected Response (403):**

```json
{
    "message": "Forbidden - Only super admins can view all admins",
    "your_role": "admin"
}
```

---

### Test Case 5: Super Admin Can Access Admin Management

**GET** `http://localhost:8000/api/admins`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Expected Response (200):**

```json
{
    "message": "Admins retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Manager",
            "email": "manager@example.com",
            "phone_number": "1111111111",
            "role": "manager",
            "balance": 0
        },
        {
            "id": 2,
            "name": "Admin",
            "email": "admin@example.com",
            "phone_number": "2222222222",
            "role": "admin",
            "balance": 0
        },
        {
            "id": 3,
            "name": "Super Admin",
            "email": "superadmin@example.com",
            "phone_number": "3333333333",
            "role": "super_admin",
            "balance": 0
        }
    ],
    "count": 3
}
```

---

### Test Case 6: All Roles Can Manage Services

**POST** `http://localhost:8000/api/vehicle-types`

**Headers (Manager):**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Body:**

```json
{
    "name": "Sedan",
    "description": "Standard sedan",
    "is_active": true
}
```

**Expected Response (201):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Sedan",
        "description": "Standard sedan",
        "is_active": true
    },
    "message": "Vehicle type created successfully"
}
```

✅ Same success for Admin and Super Admin tokens

---

### Test Case 7: Super Admin Updates Another Admin's Profile

**PUT** `http://localhost:8000/api/admins/2`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Body:**

```json
{
    "name": "Updated Admin",
    "role": "admin"
}
```

**Expected Response (200):**

```json
{
    "message": "Admin profile updated successfully",
    "data": {
        "id": 2,
        "name": "Updated Admin",
        "email": "admin@example.com",
        "phone_number": "2222222222",
        "role": "admin",
        "balance": 0
    }
}
```

---

### Test Case 8: Admin Tries to Update Another Admin's Profile

**PUT** `http://localhost:8000/api/admins/2`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Expected Response (403):**

```json
{
    "message": "Forbidden - Only super admins can update other admins",
    "your_role": "admin"
}
```

---

## Error Responses

### 403 Forbidden - Insufficient Permissions

```json
{
    "message": "Forbidden - Managers cannot access customer data",
    "required_role": "admin or super_admin",
    "your_role": "manager"
}
```

### 401 Unauthorized - Missing Token

```json
{
    "message": "Unauthorized - Admin access required"
}
```

### 404 Not Found

```json
{
    "message": "Customer not found"
}
```

---

## Implementation Notes

### Architecture

- **Trait-Based Approach:** `RoleBasedAccess` trait provides reusable role checking methods
- **Permission Checks:** Performed at the **controller method level**
- **Middleware Stack:** Token validation → Admin instance check → Role authorization
- **Error Responses:** All non-permitted requests return **403 Forbidden** with role information

### How the Trait Works

**Before** (Repetitive Code):

```php
if ($admin->role !== 'super_admin') {
    return response()->json(['message' => 'Forbidden...'], 403);
}
```

**After** (Using Trait):

```php
if (!$this->isSuperAdmin()) {
    return $this->forbiddenResponse('Forbidden message', 'super_admin');
}
```

**Benefits:**

- ✅ Reduces code duplication across controllers
- ✅ Consistent 403 response format
- ✅ Single source of truth for authorization logic
- ✅ Easier to maintain and update permissions

### Controllers Implementing the Trait

1. **AdminAuthController** (`app/Http/Controllers/Api/AdminAuthController.php`)
    - Uses `$this->isSuperAdmin()` for admin management operations
    - Uses `$this->forbiddenResponse()` for consistent error messages

2. **CustomerAuthController** (`app/Http/Controllers/Api/CustomerAuthController.php`)
    - Checks `$admin->role === 'manager'` to restrict customer data access
    - Uses `$this->forbiddenResponse()` for role-based blocking

### Key Trait Methods Used

```php
// In AdminAuthController - only super admins
if (!$this->isSuperAdmin()) {
    return $this->forbiddenResponse('Only super admins...', 'super_admin');
}

// In CustomerAuthController - block managers
if ($admin->role === 'manager') {
    return $this->forbiddenResponse('Managers cannot access...', ['admin', 'super_admin']);
}
```

### Role Inheritance Notes

- **Super Admin** maintains backward compatibility with all operations
- **Manager** role is strictly scoped to service management
- **Admin** role has access to both services and customer data
- Token validation happens **before** role validation

### Related Documentation

For web-based RBAC (Gates, Providers, Middleware routes), see:

- [ADMIN_RBAC_STEP_BY_STEP.md](./ADMIN_RBAC_STEP_BY_STEP.md) - Complete web RBAC system with Gates and Service Providers
