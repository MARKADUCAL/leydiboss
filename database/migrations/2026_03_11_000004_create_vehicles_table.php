<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types');

            $table->string('nickname', 80); // e.g. mtb, layo
            $table->string('model', 120)->nullable();
            $table->string('plate_number', 40)->nullable();
            $table->string('color', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'vehicle_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

