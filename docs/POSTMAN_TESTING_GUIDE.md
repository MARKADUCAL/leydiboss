# Postman Testing Guide: Service Packages & Pricing Entries

## Base URL

```
http://localhost:8000/api
```

---

## 1. SERVICE PACKAGES ENDPOINTS

### 1.1 Create Service Package

**Endpoint:** `POST /api/service-packages`

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
    "code": "p1",
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
    "data": {
        "id": 1,
        "code": "p1",
        "name": "Basic Wash",
        "description": "Standard car wash service",
        "is_active": true,
        "sort_order": 1,
        "created_at": "2026-04-09T10:30:00.000000Z",
        "updated_at": "2026-04-09T10:30:00.000000Z",
        "deleted_at": null
    },
    "message": "Service package created successfully"
}
```

**Error Response (422) - Duplicate code:**

```json
{
    "message": "The code has already been taken.",
    "errors": {
        "code": ["The code has already been taken."]
    }
}
```

---

### 1.2 Get All Service Packages

**Endpoint:** `GET /api/service-packages`

**Request Headers:**

```
Accept: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "p1",
            "name": "Basic Wash",
            "description": "Standard car wash service",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:30:00.000000Z",
            "updated_at": "2026-04-09T10:30:00.000000Z",
            "deleted_at": null
        },
        {
            "id": 2,
            "code": "p2",
            "name": "Premium Wash",
            "description": "Detailed car wash with wax",
            "is_active": true,
            "sort_order": 2,
            "created_at": "2026-04-09T10:35:00.000000Z",
            "updated_at": "2026-04-09T10:35:00.000000Z",
            "deleted_at": null
        }
    ],
    "message": "Service packages retrieved successfully"
}
```

---

### 1.3 Get Single Service Package

**Endpoint:** `GET /api/service-packages/{id}`

**Example:** `GET /api/service-packages/1`

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "p1",
        "name": "Basic Wash",
        "description": "Standard car wash service",
        "is_active": true,
        "sort_order": 1,
        "created_at": "2026-04-09T10:30:00.000000Z",
        "updated_at": "2026-04-09T10:30:00.000000Z",
        "deleted_at": null
    },
    "message": "Service package retrieved successfully"
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Service package not found"
}
```

---

### 1.4 Update Service Package

**Endpoint:** `PUT /api/service-packages/{id}`

**Example:** `PUT /api/service-packages/1`

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body (all fields optional):**

```json
{
    "code": "p1-updated",
    "name": "Basic Wash Updated",
    "description": "Updated description",
    "is_active": false,
    "sort_order": 2
}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "p1-updated",
        "name": "Basic Wash Updated",
        "description": "Updated description",
        "is_active": false,
        "sort_order": 2,
        "created_at": "2026-04-09T10:30:00.000000Z",
        "updated_at": "2026-04-09T11:00:00.000000Z",
        "deleted_at": null
    },
    "message": "Service package updated successfully"
}
```

---

### 1.5 Delete Service Package

**Endpoint:** `DELETE /api/service-packages/{id}`

**Example:** `DELETE /api/service-packages/1`

**Success Response (200):**

```json
{
    "success": true,
    "message": "Service package deleted successfully"
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Service package not found"
}
```

---

## 2. PRICING ENTRIES ENDPOINTS

### 2.1 Create Pricing Entry

**Endpoint:** `POST /api/pricing-entries`

**Note:** Before creating pricing entries, ensure you have:

- At least one Vehicle Type (from vehicle-types endpoint)
- At least one Service Package (from above)

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
    "vehicle_type_id": 1,
    "service_package_id": 1,
    "price": 599.99,
    "is_active": true
}
```

**Success Response (201):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price_cents": 59999,
        "price": 599.99,
        "is_active": true,
        "created_at": "2026-04-09T11:00:00.000000Z",
        "updated_at": "2026-04-09T11:00:00.000000Z",
        "deleted_at": null,
        "vehicle_type": {
            "id": 1,
            "code": "S",
            "label": "Small",
            "description": "Sedans (all sedan types)",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:00:00.000000Z",
            "updated_at": "2026-04-09T10:00:00.000000Z",
            "deleted_at": null
        },
        "service_package": {
            "id": 1,
            "code": "p1",
            "name": "Basic Wash",
            "description": "Standard car wash service",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:30:00.000000Z",
            "updated_at": "2026-04-09T10:30:00.000000Z",
            "deleted_at": null
        }
    },
    "message": "Pricing entry created successfully"
}
```

