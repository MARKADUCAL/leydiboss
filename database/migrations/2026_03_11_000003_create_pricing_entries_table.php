<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');
            $table->foreignId('service_package_id')->constrained('service_packages');
            $table->unsignedInteger('price_cents'); // store money as cents
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['vehicle_type_id', 'service_package_id'], 'pricing_entries_vehicle_package_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_entries');
    }
};

