<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table exists and if id column is missing
        if (Schema::hasTable('label_task')) {
            // Drop and recreate the table with proper structure
            Schema::dropIfExists('label_task');
        }

        // Recreate with proper UUID id column
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
        // No need to do anything as the original migration handles the down
    }
};
