<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_vehicle_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('fleet_vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('brand_id')->constrained('fleet_vehicle_brands')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('fleet_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('model_id')->constrained('fleet_vehicle_models');
            $table->string('license_plate')->unique();
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->default(0);
            $table->string('color')->nullable();
            $table->string('vin_number')->nullable();
            $table->integer('seats')->nullable();
            $table->string('status')->default('active');
            $table->decimal('current_odometer', 15, 2)->default(0);
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fleet_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('fleet_vehicles')->onDelete('cascade');
            $table->string('type');
            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('ref_number')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('cost_frequency')->default('monthly');
            $table->text('terms')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('fleet_service_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('fleet_vehicles')->onDelete('cascade');
            $table->string('type');
            $table->string('description');
            $table->date('date');
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('odometer', 15, 2)->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fleet_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('fleet_vehicles')->onDelete('cascade');
            $table->date('date');
            $table->decimal('liters', 10, 2);
            $table->decimal('cost', 15, 2);
            $table->decimal('odometer', 15, 2)->nullable();
            $table->string('fuel_type')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_fuel_logs');
        Schema::dropIfExists('fleet_service_logs');
        Schema::dropIfExists('fleet_contracts');
        Schema::dropIfExists('fleet_vehicles');
        Schema::dropIfExists('fleet_vehicle_models');
        Schema::dropIfExists('fleet_vehicle_brands');
    }
};
