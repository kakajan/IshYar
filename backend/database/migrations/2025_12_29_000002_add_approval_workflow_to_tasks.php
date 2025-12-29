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
        Schema::table('tasks', function (Blueprint $table) {
            // Add pending_review to status enum - we'll handle this via check constraint
            // since SQLite and some DBs don't support ALTER ENUM

            // Add new approval workflow columns
            $table->unsignedInteger('revision_count')->default(0)->after('approved_at');
            $table->json('revision_notes')->nullable()->after('revision_count');
            $table->timestamp('submitted_at')->nullable()->after('revision_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['revision_count', 'revision_notes', 'submitted_at']);
        });
    }
};
