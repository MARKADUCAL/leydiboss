# Complete Postman Testing Guide

## Base URL

```
http://localhost:8000/api
```

## Table of Contents

1. [Authentication & Auth Setup](#authentication--auth-setup)
2. [Customer Authentication](#customer-authentication)
3. [Admin Authentication](#admin-authentication)
4. [Customer Profile Management](#customer-profile-management)
5. [Admin Profile Management](#admin-profile-management)
6. [Admin Management (Super Admin Only)](#admin-management-super-admin-only)
7. [Customer Data Access (Admin+)](#customer-data-access-admin)
8. [Vehicle Management (Customer)](#vehicle-management-customer)
9. [Vehicle Types (Admin)](#vehicle-types-admin)
10. [Service Packages (Admin)](#service-packages-admin)
11. [Pricing Entries (Admin)](#pricing-entries-admin)
12. [Role-Based Access Control Tests](#role-based-access-control-tests)

---

# Authentication & Auth Setup

## Create Test Admin Accounts

### Test Case 1: Register Manager Account

**Endpoint:** `POST /admin/register`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
    "name": "Manager Account",
    "email": "manager@example.com",
    "phone_number": "1111111111",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "manager"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Admin registered successfully",
    "data": {
        "id": 1,
        "name": "Manager Account",
        "email": "manager@example.com",
        "phone_number": "1111111111",
        "role": "manager",
        "balance": 0,
        "created_at": "2026-04-13T10:00:00.000000Z"
    },
    "access_token": "TOKEN_HERE"
}
```

**Save as Postman variable:** `{{manager_token}}`

---

### Test Case 2: Register Admin Account

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Admin Account",
    "email": "admin@example.com",
    "phone_number": "2222222222",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Admin registered successfully",
    "data": {
        "id": 2,
        "name": "Admin Account",
        "email": "admin@example.com",
        "phone_number": "2222222222",
        "role": "admin",
        "balance": 0,
        "created_at": "2026-04-13T10:01:00.000000Z"
    },
    "access_token": "TOKEN_HERE"
}
```

**Save as Postman variable:** `{{admin_token}}`

---

### Test Case 3: Register Super Admin Account

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Super Admin Account",
    "email": "superadmin@example.com",
    "phone_number": "3333333333",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "super_admin"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Admin registered successfully",
    "data": {
        "id": 3,
        "name": "Super Admin Account",
        "email": "superadmin@example.com",
        "phone_number": "3333333333",
        "role": "super_admin",
        "balance": 0,
        "created_at": "2026-04-13T10:02:00.000000Z"
    },
    "access_token": "TOKEN_HERE"
}
```

**Save as Postman variable:** `{{super_admin_token}}`

---

### Test Case 4: Register Admin with Invalid Role (Should default to manager)

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Invalid Role Admin",
    "email": "invalid@example.com",
    "phone_number": "4444444444",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "invalid_role"
}
```

**Note:** Should either be rejected or default to "manager" - depends on validation

---

### Test Case 5: Register with Duplicate Email

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Duplicate Email",
    "email": "manager@example.com",
    "phone_number": "5555555555",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Error Response (422):**

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

### Test Case 6: Register with Invalid Email

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Invalid Email",
    "email": "not-an-email",
    "phone_number": "5555555555",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Error Response (422):**

```json
{
    "message": "The email field must be a valid email address.",
    "errors": {
        "email": ["The email field must be a valid email address."]
    }
}
```

---

### Test Case 7: Register with Password Mismatch

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Password Mismatch",
    "email": "mismatch@example.com",
    "phone_number": "6666666666",
    "password": "password123",
    "password_confirmation": "different123"
}
```

**Error Response (422):**

```json
{
    "message": "The password confirmation does not match.",
    "errors": {
        "password": ["The password confirmation does not match."]
    }
}
```

---

### Test Case 8: Register with Short Password

**Endpoint:** `POST /admin/register`

**Request Body:**

```json
{
    "name": "Short Password",
    "email": "short@example.com",
    "phone_number": "7777777777",
    "password": "short",
    "password_confirmation": "short"
}
```

**Error Response (422):**

```json
{
    "message": "The password must be at least 8 characters.",
    "errors": {
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

# Customer Authentication

## Test Case 9: Register Customer

**Endpoint:** `POST /customer/register`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
    "name": "John Customer",
    "email": "customer@example.com",
    "phone_number": "9999999999",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Customer registered successfully",
    "data": {
        "id": 1,
        "name": "John Customer",
        "email": "customer@example.com",
        "phone_number": "9999999999",
        "created_at": "2026-04-13T10:00:00.000000Z"
    },
    "access_token": "TOKEN_HERE"
}
```

**Save as Postman variable:** `{{customer_token}}`

---

# Admin Authentication

## Test Case 10: Admin Login

**Endpoint:** `POST /admin/login`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
    "email": "superadmin@example.com",
    "password": "password123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Admin logged in successfully",
    "data": {
        "id": 3,
        "name": "Super Admin Account",
        "email": "superadmin@example.com",
        "phone_number": "3333333333",
        "role": "super_admin",
        "balance": 0
    },
    "access_token": "TOKEN_HERE"
}
```

---

## Test Case 11: Admin Login - Wrong Password

**Endpoint:** `POST /admin/login`

**Request Body:**

```json
{
    "email": "superadmin@example.com",
    "password": "wrongpassword"
}
```

**Error Response (401):**

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

---

## Test Case 12: Admin Login - User Not Found

**Endpoint:** `POST /admin/login`

**Request Body:**

```json
{
    "email": "notfound@example.com",
    "password": "password123"
}
```

**Error Response (401):**

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

---

## Test Case 13: Admin Logout

**Endpoint:** `POST /admin/logout`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Request Body:** (empty)

**Success Response (200):**

```json
{
    "success": true,
    "message": "Admin logged out successfully"
}
```

---

## Test Case 14: Admin Logout - Without Token

**Endpoint:** `POST /admin/logout`

**Headers:** (no Authorization header)

**Error Response (401):**

```json
{
    "message": "Unauthenticated."
}
```

---

# Customer Profile Management

## Test Case 15: Get Own Customer Profile

**Endpoint:** `GET /customer/profile`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Customer",
        "email": "customer@example.com",
        "phone_number": "9999999999",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 16: Update Customer Profile

**Endpoint:** `PUT /customer/profile`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "John Updated",
    "email": "customer_updated@example.com",
    "phone_number": "9999999998"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "customer_updated@example.com",
        "phone_number": "9999999998",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 17: Update Customer Profile - Duplicate Email

**Endpoint:** `PUT /customer/profile`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "email": "superadmin@example.com"
}
```

**Error Response (422):**

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

## Test Case 18: Delete Customer Profile

**Endpoint:** `DELETE /customer/profile`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Profile deleted successfully"
}
```

---

## Test Case 19: Customer Logout

**Endpoint:** `POST /customer/logout`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Customer logged out successfully"
}
```

---

# Admin Profile Management

## Test Case 20: Get Own Admin Profile

**Endpoint:** `GET /admin/profile`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Admin Account",
        "email": "admin@example.com",
        "phone_number": "2222222222",
        "role": "admin",
        "balance": 0,
        "created_at": "2026-04-13T10:01:00.000000Z"
    }
}
```

---

## Test Case 21: Update Own Admin Profile (Name & Email)

**Endpoint:** `PUT /admin/profile`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Updated Admin",
    "email": "admin_updated@example.com",
    "phone_number": "2222222221"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 2,
        "name": "Updated Admin",
        "email": "admin_updated@example.com",
        "phone_number": "2222222221",
        "role": "admin",
        "balance": 0
    }
}
```

---

## Test Case 22: Update Own Profile - Manager Tries to Update Role

**Endpoint:** `PUT /admin/profile`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "role": "admin"
}
```

**Error Response (403):**

```json
{
    "message": "Only super admins can update role",
    "required_role": "super_admin",
    "your_role": "manager"
}
```

---

## Test Case 23: Update Own Profile - Admin Tries to Update Role

**Endpoint:** `PUT /admin/profile`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "role": "super_admin"
}
```

**Error Response (403):**

```json
{
    "message": "Only super admins can update role",
    "required_role": "super_admin",
    "your_role": "admin"
}
```

---

## Test Case 24: Delete Own Admin Profile

**Endpoint:** `DELETE /admin/profile`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Profile deleted successfully"
}
```

---

# Admin Management (Super Admin Only)

## Test Case 25: Get All Admins (Super Admin)

**Endpoint:** `GET /admins`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Manager Account",
            "email": "manager@example.com",
            "phone_number": "1111111111",
            "role": "manager",
            "balance": 0
        },
        {
            "id": 2,
            "name": "Admin Account",
            "email": "admin@example.com",
            "phone_number": "2222222222",
            "role": "admin",
            "balance": 0
        },
        {
            "id": 3,
            "name": "Super Admin Account",
            "email": "superadmin@example.com",
            "phone_number": "3333333333",
            "role": "super_admin",
            "balance": 0
        }
    ]
}
```

---

## Test Case 26: Get All Admins (Manager - Should Fail)

**Endpoint:** `GET /admins`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Only super admins can access admin data",
    "required_role": "super_admin",
    "your_role": "manager"
}
```

---

## Test Case 27: Get All Admins (Admin - Should Fail)

**Endpoint:** `GET /admins`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Only super admins can access admin data",
    "required_role": "super_admin",
    "your_role": "admin"
}
```

---

## Test Case 28: Get Specific Admin (Super Admin)

**Endpoint:** `GET /admins/2`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Admin Account",
        "email": "admin@example.com",
        "phone_number": "2222222222",
        "role": "admin",
        "balance": 0
    }
}
```

---

## Test Case 29: Get Specific Admin - Admin Not Found

**Endpoint:** `GET /admins/999`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Admin not found"
}
```

---

## Test Case 30: Update Other Admin (Super Admin)

**Endpoint:** `PUT /admins/1`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Updated Manager",
    "email": "manager_updated@example.com",
    "phone_number": "1111111112"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Admin updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Manager",
        "email": "manager_updated@example.com",
        "phone_number": "1111111112",
        "role": "manager",
        "balance": 0
    }
}
```

