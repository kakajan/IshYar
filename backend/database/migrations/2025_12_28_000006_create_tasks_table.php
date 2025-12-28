<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->foreignUuid('routine_template_id')->nullable()->constrained()->nullOnDelete();

            $table->json('title');
            $table->json('description')->nullable();
            $table->enum('type', ['routine', 'situational'])->default('situational');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            $table->foreignUuid('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->tinyInteger('progress')->default(0);

            $table->json('checklist')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();

            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable();

            $table->enum('approval_status', ['not_required', 'pending', 'approved', 'rejected'])->default('not_required');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('tasks')->nullOnDelete();

            $table->index(['organization_id', 'status']);
            $table->index(['assignee_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('due_date');
            $table->index('priority');
        });

        // Task dependencies pivot table
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('depends_on_id')->constrained('tasks')->cascadeOnDelete();
            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish'])->default('finish_to_start');
            $table->timestamps();

            $table->unique(['task_id', 'depends_on_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('tasks');
    }
};
