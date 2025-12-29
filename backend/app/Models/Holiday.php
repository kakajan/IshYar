<?php

namespace App\Models;

use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'jalali_date',
        'gregorian_date',
        'hijri_date',
        'title',
        'title_en',
        'description',
        'type',
        'is_official_holiday',
        'is_recurring',
        'year',
        'organization_id',
    ];

    protected $casts = [
        'is_official_holiday' => 'boolean',
        'is_recurring' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the organization that owns the holiday.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope query to only include official holidays.
     */
    public function scopeOfficial($query)
    {
        return $query->where('is_official_holiday', true);
    }

    /**
     * Scope query to only include recurring holidays.
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Scope query to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get holidays for a specific Jalali year.
     */
    public static function getForYear(int $year, ?int $organizationId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::query()
            ->where(function ($q) use ($year) {
                // Recurring holidays
                $q->where('is_recurring', true)
                    // Or specific year holidays
                    ->orWhere('year', $year);
            });

        // Include organization-specific holidays
        if ($organizationId) {
            $query->where(function ($q) use ($organizationId) {
                $q->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationId);
            });
        } else {
            $query->whereNull('organization_id');
        }

        return $query->get();
    }

    /**
     * Get holidays for a specific Jalali month.
     */
    public static function getForMonth(int $year, int $month, ?int $organizationId = null): \Illuminate\Database\Eloquent\Collection
    {
        $monthStr = sprintf('%02d', $month);

        return static::getForYear($year, $organizationId)
            ->filter(function ($holiday) use ($monthStr) {
                if ($holiday->jalali_date) {
                    return str_starts_with($holiday->jalali_date, $monthStr);
                }
                return false;
            });
    }

    /**
     * Check if a specific Jalali date is a holiday.
     */
    public static function isHoliday(int $year, int $month, int $day, ?int $organizationId = null): bool
    {
        $dateStr = sprintf('%02d-%02d', $month, $day);

        return static::getForYear($year, $organizationId)
            ->contains(function ($holiday) use ($dateStr) {
                return $holiday->jalali_date === $dateStr;
            });
    }

    /**
     * Get holiday info for a specific Jalali date.
     */
    public static function getHolidayInfo(int $year, int $month, int $day, ?int $organizationId = null): ?self
    {
        $dateStr = sprintf('%02d-%02d', $month, $day);

        return static::getForYear($year, $organizationId)
            ->first(function ($holiday) use ($dateStr) {
                return $holiday->jalali_date === $dateStr;
            });
    }

    /**
     * Get the Gregorian date for this holiday in a specific year.
     */
    public function getGregorianDateForYear(int $jalaliYear): ?Carbon
    {
        if (!$this->jalali_date) {
            return null;
        }

        $parts = explode('-', $this->jalali_date);
        $month = (int) $parts[0];
        $day = (int) $parts[1];

        $jalali = JalaliDate::create($jalaliYear, $month, $day);
        return $jalali->toGregorian();
    }

    /**
     * Convert to array with full date information for a specific year.
     */
    public function toArrayWithYear(int $jalaliYear): array
    {
        $gregorian = $this->getGregorianDateForYear($jalaliYear);
        $parts = explode('-', $this->jalali_date ?? '01-01');

        return [
            'id' => $this->id,
            'date' => sprintf('%d/%02d/%02d', $jalaliYear, $parts[0], $parts[1]),
            'gregorian' => $gregorian?->format('Y-m-d'),
            'title' => $this->title,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'type' => $this->type,
            'is_holiday' => $this->is_official_holiday,
        ];
    }
}