---

## Test Case 31: Update Other Admin - Change Role (Super Admin)

**Endpoint:** `PUT /admins/1`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "role": "admin"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Admin updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Manager",
        "email": "manager_updated@example.com",
        "phone_number": "1111111112",
        "role": "admin",
        "balance": 0
    }
}
```

---

## Test Case 32: Update Other Admin (Manager - Should Fail)

**Endpoint:** `PUT /admins/2`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Attempt Update"
}
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Only super admins can update other admins",
    "required_role": "super_admin",
    "your_role": "manager"
}
```

---

## Test Case 33: Delete Admin (Super Admin)

**Endpoint:** `DELETE /admins/1`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Admin deleted successfully"
}
```

---

## Test Case 34: Delete Admin - Cannot Delete Self

**Endpoint:** `DELETE /admins/3`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "success": false,
    "message": "Cannot delete your own account"
}
```

---

## Test Case 35: Delete Admin (Admin - Should Fail)

**Endpoint:** `DELETE /admins/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Only super admins can delete admins",
    "required_role": "super_admin",
    "your_role": "admin"
}
```

---

# Customer Data Access (Admin+)

## Test Case 36: List All Customers (Admin)

**Endpoint:** `GET /customers`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Customer",
            "email": "customer@example.com",
            "phone_number": "9999999999",
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 37: List All Customers (Manager - Should Fail)

