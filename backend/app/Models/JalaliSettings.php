<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JalaliSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'enabled',
        'calendar_system',
        'persian_numerals',
        'first_day_of_week',
        'show_gregorian_alongside',
        'holiday_highlight',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'persian_numerals' => 'boolean',
        'show_gregorian_alongside' => 'boolean',
        'holiday_highlight' => 'boolean',
    ];

    /**
     * Get the organization that owns the settings.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get effective settings for a user.
     * User settings override organization settings.
     */
    public static function getEffectiveSettings(?string $userId = null, ?string $organizationId = null): array
    {
        $defaults = [
            'enabled' => config('jalali.enabled', true),
            'calendar_system' => config('jalali.default_calendar', 'auto'),
            'persian_numerals' => config('jalali.persian_numerals', true),
            'first_day_of_week' => config('jalali.first_day_of_week', 'saturday'),
            'show_gregorian_alongside' => config('jalali.show_gregorian_alongside', false),
            'holiday_highlight' => config('jalali.holiday_highlight', true),
        ];

        // Get organization settings
        if ($organizationId) {
            $orgSettings = static::where('organization_id', $organizationId)
                ->whereNull('user_id')
                ->first();

            if ($orgSettings) {
                $defaults = array_merge($defaults, $orgSettings->only([
                    'enabled',
                    'calendar_system',
                    'persian_numerals',
                    'first_day_of_week',
                    'show_gregorian_alongside',
                    'holiday_highlight',
                ]));
            }
        }

        // Get user settings (override organization)
        if ($userId) {
            $userSettings = static::where('user_id', $userId)->first();

            if ($userSettings) {
                $defaults = array_merge($defaults, $userSettings->only([
                    'enabled',
                    'calendar_system',
                    'persian_numerals',
                    'first_day_of_week',
                    'show_gregorian_alongside',
                    'holiday_highlight',
                ]));
            }
        }

        return $defaults;
    }

    /**
     * Update or create settings for a user.
     */
    public static function updateUserSettings(string $userId, array $settings): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId],
            $settings
        );
    }

    /**
     * Update or create settings for an organization.
     */
    public static function updateOrganizationSettings(string $organizationId, array $settings): self
    {
        return static::updateOrCreate(
            ['organization_id' => $organizationId, 'user_id' => null],
            $settings
        );
    }
}
