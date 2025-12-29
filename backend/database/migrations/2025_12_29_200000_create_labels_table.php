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
        // Labels table
        Schema::create('labels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('color', 7)->default('#6366f1'); // Hex color
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
            $table->index(['organization_id', 'sort_order']);
        });

        // Pivot table for many-to-many relationship
        Schema::create('label_task', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('label_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['label_id', 'task_id']);
            $table->index('task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_task');
        Schema::dropIfExists('labels');
    }
};