**Endpoint:** `GET /customers`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Managers cannot access customer data",
    "required_role": ["admin", "super_admin"],
    "your_role": "manager"
}
```

---

## Test Case 38: List All Customers (Super Admin)

**Endpoint:** `GET /customers`

**Headers:**

```
Authorization: Bearer {{super_admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Customer",
            "email": "customer@example.com",
            "phone_number": "9999999999",
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 39: Get Specific Customer (Admin)

**Endpoint:** `GET /customers/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Customer",
        "email": "customer@example.com",
        "phone_number": "9999999999",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 40: Get Specific Customer (Manager - Should Fail)

**Endpoint:** `GET /customers/1`

**Headers:**

```
Authorization: Bearer {{manager_token}}
Content-Type: application/json
```

**Error Response (403):**

```json
{
    "message": "Forbidden - Managers cannot access customer data",
    "required_role": ["admin", "super_admin"],
    "your_role": "manager"
}
```

---

## Test Case 41: Get Specific Customer - Not Found

**Endpoint:** `GET /customers/999`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Customer not found"
}
```

---

# Vehicle Management (Customer)

## Test Case 42: Create Vehicle (Customer)

**Endpoint:** `POST /vehicles`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "code": "V001",
    "name": "Honda Civic",
    "description": "Silver 2023",
    "vehicle_type_id": 1,
    "license_plate": "ABC-1234"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Vehicle created successfully",
    "data": {
        "id": 1,
        "customer_id": 1,
        "code": "V001",
        "name": "Honda Civic",
        "description": "Silver 2023",
        "vehicle_type_id": 1,
        "license_plate": "ABC-1234",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 43: Get All Vehicles (Customer)

**Endpoint:** `GET /vehicles`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "customer_id": 1,
            "code": "V001",
            "name": "Honda Civic",
            "description": "Silver 2023",
            "vehicle_type_id": 1,
            "license_plate": "ABC-1234",
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 44: Get Specific Vehicle (Customer)

**Endpoint:** `GET /vehicles/1`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "customer_id": 1,
        "code": "V001",
        "name": "Honda Civic",
        "description": "Silver 2023",
        "vehicle_type_id": 1,
        "license_plate": "ABC-1234",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 45: Update Vehicle (Customer)

**Endpoint:** `PUT /vehicles/1`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Honda Civic 2023",
    "description": "Silver - Sedan"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Vehicle updated successfully",
    "data": {
        "id": 1,
        "customer_id": 1,
        "code": "V001",
        "name": "Honda Civic 2023",
        "description": "Silver - Sedan",
        "vehicle_type_id": 1,
        "license_plate": "ABC-1234",
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 46: Delete Vehicle (Customer)

**Endpoint:** `DELETE /vehicles/1`

**Headers:**

```
Authorization: Bearer {{customer_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Vehicle deleted successfully"
}
```

---

# Vehicle Types (Admin)

## Test Case 47: Create Vehicle Type

**Endpoint:** `POST /vehicle-types`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "code": "VT001",
    "name": "Sedan",
    "description": "4-door sedan vehicles",
    "is_active": true
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Vehicle type created successfully",
    "data": {
        "id": 1,
        "code": "VT001",
        "name": "Sedan",
        "description": "4-door sedan vehicles",
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 48: Get All Vehicle Types

**Endpoint:** `GET /vehicle-types`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "VT001",
            "name": "Sedan",
            "description": "4-door sedan vehicles",
            "is_active": true,
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 49: Get Specific Vehicle Type

**Endpoint:** `GET /vehicle-types/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "VT001",
        "name": "Sedan",
        "description": "4-door sedan vehicles",
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 50: Update Vehicle Type

**Endpoint:** `PUT /vehicle-types/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Sedan (Updated)",
    "is_active": true
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Vehicle type updated successfully",
    "data": {
        "id": 1,
        "code": "VT001",
        "name": "Sedan (Updated)",
        "description": "4-door sedan vehicles",
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 51: Delete Vehicle Type

**Endpoint:** `DELETE /vehicle-types/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Vehicle type deleted successfully"
}
```

---

# Service Packages (Admin)

## Test Case 52: Create Service Package

**Endpoint:** `POST /service-packages`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "code": "SP001",
    "name": "Basic Wash",
    "description": "Standard car wash service",
    "is_active": true,
    "sort_order": 1
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Service package created successfully",
    "data": {
        "id": 1,
        "code": "SP001",
        "name": "Basic Wash",
        "description": "Standard car wash service",
        "is_active": true,
        "sort_order": 1,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 53: Get All Service Packages

**Endpoint:** `GET /service-packages`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "SP001",
            "name": "Basic Wash",
            "description": "Standard car wash service",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 54: Get Specific Service Package

**Endpoint:** `GET /service-packages/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "SP001",
        "name": "Basic Wash",
        "description": "Standard car wash service",
        "is_active": true,
        "sort_order": 1,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 55: Update Service Package

**Endpoint:** `PUT /service-packages/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Premium Wash",
    "description": "Professional car wash with wax",
    "sort_order": 2
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Service package updated successfully",
    "data": {
        "id": 1,
        "code": "SP001",
        "name": "Premium Wash",
        "description": "Professional car wash with wax",
        "is_active": true,
        "sort_order": 2,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 56: Delete Service Package

**Endpoint:** `DELETE /service-packages/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Service package deleted successfully"
}
```

---

# Pricing Entries (Admin)

## Test Case 57: Create Pricing Entry

**Endpoint:** `POST /pricing-entries`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "code": "PE001",
    "vehicle_type_id": 1,
    "service_package_id": 1,
    "price": 299.99,
    "is_active": true
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Pricing entry created successfully",
    "data": {
        "id": 1,
        "code": "PE001",
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price": 299.99,
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 58: Get All Pricing Entries

**Endpoint:** `GET /pricing-entries`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "PE001",
            "vehicle_type_id": 1,
            "service_package_id": 1,
            "price": 299.99,
            "is_active": true,
            "created_at": "2026-04-13T10:00:00.000000Z"
        }
    ]
}
```

---

## Test Case 59: Get Specific Pricing Entry

**Endpoint:** `GET /pricing-entries/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "PE001",
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price": 299.99,
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 60: Update Pricing Entry

**Endpoint:** `PUT /pricing-entries/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**

```json
{
    "price": 349.99
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Pricing entry updated successfully",
    "data": {
        "id": 1,
        "code": "PE001",
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price": 349.99,
        "is_active": true,
        "created_at": "2026-04-13T10:00:00.000000Z"
    }
}
```

---

## Test Case 61: Delete Pricing Entry

**Endpoint:** `DELETE /pricing-entries/1`

**Headers:**

```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Pricing entry deleted successfully"
}
```

---

# Role-Based Access Control Tests

## Summary of RBAC Testing

### Test Cases by Role Restriction

| Test Case | Endpoint                  | Manager | Admin  | Super Admin | Expected Result                      |
| --------- | ------------------------- | ------- | ------ | ----------- | ------------------------------------ |
| 26        | GET /admins               | ❌ 403  | ❌ 403 | ✅ 200      | Only super admin can list admins     |
| 27        | GET /admins               | ❌ 403  | ❌ 403 | ✅ 200      | Forbids manager access to admins     |
| 28        | GET /admins/2             | ❌ 403  | ❌ 403 | ✅ 200      | Only super admin can view admin      |
| 32        | PUT /admins/2             | ❌ 403  | ❌ 403 | ✅ 200      | Only super admin can update admin    |
| 35        | DELETE /admins            | ❌ 403  | ❌ 403 | ✅ 200      | Only super admin can delete admin    |
| 37        | GET /customers            | ❌ 403  | ✅ 200 | ✅ 200      | Manager blocked from customer data   |
| 40        | GET /customers/1          | ❌ 403  | ✅ 200 | ✅ 200      | Manager blocked from customer detail |
| 22        | PUT /admin/profile (role) | ❌ 403  | ❌ 403 | ✅ 200      | Only super admin can update role     |

---

## Error Response Format

**Standard 403 Forbidden Response:**

```json
{
    "message": "Forbidden message here",
    "required_role": "super_admin",
    "your_role": "manager"
}
```

**Standard 401 Unauthenticated Response:**

```json
{
    "message": "Unauthenticated."
}
```

**Standard 404 Not Found Response:**

```json
{
    "success": false,
    "message": "Resource not found"
}
```

**Standard 422 Validation Error Response:**

```json
{
    "message": "Validation failed",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

---

## Quick Testing Checklist

- [ ] Create 3 test admin accounts (manager, admin, super_admin)
- [ ] Create 1 test customer account
- [ ] Test all authentication endpoints (register, login, logout)
- [ ] Test profile management (get, update, delete)
- [ ] Test admin management (only super_admin)
- [ ] Test customer data access (manager blocked, admin+)
- [ ] Test vehicle CRUD (customer only)
- [ ] Test vehicle types CRUD (all admin roles)
- [ ] Test service packages CRUD (all admin roles)
- [ ] Test pricing entries CRUD (all admin roles)
- [ ] Verify 403 responses include role information
- [ ] Test cannot delete self (admin)
- [ ] Test role update restrictions (only super_admin)

---

## Postman Environment Variables

Set these variables in Postman environment:

```json
{
    "baseUrl": "http://localhost:8000/api",
    "manager_token": "paste-token-here",
    "admin_token": "paste-token-here",
    "super_admin_token": "paste-token-here",
    "customer_token": "paste-token-here"
}
```

Then use in requests like: `{{baseUrl}}/admin/login`

---

**Total Test Cases: 61**
