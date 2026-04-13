<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\VehicleType;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use Tests\TestCase;

describe('Vehicle Types Management', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe('Add Vehicle Type', function () {
        
        it('can add a vehicle type with valid data', function () {
            // Arrange
            $payload = [
                'code' => 'S',
                'label' => 'Small',
                'description' => 'Sedans (all sedan types)',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Vehicle type added.');
            $this->assertDatabaseHas('vehicle_types', [
                'code' => 'S',
                'label' => 'Small',
                'description' => 'Sedans (all sedan types)',
                'is_active' => true,
            ]);
        });

        it('can add a vehicle type with uppercase code conversion', function () {
            // Arrange
            $payload = [
                'code' => 's',
                'label' => 'Small',
                'description' => 'Sedans',
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert
            $this->assertDatabaseHas('vehicle_types', [
                'code' => 'S',
            ]);
        });

        it('can add a vehicle type with minimal data', function () {
            // Arrange
            $payload = [
                'code' => 'M',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('vehicle_types', [
                'code' => 'M',
                'label' => null,
                'description' => null,
                'is_active' => false,
            ]);
        });

        it('requires a code field', function () {
            // Arrange
            $payload = [
                'code' => '',
                'label' => 'Large',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert
            // $response->assertSessionHasErrors('code');
            $response->assertInvalid(['code'])->assertRedirectBack()->assertFound(); 
            
        });

        it('requires unique code', function () {
            // Arrange
            VehicleType::create(['code' => 'L']);
            
            $payload = [
                'code' => 'L',
                'label' => 'Large',
            ];

            // Act - try creating duplicate code
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - unique constraint error should trigger
            $response->assertSessionHasErrors('code');
        });

        it('validates code max length', function () {
            // Arrange - test max length validation sa code
            $payload = [
                'code' => 'TOOLONG123456',
            ];

            // Act - post with too long code
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - max length error should appear
            $response->assertSessionHasErrors('code');
        });

        it('validates label max length', function () {
            // Arrange - test max length validation sa label
            $payload = [
                'code' => 'XL',
                'label' => str_repeat('a', 256),
            ];

            // Act - post with too long label
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - label max length error
            $response->assertSessionHasErrors('label');
        });

        it('validates description max length', function () {
            // Arrange - test max length validation sa description
            $payload = [
                'code' => 'XL',
                'description' => str_repeat('a', 256),
            ];

            // Act - post with too long description
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - description length error
            $response->assertSessionHasErrors('description');
        });

        it('trims whitespace from code', function () {
            // Arrange whitespace gets trimmed from code
            $payload = [
                'code' => '  M  ',
                'label' => 'Medium',
            ];

            // Act - post code with spaces
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - spaces should be gone
            $this->assertDatabaseHas('vehicle_types', [
                'code' => 'M',
            ]);
        });

        it('handles inactive status correctly', function () {
            // Arrange - test kung default inactive ang vehicle type
            $payload = [
                'code' => 'INACTIVE',
            ];

            // Act - post without active status
            $response = $this->actingAs($this->admin, 'admin')
                ->post(route('admin.services.vehicle-types.store'), $payload);

            // Assert - should be inactive by default
            $this->assertDatabaseHas('vehicle_types', [
                'code' => 'INACTIVE',
                'is_active' => false,
            ]);
        });
    });
});
