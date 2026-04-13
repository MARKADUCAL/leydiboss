# Customer Authentication API Documentation

## Overview

This documentation covers the REST API for customer registration, login, and account management. The API uses **Laravel Sanctum** for API token-based authentication.

---

## Table of Contents

1. [Installation & Setup](#installation--setup)
2. [API Endpoints](#api-endpoints)
3. [Request/Response Examples](#requestresponse-examples)
4. [Code Structure](#code-structure)
5. [Error Handling](#error-handling)
6. [Authentication](#authentication)

---

## Installation & Setup

### 1. Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. Add Trait to Customer Model

The `Customer` model must include the `HasApiTokens` trait:

```php
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    // ...
}
```

### 3. Configure Guard (Already configured in config/auth.php)

```php
'guards' => [
    'customer' => [
        'driver' => 'session',
        'provider' => 'customers',
    ],
],
'providers' => [
    'customers' => [
        'driver' => 'eloquent',
        'model' => App\Models\Customer::class,
    ],
],
```

---

## API Endpoints

### 1. Register Customer (PUBLIC)

**Endpoint:** `POST /api/customer/register`

**Description:** Creates a new customer account and returns an API token.

**Required Headers:**

```
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "Jane Smith",
    "email": "jane.smith@email.com",
    "phone_number": "09171234567",
    "password": "securePass456",
    "password_confirmation": "securePass456"
}
```

**Validation Rules:**

- `name`: Required, string, max 255 characters
- `email`: Required, email format, unique in customers table
- `phone_number`: Required, string, unique in customers table
- `password`: Required, min 8 characters, must match confirmation

**Success Response (201 Created):**

```json
{
    "message": "Customer registered successfully",
    "customer": {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane.smith@email.com",
        "phone_number": "09171234567",
        "balance": 0
    },
    "access_token": "1|abcdef123456...",
    "token_type": "Bearer"
}
```

**Error Response (422 Unprocessable Entity):**

```json
{
    "message": "Email is already registered.",
    "errors": {
        "email": ["This email is already registered."]
    }
}
```

---

### 2. Login Customer (PUBLIC)

**Endpoint:** `POST /api/customer/login`

**Description:** Authenticates a customer and returns an API token.

**Request Body:**

```json
{
    "email": "jane.smith@email.com",
    "password": "securePass456"
}
```

**Validation Rules:**

- `email`: Required, email format
- `password`: Required, string

**Success Response (200 OK):**

```json
{
    "message": "Login successful",
    "customer": {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane.smith@email.com",
        "phone_number": "09171234567",
        "balance": 0
    },
    "access_token": "1|abcdef123456...",
    "token_type": "Bearer"
}
```

**Error Response (401 Unauthorized):**

```json
{
    "message": "Invalid email or password"
}
```

---

### 3. Logout Customer (PROTECTED)

**Endpoint:** `POST /api/customer/logout`

**Description:** Revokes the current API token (logs out the customer).

**Required Headers:**

```
Authorization: Bearer {access_token}
```

**Success Response (200 OK):**

```json
{
    "message": "Logout successful"
}
```

---

### 4. Get Customer Profile (PROTECTED)

**Endpoint:** `GET /api/customer/profile`

**Description:** Returns the authenticated customer's profile information.

**Required Headers:**

```
Authorization: Bearer {access_token}
```

**Success Response (200 OK):**

```json
{
    "id": 1,
    "name": "Jane Smith",
    "email": "jane.smith@email.com",
    "phone_number": "09171234567",
    "balance": 0,
    "created_at": "2026-04-10T10:30:00.000000Z",
    "updated_at": "2026-04-10T10:30:00.000000Z"
}
```

---

### 5. Get All Customers (PUBLIC)

**Endpoint:** `GET /api/customers`

**Description:** Retrieves a list of all customers in the system.

**No Authentication Required**

**Success Response (200 OK):**

```json
{
    "message": "Customers retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Jane Smith",
            "email": "jane.smith@email.com",
            "phone_number": "09171234567",
            "balance": 0,
            "created_at": "2026-04-10T10:30:00.000000Z",
            "updated_at": "2026-04-10T10:30:00.000000Z"
        },
        {
            "id": 2,
            "name": "John Doe",
            "email": "john.doe@email.com",
            "phone_number": "09187654321",
            "balance": 500,
            "created_at": "2026-04-10T11:15:00.000000Z",
            "updated_at": "2026-04-10T11:15:00.000000Z"
        }
    ],
    "count": 2
}
```

**Error Response (500 Internal Server Error):**

```json
{
    "message": "Failed to retrieve customers",
    "error": "Database connection error"
}
```

---

### 6. Get Single Customer (PUBLIC)

**Endpoint:** `GET /api/customers/{id}`

**Description:** Retrieves a specific customer by their ID.

**No Authentication Required**

**URL Parameters:**

- `id`: Customer ID (integer)

**Success Response (200 OK):**

```json
{
    "message": "Customer retrieved successfully",
    "data": {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane.smith@email.com",
        "phone_number": "09171234567",
        "balance": 0,
        "profile_photo_path": "/customers/photos/jane.jpg",
        "created_at": "2026-04-10T10:30:00.000000Z",
        "updated_at": "2026-04-10T10:30:00.000000Z"
    }
}
```

**Error Response (404 Not Found):**

```json
{
    "message": "Customer not found"
}
```

---

## Request/Response Examples

### Example 1: Complete Registration Flow

**Step 1: Register**

```bash
curl -X POST http://localhost:8000/api/customer/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane.smith@email.com",
    "phone_number": "09171234567",
    "password": "securePass456",
    "password_confirmation": "securePass456"
  }'
```

**Response:**

```json
{
    "message": "Customer registered successfully",
    "customer": {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane.smith@email.com",
        "phone_number": "09171234567",
        "balance": 0
    },
    "access_token": "1|abcdef123456xyz789...",
    "token_type": "Bearer"
}
```

**Step 2: Use Token to Get Profile**

```bash
curl -X GET http://localhost:8000/api/customer/profile \
  -H "Authorization: Bearer 1|abcdef123456xyz789..."
```

**Step 3: Logout**

```bash
curl -X POST http://localhost:8000/api/customer/logout \
  -H "Authorization: Bearer 1|abcdef123456xyz789..."
```

### Example 2: Login Flow

**Step 1: Login**

```bash
curl -X POST http://localhost:8000/api/customer/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jane.smith@email.com",
    "password": "securePass456"
  }'
```

**Step 2: Get All Customers**

```bash
curl -X GET http://localhost:8000/api/customers
```

**Step 3: Get Single Customer**

```bash
curl -X GET http://localhost:8000/api/customers/1
```

---

## Code Structure

### File Organization

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── CustomerAuthController.php
│   └── Requests/
│       ├── CustomerLoginRequest.php
│       └── CustomerRegisterRequest.php
├── Models/
│   └── Customer.php
routes/
└── api.php
```

### CustomerAuthController.php

**File Location:** `app/Http/Controllers/Api/CustomerAuthController.php`

**Methods:**

#### 1. `register(CustomerRegisterRequest $request): JsonResponse`

- Creates a new customer account
- Hashes the password using `Hash::make()`
- Creates an API token using Sanctum's `createToken()` method
- Returns customer data and access token

```php
public function register(CustomerRegisterRequest $request): JsonResponse
{
    $customer = Customer::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'password' => Hash::make($request->password),
        'balance' => 0,
    ]);

    $token = $customer->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'Customer registered successfully',
        'customer' => [...],
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 201);
}
```

#### 2. `login(CustomerLoginRequest $request): JsonResponse`

- Finds customer by email
- Verifies password using `Hash::check()`
- Creates an API token on successful authentication
- Returns 401 for invalid credentials

```php
public function login(CustomerLoginRequest $request): JsonResponse
{
    $customer = Customer::where('email', $request->email)->first();

    if (!$customer || !Hash::check($request->password, $customer->password)) {
        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
    }

    $token = $customer->createToken('api_token')->plainTextToken;

    return response()->json([...], 200);
}
```

#### 3. `logout(): JsonResponse`

- Revokes the current API token
- Uses Sanctum's `currentAccessToken()` method
- Deletes the token from database

```php
public function logout(): JsonResponse
{
    auth('sanctum')->user()?->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout successful',
    ], 200);
}
```

#### 4. `index(): JsonResponse`

- Retrieves all customers from database
- Maps customer data to exclude sensitive info
- Returns count and array of customers

```php
public function index(): JsonResponse
{
    $customers = Customer::all()->map(function ($customer) {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone_number' => $customer->phone_number,
            'balance' => $customer->balance,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,
        ];
    });

    return response()->json([
        'message' => 'Customers retrieved successfully',
        'data' => $customers,
        'count' => count($customers),
    ], 200);
}
```

#### 5. `show($id): JsonResponse`

- Retrieves a single customer by ID
- Returns 404 if customer not found
- Includes profile photo path in response

```php
public function show($id): JsonResponse
{
    $customer = Customer::find($id);

    if (!$customer) {
        return response()->json([
            'message' => 'Customer not found',
        ], 404);
    }

    return response()->json([
        'message' => 'Customer retrieved successfully',
        'data' => [...],
    ], 200);
}
```

---

### Form Requests

#### CustomerRegisterRequest.php

**File Location:** `app/Http/Requests/CustomerRegisterRequest.php`

**Validation Rules:**

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
        'phone_number' => 'required|string|unique:customers,phone_number',
        'password' => 'required|string|min:8|confirmed',
    ];
}
```

