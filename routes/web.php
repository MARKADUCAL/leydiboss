<?php

use Illuminate\Support\Facades\Route;

// ── Landing Controllers ──────────────────────────────────────────────────────
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\GalleryController;
use App\Http\Controllers\Landing\ServicesController;
use App\Http\Controllers\Landing\ContactController;

// ── Customer Auth Controllers ────────────────────────────────────────────────
use App\Http\Controllers\Customer\Auth\LoginController    as CustomerLoginController;
use App\Http\Controllers\Customer\Auth\RegisterController as CustomerRegisterController;

// ── Customer Page Controllers ────────────────────────────────────────────────
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\AppointmentController;
use App\Http\Controllers\Customer\TransactionController;
use App\Http\Controllers\Customer\ProfileController;

// ── Admin Auth Controllers ───────────────────────────────────────────────────
use App\Http\Controllers\Admin\Auth\LoginController    as AdminLoginController;
use App\Http\Controllers\Admin\Auth\RegisterController as AdminRegisterController;

// ── Admin Page Controllers ───────────────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController    as AdminDashboardController;
use App\Http\Controllers\Admin\AdminsController       as AdminAdminsController;
use App\Http\Controllers\Admin\CustomersController    as AdminCustomersController;
use App\Http\Controllers\Admin\ServicesManagementController;
use App\Http\Controllers\Admin\ProfileController      as AdminProfileController;
use App\Http\Controllers\Admin\CreateUserWalletTransactionsController;

// ============================================================
//  Landing Routes
// ============================================================
Route::name('landing.')->group(function () {
    Route::get('/',          [HomeController::class,     'index'])->name('index');
    Route::get('/services',  [ServicesController::class, 'index'])->name('services');
    Route::get('/gallery',   [GalleryController::class,  'index'])->name('gallery');
    Route::get('/contact',   [ContactController::class,  'index'])->name('contact');
    Route::post('/contact',  [ContactController::class,  'store'])->name('contact.store');
});

// Redirect /index → /
Route::get('/index', fn () => redirect()->route('landing.index'));

// ============================================================
//  Customer Routes
// ============================================================
Route::name('customer.')->prefix('customer')->group(function () {

    // ── Auth (guest only) ────────────────────────────────────
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login',     [CustomerLoginController::class,    'index'])->name('login');
        Route::post('/login',    [CustomerLoginController::class,    'login']);
        Route::get('/register',  [CustomerRegisterController::class, 'index'])->name('register');
        Route::post('/register', [CustomerRegisterController::class, 'store']);
    });

    Route::post('/logout', [CustomerLoginController::class, 'logout'])->name('logout');

    // ── Authenticated customer pages ─────────────────────────
    Route::middleware('customer.auth')->group(function () {
        Route::get('/index',        [DashboardController::class,   'index'])->name('index');
        Route::get('/appointment',  [AppointmentController::class, 'index'])->name('appointment.index');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/profile',      [ProfileController::class,     'index'])->name('profile.index');

        // Update customer profile
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

        // Saved Vehicles (Profile)
        Route::post('/profile/vehicles', [ProfileController::class, 'storeVehicle'])->name('profile.vehicles.store');
        Route::delete('/profile/vehicles/{vehicle}', [ProfileController::class, 'destroyVehicle'])->name('profile.vehicles.destroy');
    });
});

