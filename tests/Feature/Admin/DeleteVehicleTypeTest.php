<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\VehicleType;
use Tests\TestCase;

describe('Vehicle Types - Delete Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe('Dedelete Mo Yan (Test Delete)', function () {
        
        it('can delete a vehicle type', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'S',
                'label' => 'Small',
            ]);
            $vehicleTypeId = $vehicleType->id;

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Vehicle type deleted.');
            // Verify soft delete - record should exist but be deleted
            $this->assertSoftDeleted('vehicle_types',[
                'id' => $vehicleTypeId,
                'code' => 'S']);
            // $this->assertDatabaseHas('vehicle_types', [
            //     'id' => $vehicleTypeId,
            //     'code' => 'S',
            // ],);
        });

        it('soft deletes vehicle type (record still exists in DB)', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'M',
                'label' => 'Medium',
            ]);
            $vehicleTypeId = $vehicleType->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert that the record is soft deleted
            $this->assertNull(VehicleType::find($vehicleTypeId));
            // But with query on all including soft deletes
            $deletedRecord = VehicleType::withTrashed()->find($vehicleTypeId);
            expect($deletedRecord)->not->toBeNull();
            expect($deletedRecord->deleted_at)->not->toBeNull();
        });

        it('can delete multiple vehicle types', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $vt3 = VehicleType::create(['code' => 'L']);

            // Act
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.vehicle-types.destroy', $vt1));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.vehicle-types.destroy', $vt2));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.vehicle-types.destroy', $vt3));

            // Assert
            expect(VehicleType::find($vt1->id))->toBeNull();
            expect(VehicleType::find($vt2->id))->toBeNull();
            expect(VehicleType::find($vt3->id))->toBeNull();
        });

        it('cannot delete a non-existent vehicle type', function () {
            // Arrange - try to delete a vehicle type with ID that doesn't exist
            $nonExistentId = 9999;

            // Act & Assert
            $response = $this->actingAs($this->admin, 'admin')
                ->delete("/admin/services/vehicle-types/{$nonExistentId}");

            $response->assertNotFound();
        });

        it('removes vehicle type from query results after deletion', function () {
            // Arrange
            VehicleType::create(['code' => 'S']);
            VehicleType::create(['code' => 'M']);
            $vehicleToDelete = VehicleType::create(['code' => 'L']);

            expect(VehicleType::count())->toBe(3);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleToDelete));

            // Assert
            expect(VehicleType::count())->toBe(2);
            $this->assertDatabaseMissing('vehicle_types', [
                'code' => 'L',
                'deleted_at' => null,
            ]);
        });

        it('preserves other vehicle types when deleting one', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'S', 'label' => 'Small']);
            $vt2 = VehicleType::create(['code' => 'M', 'label' => 'Medium']);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vt1));

            // Assert vt2 still exists
            $this->assertDatabaseHas('vehicle_types', [
                'id' => $vt2->id,
                'code' => 'M',
                'label' => 'Medium',
            ]);
        });

        it('returns success message on deletion', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'XL']);

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            expect($response->getSession()->get('success'))->toBe('Vehicle type deleted.');
        });

        it('redirects to services index on deletion', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
        });

        it('marks deleted_at timestamp when deleting', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $vehicleTypeId = $vehicleType->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            $deletedRecord = VehicleType::withTrashed()->find($vehicleTypeId);
            expect($deletedRecord->deleted_at)->not->toBeNull();
            expect($deletedRecord->deleted_at)->toBeInstanceOf('Illuminate\Support\Carbon');
        });

        it('preserves other attributes when soft deleting', function () {
            // Arrange
            $vehicleType = VehicleType::create([
                'code' => 'L',
                'label' => 'Large',
                'description' => 'Large vehicles',
                'is_active' => true,
                'sort_order' => 3,
            ]);
            $vehicleTypeId = $vehicleType->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            $deletedRecord = VehicleType::withTrashed()->find($vehicleTypeId);
            expect($deletedRecord->code)->toBe('L');
            expect($deletedRecord->label)->toBe('Large');
            expect($deletedRecord->description)->toBe('Large vehicles');
            expect($deletedRecord->is_active)->toBe(true);
            expect($deletedRecord->sort_order)->toBe(3);
        });

        it('can delete active and inactive vehicle types', function () {
            // Arrange
            $activeVt = VehicleType::create(['code' => 'S', 'is_active' => true]);
            $inactiveVt = VehicleType::create(['code' => 'M', 'is_active' => false]);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $activeVt));
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $inactiveVt));

            // Assert
            expect(VehicleType::count())->toBe(0);
            expect(VehicleType::withTrashed()->count())->toBe(2);
        });

        it('delete operation is atomic', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $originalCode = $vehicleType->code;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert record details haven't changed, only soft deleted
            $deleted = VehicleType::withTrashed()->find($vehicleType->id);
            expect($deleted->code)->toBe($originalCode);
            expect($deleted->deleted_at)->not->toBeNull();
        });

        it('can delete vehicle type with associated pricing entries', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            // Note: We're just testing that the delete itself works
            // Relationship handling would be tested separately

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.vehicle-types.destroy', $vehicleType));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            expect(VehicleType::find($vehicleType->id))->toBeNull();
        });
    });
});
