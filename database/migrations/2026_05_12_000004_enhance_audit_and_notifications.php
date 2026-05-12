<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance existing audit_logs table if it exists, else create it
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('audit_logs', 'old_values')) {
                    $table->json('old_values')->nullable()->after('action');
                }
                if (!Schema::hasColumn('audit_logs', 'new_values')) {
                    $table->json('new_values')->nullable()->after('old_values');
                }
                if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('new_values');
                }
                if (!Schema::hasColumn('audit_logs', 'url')) {
                    $table->string('url', 2000)->nullable()->after('ip_address');
                }
                if (!Schema::hasColumn('audit_logs', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('url');
                }
            });
        } else {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('event');           // created, updated, deleted, login, approved, etc.
                $table->string('auditable_type')->nullable();
                $table->unsignedBigInteger('auditable_id')->nullable();
                $table->string('action')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('url', 2000)->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->timestamps();

                $table->index(['auditable_type', 'auditable_id']);
                $table->index('user_id');
            });
        }

        // In-app notifications
        Schema::create('erp_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('type')->default('info'); // info, warning, success, danger
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->morphs('notifiable'); // polymorphic: leave_requests, purchase_orders, etc.
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_notifications');
    }
};
