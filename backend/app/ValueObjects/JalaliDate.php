<?php

namespace App\ValueObjects;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Hekmatinasser\Verta\Verta;

/**
 * Value Object representing a Jalali (Persian/Solar Hijri) date.
 */
class JalaliDate implements \JsonSerializable
{
    /**
     * Persian month names.
     */
    public const MONTH_NAMES = [
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];

    /**
     * English month names.
     */
    public const MONTH_NAMES_EN = [
        1 => 'Farvardin',
        2 => 'Ordibehesht',
        3 => 'Khordad',
        4 => 'Tir',
        5 => 'Mordad',
        6 => 'Shahrivar',
        7 => 'Mehr',
        8 => 'Aban',
        9 => 'Azar',
        10 => 'Dey',
        11 => 'Bahman',
        12 => 'Esfand',
    ];

    /**
     * Persian day names (0 = Saturday).
     */
    public const DAY_NAMES = [
        0 => 'شنبه',
        1 => 'یکشنبه',
        2 => 'دوشنبه',
        3 => 'سه‌شنبه',
        4 => 'چهارشنبه',
        5 => 'پنجشنبه',
        6 => 'جمعه',
    ];

    /**
     * Days in each month (normal year).
     */
    public const DAYS_IN_MONTH = [
        1 => 31, 2 => 31, 3 => 31, 4 => 31, 5 => 31, 6 => 31,
        7 => 30, 8 => 30, 9 => 30, 10 => 30, 11 => 30, 12 => 29,
    ];

    protected int $year;
    protected int $month;
    protected int $day;
    protected int $hour;
    protected int $minute;
    protected int $second;

    public function __construct(
        int $year,
        int $month,
        int $day,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }

