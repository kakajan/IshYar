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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignUuid('department_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->foreignUuid('position_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
            $table->uuid('manager_id')->nullable()->after('position_id');

            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('timezone')->default('UTC')->after('avatar');
            $table->string('locale')->default('en')->after('timezone');
            $table->boolean('is_active')->default(true)->after('locale');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->json('preferences')->nullable()->after('last_login_at');

            // Two-factor authentication
            $table->text('two_factor_secret')->nullable()->after('preferences');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');

            $table->softDeletes();

            $table->foreign('manager_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropForeign(['manager_id']);

            $table->dropColumn([
                'organization_id',
                'department_id',
                'position_id',
                'manager_id',
                'phone',
                'avatar',
                'timezone',
                'locale',
                'is_active',
                'last_login_at',
                'preferences',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'deleted_at',
            ]);
        });
    }
};
