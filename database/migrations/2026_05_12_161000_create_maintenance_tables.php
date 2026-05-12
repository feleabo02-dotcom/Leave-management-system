<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('maintenance_equipment_categories')->onDelete('set null');
            $table->string('code')->nullable()->unique();
            $table->foreignId('location_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('technician_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('status')->default('operating');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('equipment_id')->constrained('maintenance_equipment')->onDelete('cascade');
            $table->text('description');
            $table->foreignId('requested_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('priority')->default('normal');
            $table->string('stage')->default('new');
            $table->string('category')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('equipment_id')->constrained('maintenance_equipment')->onDelete('cascade');
            $table->string('interval_type')->default('monthly');
            $table->integer('interval_count')->default(1);
            $table->date('planned_date')->nullable();
            $table->date('last_executed')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('maintenance_equipment');
        Schema::dropIfExists('maintenance_equipment_categories');
    }
};
