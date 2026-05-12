<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->integer('capacity')->default(1);
            $table->decimal('hourly_cost', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('routings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignId('bom_id')->nullable()->constrained('boms')->onDelete('set null');
            $table->decimal('lead_time', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('routing_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routing_id')->constrained('routings')->onDelete('cascade');
            $table->foreignId('work_center_id')->constrained('work_centers');
            $table->integer('sequence')->default(0);
            $table->string('name');
            $table->decimal('hours', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('scraps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_order_id')->constrained('manufacturing_orders');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraps');
        Schema::dropIfExists('routing_steps');
        Schema::dropIfExists('routings');
        Schema::dropIfExists('work_centers');
    }
};
