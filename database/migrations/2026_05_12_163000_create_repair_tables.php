<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sequence')->default(0);
            $table->timestamps();
        });

        Schema::create('repair_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('diagnosis')->nullable();
            $table->string('status')->default('draft');
            $table->string('priority')->default('normal');
            $table->date('date_requested');
            $table->date('date_scheduled')->nullable();
            $table->date('date_completed')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('repair_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_order_id')->constrained('repair_orders')->onDelete('cascade');
            $table->string('description');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->string('type')->default('part');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_lines');
        Schema::dropIfExists('repair_orders');
        Schema::dropIfExists('repair_stages');
    }
};
