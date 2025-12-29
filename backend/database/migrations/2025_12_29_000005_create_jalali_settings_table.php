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
        Schema::create('jalali_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->boolean('enabled')->default(true);
            $table->enum('calendar_system', ['auto', 'jalali', 'gregorian'])->default('auto');
            $table->boolean('persian_numerals')->default(true);
            $table->enum('first_day_of_week', ['saturday', 'sunday', 'monday'])->default('saturday');
            $table->boolean('show_gregorian_alongside')->default(false);
            $table->boolean('holiday_highlight')->default(true);

            $table->timestamps();

            // Each user can have only one settings record
            $table->unique(['organization_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jalali_settings');
    }
};