    /**
     * Create from Gregorian date.
     */
    public static function fromGregorian(Carbon|\DateTime|string $date): self
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } elseif ($date instanceof \DateTime) {
            $date = Carbon::instance($date);
        }

        $jalalian = Jalalian::fromCarbon($date);

        return new self(
            $jalalian->getYear(),
            $jalalian->getMonth(),
            $jalalian->getDay(),
            $date->hour,
            $date->minute,
            $date->second
        );
    }

    /**
     * Create from Jalali string.
     */
    public static function parse(string $dateString): self
    {
        // Support formats: Y/m/d, Y-m-d, Y/m/d H:i:s
        $dateString = trim($dateString);

        // Convert Persian numerals to Arabic
        $dateString = self::toArabicNumerals($dateString);

        // Parse date and time
        $parts = preg_split('/[\s]+/', $dateString);
        $datePart = $parts[0];
        $timePart = $parts[1] ?? '00:00:00';

        // Parse date
        $dateComponents = preg_split('/[\/\-]/', $datePart);
        $year = (int) $dateComponents[0];
        $month = (int) ($dateComponents[1] ?? 1);
        $day = (int) ($dateComponents[2] ?? 1);

        // Parse time
        $timeComponents = explode(':', $timePart);
        $hour = (int) ($timeComponents[0] ?? 0);
        $minute = (int) ($timeComponents[1] ?? 0);
        $second = (int) ($timeComponents[2] ?? 0);

        return new self($year, $month, $day, $hour, $minute, $second);
    }

    /**
     * Create instance for now.
     */
    public static function now(): self
    {
        return self::fromGregorian(Carbon::now());
    }

    /**
     * Create instance for today.
     */
    public static function today(): self
    {
        return self::fromGregorian(Carbon::today());
    }

    /**
     * Create a new instance.
     */
    public static function create(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): self {
        return new self($year, $month, $day, $hour, $minute, $second);
    }

    // Getters
    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getHour(): int
    {
        return $this->hour;
    }

    public function getMinute(): int
    {
        return $this->minute;
    }

    public function getSecond(): int
    {
        return $this->second;
    }

    /**
     * Get the day of week (0 = Saturday, 6 = Friday).
     */
    public function getDayOfWeek(): int
    {
        $gregorian = $this->toGregorian();
        $dayOfWeek = $gregorian->dayOfWeek; // 0 = Sunday in Carbon

        // Convert to Jalali week (0 = Saturday)
        return ($dayOfWeek + 1) % 7;
    }

    /**
     * Get the day of year (1-366).
     */
    public function getDayOfYear(): int
    {
        $days = 0;
        for ($i = 1; $i < $this->month; $i++) {
            $days += $this->getDaysInMonth($this->year, $i);
        }
        return $days + $this->day;
    }

    /**
     * Get the week of year.
     */
    public function getWeekOfYear(): int
    {
        return (int) ceil($this->getDayOfYear() / 7);
    }

    /**
     * Check if it's a leap year.
     */
    public function isLeapYear(): bool
    {
        return self::isLeapJalaliYear($this->year);
    }

    /**
     * Get the Persian month name.
     */
    public function getMonthName(): string
    {
        return self::MONTH_NAMES[$this->month] ?? '';
    }

    /**
     * Get the English month name.
     */
    public function getMonthNameEn(): string
    {
        return self::MONTH_NAMES_EN[$this->month] ?? '';
    }

    /**
     * Get the Persian day name.
     */
    public function getDayName(): string
    {
        return self::DAY_NAMES[$this->getDayOfWeek()] ?? '';
    }

    /**
     * Convert to Gregorian Carbon instance.
     */
    public function toGregorian(): Carbon
    {
        $jalalian = Jalalian::fromFormat('Y/m/d H:i:s', sprintf(
            '%04d/%02d/%02d %02d:%02d:%02d',
            $this->year,
            $this->month,
            $this->day,
            $this->hour,
            $this->minute,
            $this->second
        ));

        return $jalalian->toCarbon();
    }

    /**
     * Convert to ISO 8601 string.
     */
    public function toISO(): string
    {
        return $this->toGregorian()->toIso8601String();
    }

    /**
     * Format the date.
     *
     * Supported format characters:
     * Y - 4-digit year (1403)
     * y - 2-digit year (03)
     * m - Month with leading zero (01-12)
     * n - Month without leading zero (1-12)
     * d - Day with leading zero (01-31)
     * j - Day without leading zero (1-31)
     * F - Full Persian month name
     * M - Short month name
     * l - Full Persian day name
     * D - Short day name
     * H - Hour with leading zero (00-23)
     * i - Minute with leading zero
     * s - Second with leading zero
     */
    public function format(string $pattern, bool $persianNumerals = false): string
    {
        $replacements = [
            'Y' => sprintf('%04d', $this->year),
            'y' => sprintf('%02d', $this->year % 100),
            'm' => sprintf('%02d', $this->month),
            'n' => (string) $this->month,
            'd' => sprintf('%02d', $this->day),
            'j' => (string) $this->day,
            'F' => $this->getMonthName(),
            'M' => mb_substr($this->getMonthName(), 0, 3),
            'l' => $this->getDayName(),
            'D' => mb_substr($this->getDayName(), 0, 1),
            'H' => sprintf('%02d', $this->hour),
            'i' => sprintf('%02d', $this->minute),
            's' => sprintf('%02d', $this->second),
        ];

        $result = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $pattern
        );

        if ($persianNumerals) {
            $result = self::toPersianNumerals($result);
        }

        return $result;
    }

    /**
     * Get relative time string in Persian.
     */
    public function toRelative(): string
    {
        $gregorian = $this->toGregorian();
        $now = Carbon::now();
        $diff = $gregorian->diff($now);
        $isPast = $gregorian->lt($now);

        if ($diff->days === 0) {
            return 'امروز';
        }
        if ($diff->days === 1) {
            return $isPast ? 'دیروز' : 'فردا';
        }
        if ($diff->days < 7) {
            $days = $diff->days;
            return $isPast ? "{$days} روز پیش" : "{$days} روز دیگر";
        }
        if ($diff->days < 30) {
            $weeks = (int) floor($diff->days / 7);
            return $isPast ? "{$weeks} هفته پیش" : "{$weeks} هفته دیگر";
        }
        if ($diff->days < 365) {
            $months = $diff->m > 0 ? $diff->m : 1;
            return $isPast ? "{$months} ماه پیش" : "{$months} ماه دیگر";
        }

        $years = $diff->y > 0 ? $diff->y : 1;
        return $isPast ? "{$years} سال پیش" : "{$years} سال دیگر";
    }

    /**
     * Check if the date is today.
     */
    public function isToday(): bool
    {
        $today = self::today();
        return $this->year === $today->year
            && $this->month === $today->month
            && $this->day === $today->day;
    }

    /**
     * Check if the date is in the past.
     */
    public function isPast(): bool
    {
        return $this->toGregorian()->lt(Carbon::now());
    }

    /**
     * Check if the date is in the future.
     */
    public function isFuture(): bool
    {
        return $this->toGregorian()->gt(Carbon::now());
    }

    /**
     * Add days.
     */
    public function addDays(int $days): self
    {
        return self::fromGregorian($this->toGregorian()->addDays($days));
    }

    /**
     * Subtract days.
     */
    public function subDays(int $days): self
    {
        return self::fromGregorian($this->toGregorian()->subDays($days));
    }

    /**
     * Add months.
     */
    public function addMonths(int $months): self
    {
        return self::fromGregorian($this->toGregorian()->addMonths($months));
    }

    /**
     * Add years.
     */
    public function addYears(int $years): self
    {
        return self::fromGregorian($this->toGregorian()->addYears($years));
    }

    /**
     * Get start of month.
     */
    public function startOfMonth(): self
    {
        return new self($this->year, $this->month, 1, 0, 0, 0);
    }

    /**
     * Get end of month.
     */
    public function endOfMonth(): self
    {
        $daysInMonth = $this->getDaysInMonth($this->year, $this->month);
        return new self($this->year, $this->month, $daysInMonth, 23, 59, 59);
    }

    /**
     * Get start of year.
     */
    public function startOfYear(): self
    {
        return new self($this->year, 1, 1, 0, 0, 0);
    }

    /**
     * Get end of year.
     */
    public function endOfYear(): self
    {
        $daysInLastMonth = $this->isLeapYear() ? 30 : 29;
        return new self($this->year, 12, $daysInLastMonth, 23, 59, 59);
    }

    /**
     * Get the number of days in a given Jalali month.
     */
    public static function getDaysInMonth(int $year, int $month): int
    {
        if ($month < 1 || $month > 12) {
            return 0;
        }
        if ($month === 12) {
            return self::isLeapJalaliYear($year) ? 30 : 29;
        }
        return self::DAYS_IN_MONTH[$month];
    }

    /**
     * Check if a Jalali year is a leap year.
     */
    public static function isLeapJalaliYear(int $year): bool
    {
        $leapYears = [1, 5, 9, 13, 17, 22, 26, 30];
        $cycle = $year % 33;
        return in_array($cycle, $leapYears);
    }

    /**
     * Validate a Jalali date.
     */
    public static function isValid(int $year, int $month, int $day): bool
    {
        if ($year < 1 || $month < 1 || $month > 12 || $day < 1) {
            return false;
        }

        $daysInMonth = self::getDaysInMonth($year, $month);
        return $day <= $daysInMonth;
    }

    /**
     * Convert Arabic numerals to Persian.
     */
    public static function toPersianNumerals(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabic, $persian, $string);
    }

    /**
     * Convert Persian numerals to Arabic.
     */
    public static function toArabicNumerals(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($persian, $arabic, $string);
    }

    /**
     * Compare with another JalaliDate.
     */
    public function eq(self $other): bool
    {
        return $this->year === $other->year
            && $this->month === $other->month
            && $this->day === $other->day;
    }

    public function gt(self $other): bool
    {
        return $this->toGregorian()->gt($other->toGregorian());
    }

    public function gte(self $other): bool
    {
        return $this->toGregorian()->gte($other->toGregorian());
    }

    public function lt(self $other): bool
    {
        return $this->toGregorian()->lt($other->toGregorian());
    }

    public function lte(self $other): bool
    {
        return $this->toGregorian()->lte($other->toGregorian());
    }

    /**
     * Get difference in days.
     */
    public function diffInDays(self $other): int
    {
        return $this->toGregorian()->diffInDays($other->toGregorian());
    }

    /**
     * Serialize to string.
     */
    public function __toString(): string
    {
        return $this->format('Y/m/d');
    }

    /**
     * JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'hour' => $this->hour,
            'minute' => $this->minute,
            'second' => $this->second,
            'formatted' => $this->format('Y/m/d'),
            'formatted_full' => $this->format('l، j F Y'),
            'gregorian' => $this->toISO(),
            'month_name' => $this->getMonthName(),
            'day_name' => $this->getDayName(),
            'is_leap_year' => $this->isLeapYear(),
            'day_of_week' => $this->getDayOfWeek(),
            'day_of_year' => $this->getDayOfYear(),
        ];
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['year'] ?? $data['Y'] ?? 1400,
            $data['month'] ?? $data['m'] ?? 1,
            $data['day'] ?? $data['d'] ?? 1,
            $data['hour'] ?? $data['H'] ?? 0,
            $data['minute'] ?? $data['i'] ?? 0,
            $data['second'] ?? $data['s'] ?? 0
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