**Error Response (422) - Duplicate Combination:**

```json
{
    "success": false,
    "message": "A pricing entry already exists for this vehicle type and service package combination"
}
```

**Error Response (422) - Invalid Vehicle Type:**

```json
{
    "message": "The selected vehicle_type_id is invalid.",
    "errors": {
        "vehicle_type_id": ["The selected vehicle_type_id is invalid."]
    }
}
```

---

### 2.2 Get All Pricing Entries

**Endpoint:** `GET /api/pricing-entries`

**Optional Query Parameters:**

```
?vehicle_type_id=1       // Filter by vehicle type
?service_package_id=1    // Filter by service package
?is_active=true          // Filter by active status
```

**Example with filters:** `GET /api/pricing-entries?vehicle_type_id=1&is_active=true`

**Request Headers:**

```
Accept: application/json
```

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "vehicle_type_id": 1,
            "service_package_id": 1,
            "price_cents": 59999,
            "price": 599.99,
            "is_active": true,
            "created_at": "2026-04-09T11:00:00.000000Z",
            "updated_at": "2026-04-09T11:00:00.000000Z",
            "deleted_at": null,
            "vehicle_type": {
                "id": 1,
                "code": "S",
                "label": "Small",
                "description": "Sedans (all sedan types)",
                "is_active": true,
                "sort_order": 1,
                "created_at": "2026-04-09T10:00:00.000000Z",
                "updated_at": "2026-04-09T10:00:00.000000Z",
                "deleted_at": null
            },
            "service_package": {
                "id": 1,
                "code": "p1",
                "name": "Basic Wash",
                "description": "Standard car wash service",
                "is_active": true,
                "sort_order": 1,
                "created_at": "2026-04-09T10:30:00.000000Z",
                "updated_at": "2026-04-09T10:30:00.000000Z",
                "deleted_at": null
            }
        }
    ],
    "message": "Pricing entries retrieved successfully"
}
```

---

### 2.3 Get Single Pricing Entry

**Endpoint:** `GET /api/pricing-entries/{id}`

**Example:** `GET /api/pricing-entries/1`

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price_cents": 59999,
        "price": 599.99,
        "is_active": true,
        "created_at": "2026-04-09T11:00:00.000000Z",
        "updated_at": "2026-04-09T11:00:00.000000Z",
        "deleted_at": null,
        "vehicle_type": {
            "id": 1,
            "code": "S",
            "label": "Small",
            "description": "Sedans (all sedan types)",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:00:00.000000Z",
            "updated_at": "2026-04-09T10:00:00.000000Z",
            "deleted_at": null
        },
        "service_package": {
            "id": 1,
            "code": "p1",
            "name": "Basic Wash",
            "description": "Standard car wash service",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:30:00.000000Z",
            "updated_at": "2026-04-09T10:30:00.000000Z",
            "deleted_at": null
        }
    },
    "message": "Pricing entry retrieved successfully"
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Pricing entry not found"
}
```

---

### 2.4 Update Pricing Entry

**Endpoint:** `PUT /api/pricing-entries/{id}`

**Example:** `PUT /api/pricing-entries/1`

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body (all fields optional):**

```json
{
    "vehicle_type_id": 1,
    "service_package_id": 1,
    "price": 699.99,
    "is_active": false
}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "vehicle_type_id": 1,
        "service_package_id": 1,
        "price_cents": 69999,
        "price": 699.99,
        "is_active": false,
        "created_at": "2026-04-09T11:00:00.000000Z",
        "updated_at": "2026-04-09T11:30:00.000000Z",
        "deleted_at": null,
        "vehicle_type": {
            "id": 1,
            "code": "S",
            "label": "Small",
            "description": "Sedans (all sedan types)",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:00:00.000000Z",
            "updated_at": "2026-04-09T10:00:00.000000Z",
            "deleted_at": null
        },
        "service_package": {
            "id": 1,
            "code": "p1",
            "name": "Basic Wash",
            "description": "Standard car wash service",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-04-09T10:30:00.000000Z",
            "updated_at": "2026-04-09T10:30:00.000000Z",
            "deleted_at": null
        }
    },
    "message": "Pricing entry updated successfully"
}
```