**Custom Messages:**

```php
public function messages(): array
{
    return [
        'email.unique' => 'This email is already registered.',
        'phone_number.unique' => 'This phone number is already registered.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];
}
```

#### CustomerLoginRequest.php

**File Location:** `app/Http/Requests/CustomerLoginRequest.php`

**Validation Rules:**

```php
public function rules(): array
{
    return [
        'email' => 'required|email',
        'password' => 'required|string',
    ];
}
```

---

### API Routes

**File Location:** `routes/api.php`

```php
// Public Routes (No Authentication Required)
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);
Route::get('/customers', [CustomerAuthController::class, 'index']);
Route::get('/customers/{id}', [CustomerAuthController::class, 'show']);

// Protected Routes (Authentication Required with Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/customer/profile', function (Request $request) {
        return $request->user();
    });
});
```

---

### Customer Model Updates

**File Location:** `app/Models/Customer.php`

**Added Trait:**

```php
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    // ...
}
```

**The `HasApiTokens` trait provides:**

- `createToken($name)` - Creates a new API token
- `tokens()` - Relationship to access tokens
- `currentAccessToken()` - Get the current token
- Token revocation methods

---

## Error Handling

All endpoints use try-catch blocks to handle exceptions gracefully.

### Common Error Responses

**400 Bad Request - Validation Error:**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

