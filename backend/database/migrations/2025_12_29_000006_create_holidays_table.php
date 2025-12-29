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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();

            // Date in different calendar formats
            $table->string('jalali_date', 5)->nullable();    // Format: MM-DD (e.g., 01-01 for Nowruz)
            $table->string('gregorian_date', 5)->nullable();  // Format: MM-DD (for fixed Gregorian holidays)
            $table->string('hijri_date', 5)->nullable();      // Format: MM-DD (for Islamic holidays)

            // Holiday information
            $table->string('title');                          // Persian title
            $table->string('title_en');                       // English title
            $table->text('description')->nullable();

            // Classification
            $table->enum('type', ['national', 'religious', 'international', 'custom'])->default('national');
            $table->boolean('is_official_holiday')->default(true);
            $table->boolean('is_recurring')->default(true);   // Repeats every year

            // For non-recurring holidays
            $table->integer('year')->nullable();              // Specific year

            // Organization-specific holidays
            $table->foreignUuid('organization_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();

            // Index for efficient lookups
            $table->index(['jalali_date', 'is_recurring']);
            $table->index(['gregorian_date', 'is_recurring']);
            $table->index(['hijri_date', 'is_recurring']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
