<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\VehicleType;
use Tests\TestCase;

describe('Pricing Entries - Delete Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe('Dedelete Mo Yan Pricing Entry (Test Delete)', function () {
        
        it('can delete a pricing entry', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);
            $pricingEntryId = $pricingEntry->id;

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Pricing entry deleted.');
            // Verify soft delete - record should exist but be deleted
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntryId,
                'vehicle_type_id' => $vehicleType->id,
            ], 'sqlite');
        });

        it('soft deletes pricing entry (record still exists in DB)', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);
            $pricingEntryId = $pricingEntry->id;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert
            $this->assertNull(PricingEntry::find($pricingEntryId));
            // But with query on all including soft deletes
            $deletedRecord = PricingEntry::withTrashed()->find($pricingEntryId);
            expect($deletedRecord)->not->toBeNull();
            expect($deletedRecord->deleted_at)->not->toBeNull();
        });

        it('can delete multiple pricing entries', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'L']);
            $vt2 = VehicleType::create(['code' => 'XL']);
            $vt3 = VehicleType::create(['code' => 'XXL']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            
            $pe1 = PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);
            $pe2 = PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);
            $pe3 = PricingEntry::create([
                'vehicle_type_id' => $vt3->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);

            // Act
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.pricing.destroy', $pe1));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.pricing.destroy', $pe2));
            $this->actingAs($this->admin, 'admin')->delete(route('admin.services.pricing.destroy', $pe3));

            // Assert
            expect(PricingEntry::find($pe1->id))->toBeNull();
            expect(PricingEntry::find($pe2->id))->toBeNull();
            expect(PricingEntry::find($pe3->id))->toBeNull();
        });

        it('cannot delete a non-existent pricing entry', function () {
            // Arrange - try to delete something na wala talaga sa database
            $nonExistentId = 9999;

            // Act & Assert
            $response = $this->actingAs($this->admin, 'admin')
                ->delete("/admin/services/pricing/{$nonExistentId}");

            $response->assertNotFound();
        });

        it('removes pricing entry from query results after deletion', function () {
            // Arrange tatlong pricing entries test
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $vt3 = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            
            PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);
            PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);
            $pricingToDelete = PricingEntry::create([
                'vehicle_type_id' => $vt3->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);

            expect(PricingEntry::count())->toBe(3);

            // Act - delete one entry then check count
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingToDelete));

            // Assert - after delete dapat 2 nalang ang count
            expect(PricingEntry::count())->toBe(2);
            $this->assertDatabaseMissing('pricing_entries', [
                'id' => $pricingToDelete->id,
                'deleted_at' => null,
            ]);
        });

        it('preserves other pricing entries when deleting one', function () {
            // Arrange dalawang pricing entries para ma-test ang preservation
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            
            $pe1 = PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);
            $pe2 = PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);

            // Act - delete lang ang first entry
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pe1));

            // Assert - ang second entry dapat intact pa rin
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pe2->id,
                'price_cents' => 20000,
            ]);
        });

        it('returns success message on deletion', function () {
            // Arrange - prepare ng pricing entry para ma-delete
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 15000,
            ]);

            // Act - send delete request
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert - check success message sa response
            expect($response->getSession()->get('success'))->toBe('Pricing entry deleted.');
        });

        it('redirects to services index on deletion', function () {
            // Arrange - prepare pricing entry to delete
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 25000,
            ]);

            // Act - delete ang pricing entry
            $response = $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert - dapat redirect sa services index
            $response->assertRedirectToRoute('admin.services.index');
        });

        it('marks deleted_at timestamp when deleting', function () {
            // Arrange - prepare pricing entry with timestamp checking
            $vehicleType = VehicleType::create(['code' => 'XL']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 35000,
            ]);
            $pricingEntryId = $pricingEntry->id;

            // Act - delete the entry
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert - check if deleted_at timestamp was set
            $deletedRecord = PricingEntry::withTrashed()->find($pricingEntryId);
            expect($deletedRecord->deleted_at)->not->toBeNull();
            expect($deletedRecord->deleted_at)->toBeInstanceOf('Illuminate\Support\Carbon');
        });

        it('preserves attributes when soft deleting', function () {
            // Arrange - create entry with specific attributes to preserve
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 12500,
                'is_active' => true,
            ]);
            $pricingEntryId = $pricingEntry->id;

            // Act - soft delete the entry
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert - all attributes should remain intact
            $deletedRecord = PricingEntry::withTrashed()->find($pricingEntryId);
            expect($deletedRecord->vehicle_type_id)->toBe($vehicleType->id);
            expect($deletedRecord->service_package_id)->toBe($package->id);
            expect($deletedRecord->price_cents)->toBe(12500);
            expect($deletedRecord->is_active)->toBe(true);
        });

        it('can delete active and inactive pricing entries', function () {
            // Arrange - create both active and inactive entries
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            
            $activePe = PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
                'is_active' => true,
            ]);
            $inactivePe = PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 22000,
                'is_active' => false,
            ]);

            // Act - delete both active and inactive entries
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $activePe));
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $inactivePe));

            // Assert - both should be deleted
            expect(PricingEntry::count())->toBe(0);
            expect(PricingEntry::withTrashed()->count())->toBe(2);
        });

        it('delete operation is atomic', function () {
            // Arrange - prepare entry atomic operation test
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);
            $originalPrice = $pricingEntry->price_cents;

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pricingEntry));

            // Assert record details haven't changed, only soft deleted
            $deleted = PricingEntry::withTrashed()->find($pricingEntry->id);
            expect($deleted->price_cents)->toBe($originalPrice);
            expect($deleted->deleted_at)->not->toBeNull();
        });

        it('soft deletes do not affect aggregate queries', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $vt3 = VehicleType::create(['code' => 'L']);
            $vt4 = VehicleType::create(['code' => 'XL']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            
            PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);
            PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 15000,
            ]);
            PricingEntry::create([
                'vehicle_type_id' => $vt3->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);
            $peToDelete = PricingEntry::create([
                'vehicle_type_id' => $vt4->id,
                'service_package_id' => $package->id,
                'price_cents' => 25000,
            ]);
            $peToDelete = PricingEntry::find(4);


            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $peToDelete));

            // Verify  - check results
            expect(PricingEntry::count())->toBe(3);
            expect(PricingEntry::withTrashed()->count())->toBe(4);
        });

        it('deletes pricing entry with various price values', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $vt3 = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            
            $prices = [
                PricingEntry::create([
                    'vehicle_type_id' => $vt1->id,
                    'service_package_id' => $package->id,
                    'price_cents' => 0,
                    'is_active' => false,
                ]),
                PricingEntry::create([
                    'vehicle_type_id' => $vt2->id,
                    'service_package_id' => $package->id,
                    'price_cents' => 50000,
                    'is_active' => true,
                ]),
                PricingEntry::create([
                    'vehicle_type_id' => $vt3->id,
                    'service_package_id' => $package->id,
                    'price_cents' => 99999999,
                    'is_active' => true,
                ]),
            ];

            // Act & Assert
            foreach ($prices as $pe) {
                $response = $this->actingAs($this->admin, 'admin')
                    ->delete(route('admin.services.pricing.destroy', $pe));
                
                $response->assertRedirectToRoute('admin.services.index');
            }

            expect(PricingEntry::count())->toBe(0);
        });

        it('can delete pricing entry and query by relationships after soft delete', function () {
            // Arrange
            $vt1 = VehicleType::create(['code' => 'S']);
            $vt2 = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            
            $pe1 = PricingEntry::create([
                'vehicle_type_id' => $vt1->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);
            $pe2 = PricingEntry::create([
                'vehicle_type_id' => $vt2->id,
                'service_package_id' => $package->id,
                'price_cents' => 25000,
            ]);

            // Act
            $this->actingAs($this->admin, 'admin')
                ->delete(route('admin.services.pricing.destroy', $pe1));

            // Assert - pe2 dapat queryable pa
            $remaining = PricingEntry::where('service_package_id', $package->id)->get();
            expect($remaining->count())->toBe(1);
            expect($remaining->first()->id)->toBe($pe2->id);
        });
    });
});