// ============================================================
//  Admin Routes
// ============================================================
Route::name('admin.')->prefix('admin')->group(function () {

    // ── Auth (guest only) ────────────────────────────────────
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login',     [AdminLoginController::class,    'index'])->name('login');
        Route::post('/login',    [AdminLoginController::class,    'login']);
        Route::get('/register',  [AdminRegisterController::class, 'index'])->name('register');
        Route::post('/register', [AdminRegisterController::class, 'store']);
    });

    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // ── Authenticated admin pages ────────────────────────────
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Admin Profile (all authenticated admins)
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');

        // Customers CRUD (admin + super_admin)
        Route::middleware('admin.role:admin,super_admin')->group(function () {
            Route::get(    '/customers',          [AdminCustomersController::class, 'index'  ])->name('customers.index');
            Route::post(   '/customers',          [AdminCustomersController::class, 'store'  ])->name('customers.store');
            Route::put(    '/customers/{customer}',[AdminCustomersController::class, 'update' ])->name('customers.update');
            Route::delete( '/customers/{customer}',[AdminCustomersController::class, 'destroy'])->name('customers.destroy');
        });

        // Admins CRUD (super_admin only)
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get(    '/admins',          [AdminAdminsController::class, 'index'  ])->name('admins.index');
            Route::post(   '/admins',          [AdminAdminsController::class, 'store'  ])->name('admins.store');
            Route::put(    '/admins/{admin}',  [AdminAdminsController::class, 'update' ])->name('admins.update');
            Route::delete( '/admins/{admin}',  [AdminAdminsController::class, 'destroy'])->name('admins.destroy');
        });

        // Wallet Transactions Tool (all authenticated admins)
        Route::get( '/wallet-transactions',                 [CreateUserWalletTransactionsController::class, 'index'         ])->name('wallet-transactions.index');
        Route::post('/wallet-transactions/generate',        [CreateUserWalletTransactionsController::class, 'generate'       ])->name('wallet-transactions.generate');
        Route::post('/wallet-transactions/update-balances', [CreateUserWalletTransactionsController::class, 'updateBalances' ])->name('wallet-transactions.update-balances');

        // Services & Pricing Management
        Route::get('/services', [ServicesManagementController::class, 'index'])->name('services.index');

        // Vehicle Types CRUD
        Route::post('/services/vehicle-types', [ServicesManagementController::class, 'storeVehicleType'])->name('services.vehicle-types.store');
        Route::put('/services/vehicle-types/{vehicleType}', [ServicesManagementController::class, 'updateVehicleType'])->name('services.vehicle-types.update');
        Route::delete('/services/vehicle-types/{vehicleType}', [ServicesManagementController::class, 'destroyVehicleType'])->name('services.vehicle-types.destroy');

        // Service Packages CRUD
        Route::post('/services/packages', [ServicesManagementController::class, 'storePackage'])->name('services.packages.store');
        Route::put('/services/packages/{package}', [ServicesManagementController::class, 'updatePackage'])->name('services.packages.update');
        Route::delete('/services/packages/{package}', [ServicesManagementController::class, 'destroyPackage'])->name('services.packages.destroy');

        // Pricing CRUD
        Route::post('/services/pricing', [ServicesManagementController::class, 'storePricing'])->name('services.pricing.store');
        Route::put('/services/pricing/{pricingEntry}', [ServicesManagementController::class, 'updatePricing'])->name('services.pricing.update');
        Route::delete('/services/pricing/{pricingEntry}', [ServicesManagementController::class, 'destroyPricing'])->name('services.pricing.destroy');
    });
});

// // ============================================================
// //  Demo Routes (Queue / Job Closure Demonstration)
// // ============================================================
// Route::get('/demo-queue', function () {
//     // Dispatching a job as a simple closure
//     dispatch(function () {
//         $startTime = now();
//         \Log::info("Starting closure job at {$startTime}");
        
//         // Simulating a long-running task (5 seconds) to demonstrate max execution time mechanics
//         sleep(5); 

//         $endTime = now();
//         \Log::info("Finished closure job at {$endTime}. Total time: " . $endTime->diffInSeconds($startTime) . " seconds.");
//     });

//     return response()->json([
//         'message' => 'Closure job dispatched successfully!',
//         'instructions' => [
//             'success_test' => 'Run `php artisan queue:work --timeout=10` (Allows the 5-second job to finish)',
//             'timeout_test' => 'Run `php artisan queue:work --timeout=3` (Forces the job to fail due to max execution time)'
//         ]
//     ]);
// });