<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\PricingEntry;
use App\Models\ServicePackage;
use App\Models\VehicleType;
use Tests\TestCase;

describe('Pricing Entries - Update Operations', function () {
    
    beforeEach(function () {
        // Create an authenticated admin user for each test
        $this->admin = Admin::factory()->create([
            'role' => Admin::ROLE_ADMIN,
        ]);
    });
    
    describe(' I Update ba? (Tested na)', function () {
        
        it('can update a pricing entry with valid price', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000, // ₱100.00
                'is_active' => false,
            ]);

            $payload = [
                'price' => 150.50,
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $response->assertSessionHas('success', 'Pricing updated.');
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 15050, // ₱150.50
                'is_active' => true,
            ]);
        });

        it('can update only price without changing is_active', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000, // ₱200.00
                'is_active' => true,
            ]);

            $payload = [
                'price' => 175.99,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 17599, // ₱175.99
                'is_active' => false, // Defaults to false when not provided
            ]);
        });

        it('correctly converts decimal price to cents', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 5000, // ₱50.00
            ]);

            $payload = [
                'price' => 99.99,
            ];

            // Act
            $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 9999, // ₱99.99
            ]);
        });

        it('handles rounding correctly', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);

            $payload = [
                'price' => 123.456, // Should round to ₱123.46
            ];

            // Act
            $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 12346, // ₱123.46
            ]);
        });

        it('requires price field', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);

            $payload = [];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertSessionHasErrors('price');
        });

        it('validates price is numeric', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);

            $payload = [
                'price' => 'not-a-number',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertSessionHasErrors('price');
        });

        it('validates price min value (0)', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);

            $payload = [
                'price' => -1,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertSessionHasErrors('price');
        });

        it('allows zero price', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);

            $payload = [
                'price' => 0,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 0,
            ]);
        });

        it('validates price max value (999999.99)', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);

            $payload = [
                'price' => 1000000,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertSessionHasErrors('price');
        });

        it('allows price at max boundary (999999.99)', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'XL']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);

            $payload = [
                'price' => 999999.99,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 99999999,
            ]);
        });

        it('can activate an inactive pricing entry', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
                'is_active' => false,
            ]);

            $payload = [
                'price' => 100,
                'is_active' => '1',
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'is_active' => true,
            ]);
        });

        it('can deactivate an active pricing entry', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
                'is_active' => true,
            ]);

            $payload = [
                'price' => 200,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'is_active' => false,
            ]);
        });

        it('preserves vehicle_type_id when updating', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 30000,
            ]);

            $payload = [
                'price' => 250.50,
            ];

            // Act
            $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'vehicle_type_id' => $vehicleType->id,
            ]);
        });

        it('preserves service_package_id when updating', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'XL']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 40000,
            ]);

            $payload = [
                'price' => 300.00,
            ];

            // Act
            $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'service_package_id' => $package->id,
            ]);
        });

        it('returns success message on update', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'S']);
            $package = ServicePackage::create(['code' => 'p1', 'name' => 'Package 1']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);

            $payload = [
                'price' => 125.00,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            expect($response->getSession()->get('success'))->toBe('Pricing updated.');
        });

        it('redirects to services index on update', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'M']);
            $package = ServicePackage::create(['code' => 'p2', 'name' => 'Package 2']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 20000,
            ]);

            $payload = [
                'price' => 150.00,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $response->assertRedirectToRoute('admin.services.index');
        });

        it('can update price with large number', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'L']);
            $package = ServicePackage::create(['code' => 'p3', 'name' => 'Package 3']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 10000,
            ]);

            $payload = [
                'price' => 99999.50,
            ];

            // Act
            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $this->assertDatabaseHas('pricing_entries', [
                'id' => $pricingEntry->id,
                'price_cents' => 9999950,
            ]);
        });

        it('preserves id when updating pricing entry', function () {
            // Arrange
            $vehicleType = VehicleType::create(['code' => 'XL']);
            $package = ServicePackage::create(['code' => 'p4', 'name' => 'Package 4']);
            $pricingEntry = PricingEntry::create([
                'vehicle_type_id' => $vehicleType->id,
                'service_package_id' => $package->id,
                'price_cents' => 40000,
            ]);
            $originalId = $pricingEntry->id;

            $payload = [
                'price' => 350.00,
                'is_active' => '1',
            ];

            // Act
            $this->actingAs($this->admin, 'admin')
                ->put(route('admin.services.pricing.update', $pricingEntry), $payload);

            // Assert
            $updated = PricingEntry::find($originalId);
            expect($updated)->not->toBeNull();
            expect($updated->price)->toBe(350.00);
        });
    });
});
