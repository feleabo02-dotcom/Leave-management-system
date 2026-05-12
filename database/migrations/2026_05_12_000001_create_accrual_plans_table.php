<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accrual_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->enum('transition_mode', ['immediately', 'end_of_accrual'])->default('immediately');
            $table->enum('accrued_gain_time', ['start', 'end'])->default('end');
            $table->enum('carryover_date', ['year_start', 'allocation', 'other'])->default('year_start');
            $table->date('custom_carryover_date')->nullable();
            $table->boolean('is_based_on_worked_time')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('accrual_plan_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accrual_plan_id')->constrained('accrual_plans')->cascadeOnDelete();
            $table->integer('sequence');
            $table->string('name')->nullable();
            $table->decimal('added_value', 8, 2);
            $table->enum('added_value_type', ['days', 'hours'])->default('days');
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'biyearly', 'yearly'])->default('monthly');
            $table->integer('first_day')->nullable()->comment('e.g. 1 for 1st of month, 15 for 15th');
            $table->integer('first_month')->nullable()->comment('e.g. 1 for January');
            $table->boolean('cap_accrued_time')->default(false);
            $table->decimal('cap_accrued_time_amount', 8, 2)->nullable();
            $table->boolean('cap_accrued_time_yearly')->default(false);
            $table->decimal('cap_accrued_time_yearly_amount', 8, 2)->nullable();
            $table->enum('action_with_unused_accruals', ['lost', 'carry_over'])->default('lost');
            $table->enum('carryover_options', ['unlimited', 'limited'])->default('unlimited');
            $table->integer('carryover_limit_days')->nullable();
            $table->integer('accrual_validity_days')->nullable()->comment('Days after which carried-over accruals expire');
            $table->timestamps();
        });

        Schema::table('leave_allocations', function (Blueprint $table) {
            $table->enum('allocation_type', ['regular', 'accrual'])->default('regular');
            $table->foreignId('accrual_plan_id')->nullable()->constrained('accrual_plans')->nullOnDelete();
            $table->date('last_accrual_date')->nullable();
            $table->date('next_accrual_date')->nullable();
            $table->decimal('yearly_accrued_amount', 8, 2)->default(0);
            $table->decimal('expiring_carryover_days', 8, 2)->default(0);
            $table->date('carried_over_expiration')->nullable();
            $table->decimal('total_allocated_days', 8, 2)->default(0)->comment('Sum of all grants including carryover');
        });
    }

    public function down(): void
    {
        Schema::table('leave_allocations', function (Blueprint $table) {
            $table->dropColumn([
                'allocation_type',
                'accrual_plan_id',
                'last_accrual_date',
                'next_accrual_date',
                'yearly_accrued_amount',
                'expiring_carryover_days',
                'carried_over_expiration',
                'total_allocated_days',
            ]);
        });
        Schema::dropIfExists('accrual_plan_levels');
        Schema::dropIfExists('accrual_plans');
    }
};
