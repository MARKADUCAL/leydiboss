<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ServicePackage;
use Tests\TestCase;

describe('Service Packages - Update Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe(' I Update ang service package (Tested na)', function () {
        
        it('can update a service package with valid data', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
                'description' => 'Old description',
                'is_active' => false,
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Basic Package',
                'description' => 'Wash only',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Service package updated.');
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'p1',
                'name' => 'Basic Package',
                'description' => 'Wash only',
                'is_active' => true,
            ]);
        });

        it('can update code to same value', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'deluxe',
                'name' => 'Deluxe',
            ]);

            $payload = [
                'code' => 'deluxe',
                'name' => 'Deluxe Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'deluxe',
            ]);
        });

        it('can change code to different unique code', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'old',
                'name' => 'Old Package',
            ]);
            
            $payload = [
                'code' => 'newcode',
                'name' => 'New Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'newcode',
            ]);
        });

        it('cannot change code to duplicate existing code', function () {
            // Arrange
            $package1 = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $package2 = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);

            $payload = [
                'code' => 'p1',
                'name' => 'Different Name',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package2), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('can update code with lowercase conversion', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'PREMIUM',
                'name' => 'Premium Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'premium',
            ]);
        });

        it('requires code field', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'name' => 'Updated Name',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('requires name field', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('name');
        });

        it('validates code max length', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => str_repeat('a', 21),
                'name' => 'Updated',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('validates name max length', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => str_repeat('a', 256),
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('name');
        });

        it('can clear description by not providing it', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
                'description' => 'Has description',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Updated Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'description' => null,
            ]);
        });

        it('validates description max length', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'description' => str_repeat('a', 256),
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('description');
        });

        it('can activate an inactive package', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
                'is_active' => false,
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'is_active' => true,
            ]);
        });

        it('can deactivate an active package', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
                'is_active' => true,
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'is_active' => false,
            ]);
        });

        it('trims whitespace from code', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => '  premium  ',
                'name' => 'Premium Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'premium',
            ]);
        });

        it('accepts sort_order parameter', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'sort_order' => 10,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'sort_order' => 10,
            ]);
        });

        it('validates sort_order is integer', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'sort_order' => 'not-integer',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order min value', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'sort_order' => -1,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order max value', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'sort_order' => 65536,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('allows sort_order at max boundary', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);

            $payload = [
                'code' => 'p1',
                'name' => 'Package 1',
                'sort_order' => 65535,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'sort_order' => 65535,
            ]);
        });

        it('preserves id of updated package', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'p1',
                'name' => 'Package 1',
            ]);
            $originalId = $package->id;

            $payload = [
                'code' => 'premium',
                'name' => 'Premium Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $updated = ServicePackage::find($originalId);
            expect($updated)->not->toBeNull();
            expect($updated->name)->toBe('Premium Package');
        });

        it('can update all fields at once', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'old',
                'name' => 'Old Package',
                'description' => 'Old description',
                'sort_order' => 0,
                'is_active' => false,
            ]);

            $payload = [
                'code' => 'NEW',
                'name' => 'New Package Name',
                'description' => 'New description',
                'sort_order' => 20,
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.packages.update', $package), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'id' => $package->id,
                'code' => 'new',
                'name' => 'New Package Name',
                'description' => 'New description',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        });
    });
});
