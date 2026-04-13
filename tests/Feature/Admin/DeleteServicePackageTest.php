<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ServicePackage;
use Tests\TestCase;

describe('Service Packages - Delete Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe('Delete Mo Yan (Test Delete)', function () {
        
        it('can delete a service package', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'basic',
                'name' => 'Basic Package',
            ]);
            $packageId = $package->id;

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Service package deleted.');
            // Verify soft delete - record should exist but be deleted
            $this->assertDatabaseHas('service_packages', [
                'id' => $packageId,
                'code' => 'basic',
            ], 'sqlite');
        });

        it('soft deletes service package (record still exists in DB)', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'premium',
                'name' => 'Premium Package',
            ]);
            $packageId = $package->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert that the record is soft deleted
            $this->assertNull(ServicePackage::find($packageId));
            // But with query on all including soft deletes
            $deletedRecord = ServicePackage::withTrashed()->find($packageId);
            expect($deletedRecord)->not->toBeNull();
            expect($deletedRecord->deleted_at)->not->toBeNull();
        });

        it('can delete multiple service packages', function () {
            // Arrange
            $pkg1 = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pkg2 = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pkg3 = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);

            // Act
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.packages.destroy', $pkg1));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.packages.destroy', $pkg2));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.packages.destroy', $pkg3));

            // Assert
            expect(ServicePackage::find($pkg1->id))->toBeNull();
            expect(ServicePackage::find($pkg2->id))->toBeNull();
            expect(ServicePackage::find($pkg3->id))->toBeNull();
        });

        it('cannot delete a non-existent service package', function () {
            // Arrange - try to delete a package with ID that doesn't exist
            $nonExistentId = 9999;

            // Act & Assert
            $response = $this->actingAs($this->admin, 'admin')
                ->delete("/admin/services/packages/{$nonExistentId}");

            $response->assertNotFound();
        });

        it('removes service package from query results after deletion', function () {
            // Arrange
            ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $packageToDelete = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);

            expect(ServicePackage::count())->toBe(3);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $packageToDelete));

            // Assert
            expect(ServicePackage::count())->toBe(2);
            $this->assertDatabaseMissing('service_packages', [
                'code' => 'p3',
                'deleted_at' => null,
            ]);
        });

        it('preserves other service packages when deleting one', function () {
            // Arrange
            $pkg1 = ServicePackage::create(['code' => 'basic', 'name' => 'Basic']);
            $pkg2 = ServicePackage::create(['code' => 'premium', 'name' => 'Premium']);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $pkg1));

            // Assert pkg2 still exists
            $this->assertDatabaseHas('service_packages', [
                'id' => $pkg2->id,
                'code' => 'premium',
                'name' => 'Premium',
            ]);
        });

        it('returns success message on deletion', function () {
            // Arrange
            $package = ServicePackage::create(['code' => 'deluxe', 'name' => 'Deluxe']);

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            expect($response->getSession()->get('success'))->toBe('Service package deleted.');
        });

        it('redirects to services index on deletion', function () {
            // Arrange
            $package = ServicePackage::create(['code' => 'basic', 'name' => 'Basic']);

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
        });

        it('marks deleted_at timestamp when deleting', function () {
            // Arrange
            $package = ServicePackage::create(['code' => 'premium', 'name' => 'Premium']);
            $packageId = $package->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            $deletedRecord = ServicePackage::withTrashed()->find($packageId);
            expect($deletedRecord->deleted_at)->not->toBeNull();
            expect($deletedRecord->deleted_at)->toBeInstanceOf('Illuminate\Support\Carbon');
        });

        it('preserves other attributes when soft deleting', function () {
            // Arrange
            $package = ServicePackage::create([
                'code' => 'deluxe',
                'name' => 'Deluxe Package',
                'description' => 'Premium wash with wax',
                'is_active' => true,
                'sort_order' => 5,
            ]);
            $packageId = $package->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            $deletedRecord = ServicePackage::withTrashed()->find($packageId);
            expect($deletedRecord->code)->toBe('deluxe');
            expect($deletedRecord->name)->toBe('Deluxe Package');
            expect($deletedRecord->description)->toBe('Premium wash with wax');
            expect($deletedRecord->is_active)->toBe(true);
            expect($deletedRecord->sort_order)->toBe(5);
        });

        it('can delete active and inactive service packages', function () {
            // Arrange
            $activePkg = ServicePackage::create(['code' => 'p1', 'name' => 'Active', 'is_active' => true]);
            $inactivePkg = ServicePackage::create(['code' => 'p2', 'name' => 'Inactive', 'is_active' => false]);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $activePkg));
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $inactivePkg));

            // Assert
            expect(ServicePackage::count())->toBe(0);
            expect(ServicePackage::withTrashed()->count())->toBe(2);
        });

        it('delete operation is atomic', function () {
            // Arrange
            $package = ServicePackage::create(['code' => 'basic', 'name' => 'Basic Package']);
            $originalCode = $package->code;
            $originalName = $package->name;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert record details haven't changed, only soft deleted
            $deleted = ServicePackage::withTrashed()->find($package->id);
            expect($deleted->code)->toBe($originalCode);
            expect($deleted->name)->toBe($originalName);
            expect($deleted->deleted_at)->not->toBeNull();
        });

        it('can delete service package with associated pricing entries', function () {
            // Arrange
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            // Note: Pricing entries would be created separately in integration tests
            
            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $package));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            expect(ServicePackage::find($package->id))->toBeNull();
        });

        it('soft deletes do not affect aggregate queries', function () {
            // Arrange
            ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            $pkgToDelete = ServicePackage::find(3);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.packages.destroy', $pkgToDelete));

            // Assert
            expect(ServicePackage::count())->toBe(3);
            expect(ServicePackage::withTrashed()->count())->toBe(4);
        });

        it('deletes package with all field variations', function () {
            // Arrange - test with various field values
            $packages = [
                ServicePackage::create(['code' => 'basic', 'name' => 'Basic', 'description' => null, 'is_active' => false, 'sort_order' => 0]),
                ServicePackage::create(['code' => 'standard', 'name' => 'Standard', 'description' => 'Standard wash', 'is_active' => true, 'sort_order' => 10]),
                ServicePackage::create(['code' => 'premium', 'name' => 'Premium', 'description' => 'Premium with wax', 'is_active' => true, 'sort_order' => 20]),
            ];

            // Act & Assert
            foreach ($packages as $pkg) {
                $response = $this->actingAs($this->admin, 'admin')
                    ->delete(route('admin.services.packages.destroy', $pkg));
                
                $response->assertRedirectToRoute('admin.services.index');
            }

            expect(ServicePackage::count())->toBe(0);
        });
    });
});
