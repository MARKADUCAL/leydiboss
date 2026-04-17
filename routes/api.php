<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleTypeController;
use App\Http\Controllers\Api\ServicePackageController;
use App\Http\Controllers\Api\PricingEntryController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerVehicleController;
use App\Http\Controllers\Api\AdminAuthController;

// ─── login and register Routes ────────────────────────────────────────
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);

// ─── Admin login and register Routes ──────────────────────────────────
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// ─── Customer Authenticated Routes ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);
    Route::put('/customer/profile', [CustomerAuthController::class, 'updateProfile']);
    Route::post('/customer/profile-photo', [CustomerAuthController::class, 'uploadProfilePhoto']);
    Route::delete('/customer/profile', [CustomerAuthController::class, 'deleteProfile']);
    Route::apiResource('vehicles', CustomerVehicleController::class);
});

// ─── Admin Authenticated Routes ───────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin.api'])->group(function () {
    // Admin Auth
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);
    Route::put('/admin/profile', [AdminAuthController::class, 'updateProfile']);
    Route::post('/admin/profile-photo', [AdminAuthController::class, 'uploadProfilePhoto']);
    Route::delete('/admin/profile', [AdminAuthController::class, 'deleteProfile']);

    // Super Admin can manage other admins
    Route::get('/admins', [AdminAuthController::class, 'index']);
    Route::get('/admins/{id}', [AdminAuthController::class, 'show']);
    Route::put('/admins/{id}', [AdminAuthController::class, 'updateAdmin']);
    Route::delete('/admins/{id}', [AdminAuthController::class, 'deleteAdmin']);

    // Admin can access all customer profiles
    Route::get('/customers', [CustomerAuthController::class, 'index']);
    Route::get('/customers/{id}', [CustomerAuthController::class, 'show']);

    // Admin Services Routes
    Route::apiResource('vehicle-types', VehicleTypeController::class);
    Route::apiResource('service-packages', ServicePackageController::class);
    Route::apiResource('pricing-entries', PricingEntryController::class);
});

// ─── admin services Routes ───────────────────────────────
