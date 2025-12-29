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
        // Add phone verification fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->json('notification_preferences')->nullable()->after('preferences');
        });

        // Create phone verifications table for OTP tracking
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->string('code', 10);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['phone', 'code']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified_at', 'notification_preferences']);
        });
    }
};
