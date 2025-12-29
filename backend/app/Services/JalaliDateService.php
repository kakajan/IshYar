<?php

namespace App\Services;

use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * Service for Jalali date operations.
 */
class JalaliDateService
{
    /**
     * Convert Gregorian date to Jalali.
     */
    public function toJalali(Carbon|\DateTime|string $date): JalaliDate
    {
        return JalaliDate::fromGregorian($date);
    }

    /**
     * Convert Jalali date to Gregorian.
     */
    public function toGregorian(JalaliDate|string $date): Carbon
    {
        if (is_string($date)) {
            $date = JalaliDate::parse($date);
        }
        return $date->toGregorian();
    }

    /**
     * Parse a Jalali date string.
     */
    public function parse(string $dateString): JalaliDate
    {
        return JalaliDate::parse($dateString);
    }

    /**
     * Get current Jalali date.
     */
    public function now(): JalaliDate
    {
        return JalaliDate::now();
    }

    /**
     * Get today's Jalali date.
     */
    public function today(): JalaliDate
    {
        return JalaliDate::today();
    }

    /**
     * Create a Jalali date instance.
     */
    public function create(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): JalaliDate {
        return JalaliDate::create($year, $month, $day, $hour, $minute, $second);
    }

    /**
     * Format a date as Jalali.
     */
    public function format(
        Carbon|\DateTime|string $date,
        string $pattern = 'Y/m/d',
        bool $persianNumerals = false
    ): string {
        $jalali = $this->toJalali($date);
        return $jalali->format($pattern, $persianNumerals);
    }

    /**
     * Format with Persian numerals.
     */
    public function formatPersian(
        Carbon|\DateTime|string $date,
        string $pattern = 'Y/m/d'
    ): string {
        return $this->format($date, $pattern, true);
    }

    /**
     * Check if a Jalali year is a leap year.
     */
    public function isLeapYear(int $year): bool
    {
        return JalaliDate::isLeapJalaliYear($year);
    }

    /**
     * Get the number of days in a Jalali month.
     */
    public function getDaysInMonth(int $year, int $month): int
    {
        return JalaliDate::getDaysInMonth($year, $month);
    }

    /**
     * Validate a Jalali date.
     */
    public function isValid(int $year, int $month, int $day): bool
    {
        return JalaliDate::isValid($year, $month, $day);
    }

    /**
     * Get relative time in Persian.
     */
    public function relative(Carbon|\DateTime|string $date): string
    {
        return $this->toJalali($date)->toRelative();
    }

    /**
     * Get all month names.
     */
    public function getMonthNames(): array
    {
        return JalaliDate::MONTH_NAMES;
    }

    /**
     * Get all day names.
     */
    public function getDayNames(): array
    {
        return JalaliDate::DAY_NAMES;
    }

    /**
     * Convert numerals to Persian.
     */
    public function toPersianNumerals(string $string): string
    {
        return JalaliDate::toPersianNumerals($string);
    }

    /**
     * Convert Persian numerals to Arabic.
     */
    public function toArabicNumerals(string $string): string
    {
        return JalaliDate::toArabicNumerals($string);
    }

    /**
     * Format a date range.
     */
    public function formatRange(
        Carbon|\DateTime|string $start,
        Carbon|\DateTime|string $end,
        bool $persianNumerals = false
    ): string {
        $startJalali = $this->toJalali($start);
        $endJalali = $this->toJalali($end);

        // Same day
        if ($startJalali->eq($endJalali)) {
            return $startJalali->format('j F Y', $persianNumerals);
        }

        // Same month and year
        if ($startJalali->getYear() === $endJalali->getYear()
            && $startJalali->getMonth() === $endJalali->getMonth()) {
            return sprintf(
                '%s تا %s %s %s',
                $startJalali->format('j', $persianNumerals),
                $endJalali->format('j', $persianNumerals),
                $startJalali->getMonthName(),
                $startJalali->format('Y', $persianNumerals)
            );
        }

        // Same year
        if ($startJalali->getYear() === $endJalali->getYear()) {
            return sprintf(
                '%s %s تا %s %s %s',
                $startJalali->format('j', $persianNumerals),
                $startJalali->getMonthName(),
                $endJalali->format('j', $persianNumerals),
                $endJalali->getMonthName(),
                $startJalali->format('Y', $persianNumerals)
            );
        }

        // Different years
        return sprintf(
            '%s تا %s',
            $startJalali->format('j F Y', $persianNumerals),
            $endJalali->format('j F Y', $persianNumerals)
        );
    }