**401 Unauthorized:**

```json
{
    "message": "Invalid email or password"
}
```

**404 Not Found:**

```json
{
    "message": "Customer not found"
}
```

**500 Internal Server Error:**

```json
{
    "message": "Registration failed",
    "error": "SQLSTATE[HY000]: General error: ..."
}
```

---

## Authentication

### Bearer Token Authentication

The API uses **bearer token authentication** via Laravel Sanctum.

**How it works:**

1. Customer registers or logs in
2. Server creates an API token using `createToken()`
3. Client stores the token securely
4. Client includes token in `Authorization` header for protected routes

**Format:**

```
Authorization: Bearer {access_token}
```

**Example:**

```bash
curl -X GET http://localhost:8000/api/customer/profile \
  -H "Authorization: Bearer 1|abcdef123456xyz789..."
```

### Token Management

**Creating Tokens:**

```php
$token = $customer->createToken('api_token')->plainTextToken;
```

**Revoking Tokens:**

```php
$customer->tokens()->delete(); // Delete all tokens
$customer->currentAccessToken()->delete(); // Delete current token
```

**Accessing Current User in Protected Routes:**

```php
$user = auth('sanctum')->user(); // Get authenticated customer
```

---

## Testing with Postman

### Setup Postman Variable

1. In Postman, create an environment variable `token`
2. After login/register, copy the `access_token` from response
3. In Tests tab, add:

```javascript
pm.environment.set("token", pm.response.json().access_token);
```

### Create Requests

**Register Request:**

```
POST http://localhost:8000/api/customer/register
Headers: Content-Type: application/json
Body (raw JSON):
{
  "name": "Test User",
  "email": "test@example.com",
  "phone_number": "09123456789",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Login Request:**

```
POST http://localhost:8000/api/customer/login
Headers: Content-Type: application/json
Body (raw JSON):
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Get Profile (Protected):**

```
GET http://localhost:8000/api/customer/profile
Headers: Authorization: Bearer {{token}}
```

**Get All Customers:**

```
GET http://localhost:8000/api/customers
```

**Get Single Customer:**

```
GET http://localhost:8000/api/customers/1
```

**Logout (Protected):**

```
POST http://localhost:8000/api/customer/logout
Headers: Authorization: Bearer {{token}}
```

---

## Summary

| Endpoint                 | Method | Auth | Description                       |
| ------------------------ | ------ | ---- | --------------------------------- |
| `/api/customer/register` | POST   | No   | Register new customer             |
| `/api/customer/login`    | POST   | No   | Login customer                    |
| `/api/customer/logout`   | POST   | Yes  | Logout customer                   |
| `/api/customer/profile`  | GET    | Yes  | Get profile of logged-in customer |
| `/api/customers`         | GET    | No   | Get all customers                 |
| `/api/customers/{id}`    | GET    | No   | Get single customer by ID         |

---

## Security Notes

✅ **Implemented:**

- Password hashing using Laravel's `Hash` facade
- Unique email and phone validation
- API token generation via Sanctum
- Protected routes with `auth:sanctum` middleware
- Password confirmation validation

⚠️ **Recommendations:**

- Always use HTTPS in production
- Implement rate limiting on auth endpoints
- Add CORS middleware if needed
- Consider adding email verification
- Implement refresh token rotation
- Add audit logging for login/logout events
