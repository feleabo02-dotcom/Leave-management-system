<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sequence')->default(0);
            $table->integer('probability')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('leader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->string('type')->default('opportunity')->after('id');
            $table->string('email')->nullable()->after('customer_id');
            $table->string('phone')->nullable()->after('email');
            $table->string('source')->nullable()->after('stage');
            $table->foreignId('team_id')->nullable()->after('assigned_to')->constrained('crm_teams')->onDelete('set null');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('website')->nullable()->after('phone');
            $table->string('vat')->nullable()->after('company');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['website', 'vat', 'notes']);
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn(['type', 'email', 'phone', 'source']);
        });

        Schema::dropIfExists('crm_teams');
        Schema::dropIfExists('crm_stages');
    }
};
