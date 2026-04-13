<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ServicePackage;
use Tests\TestCase;

describe('Service Packages Management', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe('Add Service Package', function () {
        
        it('can add a service package with valid data', function () {
            // Arrange
            $payload = [
                'code' => 'P1',
                'name' => 'Package 1',
                'description' => 'Basic wash only',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Service package added.');
            $this->assertDatabaseHas('service_packages', [
                'code' => 'p1',
                'name' => 'Package 1',
                'description' => 'Basic wash only',
                'is_active' => true,
            ]);
        });

        it('can add a service package with lowercase code conversion', function () {
            // Arrange
            $payload = [
                'code' => 'P2',
                'name' => 'Package 2',
                'description' => 'Wash with wax',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'code' => 'p2',
            ]);
        });

        it('can add a service package with minimal data', function () {
            // Arrange
            $payload = [
                'code' => 'basic',
                'name' => 'Basic Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('service_packages', [
                'code' => 'basic',
                'name' => 'Basic Package',
                'description' => null,
                'is_active' => false,
            ]);
        });

        it('requires a code field', function () {
            // Arrange
            $payload = [
                'name' => 'Package Name',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('requires a name field', function () {
            // Arrange
            $payload = [
                'code' => 'pkg',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertSessionHasErrors('name');
        });

        it('requires unique code', function () {
            // Arrange
            ServicePackage::create(['code' => 'premium', 'name' => 'Premium']);
            
            $payload = [
                'code' => 'premium',
                'name' => 'Another Premium',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('validates code max length', function () {
            // Arrange
            $payload = [
                'code' => str_repeat('a', 21),
                'name' => 'Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('validates name max length', function () {
            // Arrange - test max length validation sa name
            $payload = [
                'code' => 'pkg',
                'name' => str_repeat('a', 256),
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $response->assertSessionHasErrors('name');
        });

        it('validates description max length', function () {
            // Arrange - test max length validation sa description
            $payload = [
                'code' => 'pkg',
                'name' => 'Package',
                'description' => str_repeat('a', 256),
            ];

            // Act - post with too long description
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - dapat may too long error sa description
            $response->assertSessionHasErrors('description');
        });

        it('trims whitespace from code', function () {
            // Arrange - test kung ma-trim ang whitespace from code
            $payload = [
                'code' => '  deluxe  ',
                'name' => 'Deluxe Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'code' => 'deluxe',
            ]);
        });

        it('handles inactive status correctly', function () {
            // Arrange - test kung default ai-inactive ang package
            $payload = [
                'code' => 'archived',
                'name' => 'Old Package',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert
            $this->assertDatabaseHas('service_packages', [
                'code' => 'archived',
                'is_active' => false,
            ]);
        });

        it('accepts sort_order parameter', function () {
            // Arrange sort_order parameter works
            $payload = [
                'code' => 'first',
                'name' => 'First Package',
                'sort_order' => 10,
            ];

            // Act - post with sort_order
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - sort_order should be saved
            $this->assertDatabaseHas('service_packages', [
                'code' => 'first',
                'sort_order' => 10,
            ]);
        });

        it('validates sort_order is integer', function () {
            // Arrange - test sort_order integer validation
            $payload = [
                'code' => 'pkg',
                'name' => 'Package',
                'sort_order' => 'not-an-integer',
            ];

            // Act - post with non-integer sort_order
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - should have sort_order error
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order min value', function () {
            // Arrange - test sort_order minimum value validation
            $payload = [
                'code' => 'pkg',
                'name' => 'Package',
                'sort_order' => -1,
            ];

            // Act - post with negative sort_order
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - should error on min value
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order max value', function () {
            // Arrange - test sort_order max value validation
            $payload = [
                'code' => 'pkg',
                'name' => 'Package',
                'sort_order' => 65536,
            ];

            // Act - post with too high sort_order
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - should error on max value
            $response->assertSessionHasErrors('sort_order');
        });

        it('allows sort_order at max boundary', function () {
            // Arrange max boundary sort_order works
            $payload = [
                'code' => 'maxpkg',
                'name' => 'Max Package',
                'sort_order' => 65535,
            ];

            // Act - post at max boundary
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.packages.store'), $payload);

            // Assert - should succeed and save max value
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('service_packages', [
                'code' => 'maxpkg',
                'sort_order' => 65535,
            ]);
        });
    });
});
