<?php

namespace App\Facades;

use App\Services\JalaliDateService;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for JalaliDateService.
 *
 * @method static \App\ValueObjects\JalaliDate toJalali(\Carbon\Carbon|\DateTime|string $date)
 * @method static \Carbon\Carbon toGregorian(\App\ValueObjects\JalaliDate|string $date)
 * @method static \App\ValueObjects\JalaliDate parse(string $dateString)
 * @method static \App\ValueObjects\JalaliDate now()
 * @method static \App\ValueObjects\JalaliDate today()
 * @method static \App\ValueObjects\JalaliDate create(int $year, int $month = 1, int $day = 1, int $hour = 0, int $minute = 0, int $second = 0)
 * @method static string format(\Carbon\Carbon|\DateTime|string $date, string $pattern = 'Y/m/d', bool $persianNumerals = false)
 * @method static string formatPersian(\Carbon\Carbon|\DateTime|string $date, string $pattern = 'Y/m/d')
 * @method static bool isLeapYear(int $year)
 * @method static int getDaysInMonth(int $year, int $month)
 * @method static bool isValid(int $year, int $month, int $day)
 * @method static string relative(\Carbon\Carbon|\DateTime|string $date)
 * @method static array getMonthNames()
 * @method static array getDayNames()
 * @method static string toPersianNumerals(string $string)
 * @method static string toArabicNumerals(string $string)
 * @method static string formatRange(\Carbon\Carbon|\DateTime|string $start, \Carbon\Carbon|\DateTime|string $end, bool $persianNumerals = false)
 * @method static int calculateAge(\Carbon\Carbon|\DateTime|string $birthDate)
 * @method static string formatAge(\Carbon\Carbon|\DateTime|string $birthDate, bool $persianNumerals = false)
 * @method static string formatDuration(\Carbon\Carbon|\DateTime|string $start, \Carbon\Carbon|\DateTime|string $end, bool $persianNumerals = false)
 * @method static \Carbon\Carbon startOfMonth(\Carbon\Carbon|\DateTime|string $date)
 * @method static \Carbon\Carbon endOfMonth(\Carbon\Carbon|\DateTime|string $date)
 * @method static \Carbon\Carbon startOfYear(\Carbon\Carbon|\DateTime|string $date)
 * @method static \Carbon\Carbon endOfYear(\Carbon\Carbon|\DateTime|string $date)
 * @method static int currentYear()
 * @method static int currentMonth()
 *
 * @see \App\Services\JalaliDateService
 */
class Jalali extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return JalaliDateService::class;
    }
}
