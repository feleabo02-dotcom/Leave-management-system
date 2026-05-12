<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->integer('sequence')->default(10);
            $table->string('color')->nullable();
            $table->boolean('is_certification')->default(false);
            $table->timestamps();
        });

        Schema::create('skill_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_type_id')->constrained('skill_types')->onDelete('cascade');
            $table->string('name');
            $table->integer('level_progress')->default(0);
            $table->boolean('default_level')->default(false);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sequence')->default(10);
            $table->foreignId('skill_type_id')->constrained('skill_types')->onDelete('cascade');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->foreignId('skill_level_id')->constrained('skill_levels')->onDelete('cascade');
            $table->foreignId('skill_type_id')->constrained('skill_types')->onDelete('cascade');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'skill_id', 'valid_from'], 'employee_skill_unique');
        });

        Schema::create('resume_line_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sequence')->default(10);
            $table->boolean('is_course')->default(false);
            $table->timestamps();
        });

        Schema::create('resume_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('line_type_id')->nullable()->constrained('resume_line_types')->onDelete('set null');
            $table->string('external_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_lines');
        Schema::dropIfExists('resume_line_types');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_levels');
        Schema::dropIfExists('skill_types');
    }
};
