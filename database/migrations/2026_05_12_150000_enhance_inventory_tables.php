<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add tracking fields to products
        Schema::table('products', function (Blueprint $table) {
            $table->string('tracking')->default('none')->after('unit'); // none, lot, serial
            $table->boolean('use_expiration_date')->default(false)->after('tracking');
            $table->integer('expiration_time')->nullable()->after('use_expiration_date');
            $table->integer('best_before_time')->nullable()->after('expiration_time');
            $table->decimal('weight', 10, 2)->nullable()->after('best_before_time');
            $table->decimal('volume', 10, 2)->nullable()->after('weight');
        });

        // Lot/Serial numbers
        Schema::create('stock_lots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ref')->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('expiration_date')->nullable();
            $table->timestamp('best_before_date')->nullable();
            $table->timestamp('removal_date')->nullable();
            $table->timestamp('alert_date')->nullable();
            $table->timestamps();
        });

        // Add lot_id to stock_levels and stock_moves
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->foreignId('lot_id')->nullable()->after('product_id')->constrained('stock_lots')->onDelete('set null');
            $table->string('batch_code')->nullable()->after('lot_id');
        });

        Schema::table('stock_moves', function (Blueprint $table) {
            $table->foreignId('lot_id')->nullable()->after('product_id')->constrained('stock_lots')->onDelete('set null');
        });

        // Picking batches
        Schema::create('picking_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('state')->default('draft'); // draft, in_progress, done, cancelled
            $table->timestamp('scheduled_date')->nullable();
            $table->boolean('is_wave')->default(false);
            $table->timestamps();
        });

        // Add batch_id to purchase_orders (renamed from procurement-ish, use purchase_orders)
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('id')->constrained('picking_batches')->onDelete('set null');
        });

        // Landed Costs
        Schema::create('landed_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->decimal('amount_total', 15, 2)->default(0);
            $table->string('state')->default('draft'); // draft, done, cancelled
            $table->foreignId('journal_id')->nullable()->constrained('journals')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('landed_cost_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landed_cost_id')->constrained('landed_costs')->onDelete('cascade');
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('split_method')->default('equal'); // equal, by_quantity, by_weight, by_volume, by_value
            $table->timestamps();
        });

        Schema::create('landed_cost_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landed_cost_id')->constrained('landed_costs')->onDelete('cascade');
            $table->foreignId('stock_move_id')->constrained('stock_moves')->onDelete('cascade');
            $table->decimal('additional_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        // Purchase agreements / blanket orders
        Schema::create('purchase_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, active, closed, cancelled
            $table->text('terms')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('purchase_agreement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_agreement_id')->constrained('purchase_agreements')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->decimal('received_qty', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();
        });

        // Purchase requisitions
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, ordered, cancelled
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('required_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_requisition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained('purchase_requisitions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->decimal('received_qty', 15, 2)->default(0);
            $table->text('specifications')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_lines');
        Schema::dropIfExists('purchase_requisitions');
        Schema::dropIfExists('purchase_agreement_lines');
        Schema::dropIfExists('purchase_agreements');
        Schema::dropIfExists('landed_cost_moves');
        Schema::dropIfExists('landed_cost_lines');
        Schema::dropIfExists('landed_costs');

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });

        Schema::dropIfExists('picking_batches');
        Schema::table('stock_moves', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn('lot_id');
        });
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['lot_id', 'batch_code']);
        });
        Schema::dropIfExists('stock_lots');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tracking', 'use_expiration_date', 'expiration_time', 'best_before_time', 'weight', 'volume']);
        });
    }
};
