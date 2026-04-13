<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\VehicleType;
use Tests\TestCase;

describe('Vehicle Types - Update Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe(' I Update ang Vehicle Type', function () {
        
        it('can update a vehicle type with valid data', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'S',
                'label' => 'Small',
                'description' => 'Old description',
                'is_active' => false,
            ]);

            $payload = [
                'code' => 'S',
                'label' => 'Small Vehicles',
                'description' => 'Sedans (all sedan types)',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Vehicle type updated.');
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'code' => 'S',
                'label' => 'Small Vehicles',
                'description' => 'Sedans (all sedan types)',
                'is_active' => true,
            ]);
        });

        it('can update code to same value', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'M',
            ]);

            $payload = [
                'code' => 'M',
                'label' => 'Medium',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'code' => 'M',
            ]);
        });

        it('can change code to different unique code', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            
            $payload = [
                'code' => 'XL',
                'label' => 'Extra Large',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'code' => 'XL',
            ]);
        });

        it('cannot change code to duplicate existing code', function () {
            // Arrange
            $vehicleType1 = VehicleType::create(['code' => 'S']);
            $vehicleType2 = VehicleType::create(['code' => 'M']);

            $payload = [
                'code' => 'S',
                'label' => 'Medium',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType2), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('can update code with uppercase conversion', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => 'l',
                'label' => 'Large',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'code' => 'L',
            ]);
        });

        it('requires code field', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'label' => 'Small',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('validates code max length', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => 'TOOLONG123456',
                'label' => 'Large',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('code');
        });

        it('can clear label by not providing it', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'M',
                'label' => 'Medium',
            ]);

            $payload = [
                'code' => 'M',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'label' => null,
            ]);
        });

        it('can clear description by not providing it', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'L',
                'description' => 'Large vehicles',
            ]);

            $payload = [
                'code' => 'L',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'description' => null,
            ]);
        });

        it('validates label max length', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => 'S',
                'label' => str_repeat('a', 256),
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('label');
        });

        it('validates description max length', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);

            $payload = [
                'code' => 'M',
                'description' => str_repeat('a', 256),
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('description');
        });

        it('can activate an inactive vehicle type', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'XL',
                'is_active' => false,
            ]);

            $payload = [
                'code' => 'XL',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'is_active' => true,
            ]);
        });

        it('can deactivate an active vehicle type', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'L',
                'is_active' => true,
            ]);

            $payload = [
                'code' => 'L',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'is_active' => false,
            ]);
        });

        it('trims whitespace from code', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => '  M  ',
                'label' => 'Medium',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'code' => 'M',
            ]);
        });

        it('accepts sort_order parameter', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => 'S',
                'sort_order' => 5,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'sort_order' => 5,
            ]);
        });

        it('validates sort_order is integer', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            $payload = [
                'code' => 'S',
                'sort_order' => 'not-integer',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order min value', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);

            $payload = [
                'code' => 'M',
                'sort_order' => -1,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('validates sort_order max value', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);

            $payload = [
                'code' => 'L',
                'sort_order' => 65536,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $response->assertSessionHasErrors('sort_order');
        });

        it('allows sort_order at max boundary', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'XL']);

            $payload = [
                'code' => 'XL',
                'sort_order' => 65535,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vehicleType->id,
                'sort_order' => 65535,
            ]);
        });

        it('preserves id of updated vehicle type', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $originalId = $vehicleType->id;

            $payload = [
                'code' => 'S',
                'label' => 'Updated Label',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.vehicle-types.update', $vehicleType), $payload);

            // Assert
            $updated = VehicleType::find($originalId);
            expect($updated)->not->toBeNull();
            expect($updated->label)->toBe('Updated Label');
        });
    });
});
