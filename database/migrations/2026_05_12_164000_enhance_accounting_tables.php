<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->string('type')->default('sales');
            $table->foreignId('account_id')->constrained('accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('account_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->foreignId('partner_id')->constrained('customers');
            $table->date('date');
            $table->date('due_date');
            $table->decimal('amount_total', 15, 2);
            $table->decimal('amount_tax', 15, 2)->default(0);
            $table->decimal('amount_untaxed', 15, 2);
            $table->string('status')->default('draft');
            $table->string('ref_number')->nullable();
            $table->foreignId('journal_id')->constrained('journals');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('account_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('account_invoices');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('method')->default('bank');
            $table->string('ref_number')->nullable();
            $table->foreignId('account_id')->constrained('accounts');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_payments');
        Schema::dropIfExists('account_invoices');
        Schema::dropIfExists('taxes');
    }
};