    /**
     * Calculate age in years from a birth date.
     */
    public function calculateAge(Carbon|\DateTime|string $birthDate): int
    {
        $birth = $this->toJalali($birthDate);
        $now = $this->now();

        $age = $now->getYear() - $birth->getYear();

        // Adjust if birthday hasn't occurred this year
        if ($now->getMonth() < $birth->getMonth()
            || ($now->getMonth() === $birth->getMonth() && $now->getDay() < $birth->getDay())) {
            $age--;
        }

        return max(0, $age);
    }

    /**
     * Format age string in Persian.
     */
    public function formatAge(Carbon|\DateTime|string $birthDate, bool $persianNumerals = false): string
    {
        $age = $this->calculateAge($birthDate);
        $ageStr = $persianNumerals ? $this->toPersianNumerals((string) $age) : (string) $age;
        return "سن: {$ageStr} سال";
    }

    /**
     * Format duration between two dates.
     */
    public function formatDuration(
        Carbon|\DateTime|string $start,
        Carbon|\DateTime|string $end,
        bool $persianNumerals = false
    ): string {
        $startDate = is_string($start) ? Carbon::parse($start) : Carbon::instance($start);
        $endDate = is_string($end) ? Carbon::parse($end) : Carbon::instance($end);

        $diff = $startDate->diff($endDate);

        $parts = [];

        if ($diff->y > 0) {
            $years = $persianNumerals ? $this->toPersianNumerals((string) $diff->y) : $diff->y;
            $parts[] = "{$years} سال";
        }

        if ($diff->m > 0) {
            $months = $persianNumerals ? $this->toPersianNumerals((string) $diff->m) : $diff->m;
            $parts[] = "{$months} ماه";
        }

        if ($diff->d > 0 && count($parts) < 2) {
            if ($diff->d >= 7 && $diff->d < 30) {
                $weeks = (int) floor($diff->d / 7);
                $remainingDays = $diff->d % 7;
                $weeksStr = $persianNumerals ? $this->toPersianNumerals((string) $weeks) : $weeks;
                $parts[] = "{$weeksStr} هفته";
                if ($remainingDays > 0 && count($parts) < 2) {
                    $daysStr = $persianNumerals ? $this->toPersianNumerals((string) $remainingDays) : $remainingDays;
                    $parts[] = "{$daysStr} روز";
                }
            } else {
                $days = $persianNumerals ? $this->toPersianNumerals((string) $diff->d) : $diff->d;
                $parts[] = "{$days} روز";
            }
        }

        if (empty($parts)) {
            return $persianNumerals ? '۰ روز' : '0 روز';
        }

        return implode(' و ', $parts);
    }

    /**
     * Get start of Jalali month for a Gregorian date.
     */
    public function startOfMonth(Carbon|\DateTime|string $date): Carbon
    {
        return $this->toJalali($date)->startOfMonth()->toGregorian();
    }

    /**
     * Get end of Jalali month for a Gregorian date.
     */
    public function endOfMonth(Carbon|\DateTime|string $date): Carbon
    {
        return $this->toJalali($date)->endOfMonth()->toGregorian();
    }

    /**
     * Get start of Jalali year for a Gregorian date.
     */
    public function startOfYear(Carbon|\DateTime|string $date): Carbon
    {
        return $this->toJalali($date)->startOfYear()->toGregorian();
    }

    /**
     * Get end of Jalali year for a Gregorian date.
     */
    public function endOfYear(Carbon|\DateTime|string $date): Carbon
    {
        return $this->toJalali($date)->endOfYear()->toGregorian();
    }

    /**
     * Get current Jalali year.
     */
    public function currentYear(): int
    {
        return $this->now()->getYear();
    }

    /**
     * Get current Jalali month.
     */
    public function currentMonth(): int
    {
        return $this->now()->getMonth();
    }
}