---

### 2.5 Delete Pricing Entry

**Endpoint:** `DELETE /api/pricing-entries/{id}`

**Example:** `DELETE /api/pricing-entries/1`

**Success Response (200):**

```json
{
    "success": true,
    "message": "Pricing entry deleted successfully"
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Pricing entry not found"
}
```

---

## Testing Workflow in Postman

### Step 1: Create Service Packages

1. POST `/api/service-packages` with code `p1`, name `Basic Wash`
2. POST `/api/service-packages` with code `p2`, name `Premium Wash`
3. POST `/api/service-packages` with code `p3`, name `Deluxe Wash`

### Step 2: Create Vehicle Types (if needed)

1. POST `/api/vehicle-types` with code `S`, label `Small`
2. POST `/api/vehicle-types` with code `M`, label `Medium`
3. POST `/api/vehicle-types` with code `L`, label `Large`

### Step 3: Create Pricing Entries

1. POST `/api/pricing-entries` (S + p1 = 599.99)
2. POST `/api/pricing-entries` (S + p2 = 799.99)
3. POST `/api/pricing-entries` (M + p1 = 699.99)
4. POST `/api/pricing-entries` (M + p2 = 899.99)

### Step 4: Test Retrieval

1. GET `/api/service-packages` - Get all packages
2. GET `/api/pricing-entries` - Get all pricing entries
3. GET `/api/pricing-entries?vehicle_type_id=1` - Filter by vehicle type
4. GET `/api/pricing-entries?is_active=true` - Filter by active status

### Step 5: Test Updates

1. PUT `/api/service-packages/1` - Update package name
2. PUT `/api/pricing-entries/1` - Update price to 649.99

### Step 6: Test Deletions

1. DELETE `/api/service-packages/3` - Delete a package
2. DELETE `/api/pricing-entries/1` - Delete a pricing entry

---

## Important Notes

1. **Price Storage:** Prices are stored as `price_cents` (integer) but displayed as `price` (decimal)
    - Example: 599.99 is stored as 59999 cents
2. **Uniqueness Constraint:** Pricing entries enforce a unique combination of `vehicle_type_id` + `service_package_id`
    - You cannot have two pricing entries for the same vehicle type and service package

3. **Soft Deletes:** Deleted items are soft-deleted (retained in database with `deleted_at` timestamp)
    - Use this for audit trails and recovery

4. **Boolean Handling:**
    - In requests: Use `true`/`false` or `1`/`0`
    - In responses: Both shown as proper boolean values

5. **Pagination:** List endpoints return all results. For pagination, modify the controller if needed.

6. **Validation Rules:**
    - Service Package `code`: max 50 characters (must be unique)
    - Service Package `name`: required, max 100 characters
    - Service Package `description`: optional, max 500 characters
    - Pricing Entry `price`: must be numeric, >= 0

---

## Common Testing Scenarios

### Scenario 1: Invalid Vehicle Type

```json
POST /api/pricing-entries
{
  "vehicle_type_id": 999,
  "service_package_id": 1,
  "price": 599.99,
  "is_active": true
}
```

Expected: 422 error - vehicle_type_id not found

### Scenario 2: Duplicate Pricing Entry

```json
POST /api/pricing-entries
{
  "vehicle_type_id": 1,
  "service_package_id": 1,
  "price": 599.99,
  "is_active": true
}

POST /api/pricing-entries  // Same data again
{
  "vehicle_type_id": 1,
  "service_package_id": 1,
  "price": 599.99,
  "is_active": true
}
```

Expected: First succeeds (201), second fails (422)

### Scenario 3: Negative Price

```json
POST /api/pricing-entries
{
  "vehicle_type_id": 1,
  "service_package_id": 1,
  "price": -100,
  "is_active": true
}
```

Expected: 422 validation error - price must be >= 0
