<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->string('level')->default('bronze'); // bronze, silver, gold
            $table->string('rule_auth')->default('everyone'); // everyone, users, having, nobody
            $table->timestamps();
        });

        Schema::create('gamification_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('state')->default('draft'); // draft, inprogress, done
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('period')->default('once'); // once, daily, weekly, monthly, yearly
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('reward_badge_id')->nullable()->constrained('gamification_badges')->onDelete('set null');
            $table->string('visibility_mode')->default('personal'); // personal, ranking
            $table->timestamps();
        });

        Schema::create('gamification_goal_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('computation_mode')->default('manually'); // manually, count, sum, python
            $table->string('display_mode')->default('progress'); // progress, boolean
            $table->string('condition')->default('higher'); // higher, lower
            $table->string('suffix')->nullable();
            $table->string('domain')->default('[]');
            $table->text('compute_code')->nullable();
            $table->timestamps();
        });

        Schema::create('gamification_challenge_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('gamification_challenges')->onDelete('cascade');
            $table->foreignId('definition_id')->constrained('gamification_goal_definitions')->onDelete('cascade');
            $table->integer('sequence')->default(1);
            $table->decimal('target_goal', 15, 2);
            $table->timestamps();
        });

        Schema::create('gamification_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('definition_id')->constrained('gamification_goal_definitions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('line_id')->nullable()->constrained('gamification_challenge_lines')->onDelete('cascade');
            $table->foreignId('challenge_id')->nullable()->constrained('gamification_challenges')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('target_goal', 15, 2);
            $table->decimal('current', 15, 2)->default(0);
            $table->decimal('completeness', 5, 2)->default(0);
            $table->string('state')->default('draft'); // draft, inprogress, reached, failed, canceled
            $table->timestamps();
        });

        Schema::create('gamification_badge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained('gamification_badges')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('challenge_id')->nullable()->constrained('gamification_challenges')->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('gamification_karma_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('karma_min')->default(1);
            $table->timestamps();
        });

        Schema::create('gamification_karma_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('old_value')->default(0);
            $table->integer('new_value')->default(0);
            $table->integer('gain')->default(0);
            $table->text('reason')->nullable();
            $table->timestamp('tracking_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_karma_trackings');
        Schema::dropIfExists('gamification_karma_ranks');
        Schema::dropIfExists('gamification_badge_assignments');
        Schema::dropIfExists('gamification_goals');
        Schema::dropIfExists('gamification_challenge_lines');
        Schema::dropIfExists('gamification_goal_definitions');
        Schema::dropIfExists('gamification_challenges');
        Schema::dropIfExists('gamification_badges');
    }
};
