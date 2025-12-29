<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jalali Module Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the Jalali date module is enabled.
    | When disabled, all dates will be displayed in Gregorian format.
    |
    */
    'enabled' => env('JALALI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Calendar System
    |--------------------------------------------------------------------------
    |
    | This option determines the default calendar system to use.
    | Options: 'auto', 'jalali', 'gregorian'
    | 'auto' will use Jalali for Persian locale and Gregorian for others.
    |
    */
    'default_calendar' => env('JALALI_DEFAULT_CALENDAR', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Persian Numerals
    |--------------------------------------------------------------------------
    |
    | When enabled, dates will be displayed with Persian numerals (۰-۹)
    | instead of Arabic numerals (0-9).
    |
    */
    'persian_numerals' => env('JALALI_PERSIAN_NUMERALS', true),

    /*
    |--------------------------------------------------------------------------
    | First Day of Week
    |--------------------------------------------------------------------------
    |
    | The first day of the week for calendar displays.
    | Options: 'saturday', 'sunday', 'monday'
    |
    */
    'first_day_of_week' => env('JALALI_FIRST_DAY_OF_WEEK', 'saturday'),

    /*
    |--------------------------------------------------------------------------
    | Show Gregorian Alongside
    |--------------------------------------------------------------------------
    |
    | When enabled, Gregorian dates will be shown alongside Jalali dates.
    | Example: ۸ دی ۱۴۰۳ (28 Dec 2024)
    |
    */
    'show_gregorian_alongside' => env('JALALI_SHOW_GREGORIAN', false),

    /*
    |--------------------------------------------------------------------------
    | Holiday Highlighting
    |--------------------------------------------------------------------------
    |
    | When enabled, holidays will be highlighted in date pickers
    | and calendar displays.
    |
    */
    'holiday_highlight' => env('JALALI_HOLIDAY_HIGHLIGHT', true),

    /*
    |--------------------------------------------------------------------------
    | Date Formats
    |--------------------------------------------------------------------------
    |
    | Default format strings for different display contexts.
    |
    */
    'formats' => [
        'short' => 'Y/m/d',           // ۱۴۰۳/۱۰/۰۸
        'medium' => 'j F Y',           // ۸ دی ۱۴۰۳
        'long' => 'l، j F Y',          // شنبه، ۸ دی ۱۴۰۳
        'full' => 'l، j F Y H:i',      // شنبه، ۸ دی ۱۴۰۳ ۱۴:۳۰
        'time' => 'H:i',               // ۱۴:۳۰
        'datetime' => 'Y/m/d H:i',     // ۱۴۰۳/۱۰/۰۸ ۱۴:۳۰
    ],

    /*
    |--------------------------------------------------------------------------
    | Month Names
    |--------------------------------------------------------------------------
    |
    | Persian month names for reference.
    |
    */
    'months' => [
        1 => ['fa' => 'فروردین', 'en' => 'Farvardin', 'days' => 31],
        2 => ['fa' => 'اردیبهشت', 'en' => 'Ordibehesht', 'days' => 31],
        3 => ['fa' => 'خرداد', 'en' => 'Khordad', 'days' => 31],
        4 => ['fa' => 'تیر', 'en' => 'Tir', 'days' => 31],
        5 => ['fa' => 'مرداد', 'en' => 'Mordad', 'days' => 31],
        6 => ['fa' => 'شهریور', 'en' => 'Shahrivar', 'days' => 31],
        7 => ['fa' => 'مهر', 'en' => 'Mehr', 'days' => 30],
        8 => ['fa' => 'آبان', 'en' => 'Aban', 'days' => 30],
        9 => ['fa' => 'آذر', 'en' => 'Azar', 'days' => 30],
        10 => ['fa' => 'دی', 'en' => 'Dey', 'days' => 30],
        11 => ['fa' => 'بهمن', 'en' => 'Bahman', 'days' => 30],
        12 => ['fa' => 'اسفند', 'en' => 'Esfand', 'days' => 29], // 30 in leap year
    ],

    /*
    |--------------------------------------------------------------------------
    | Day Names
    |--------------------------------------------------------------------------
    |
    | Persian day names (0 = Saturday in Jalali weeks).
    |
    */
    'days' => [
        0 => ['fa' => 'شنبه', 'en' => 'Saturday', 'short' => 'ش'],
        1 => ['fa' => 'یکشنبه', 'en' => 'Sunday', 'short' => 'ی'],
        2 => ['fa' => 'دوشنبه', 'en' => 'Monday', 'short' => 'د'],
        3 => ['fa' => 'سه‌شنبه', 'en' => 'Tuesday', 'short' => 'س'],
        4 => ['fa' => 'چهارشنبه', 'en' => 'Wednesday', 'short' => 'چ'],
        5 => ['fa' => 'پنجشنبه', 'en' => 'Thursday', 'short' => 'پ'],
        6 => ['fa' => 'جمعه', 'en' => 'Friday', 'short' => 'ج'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Weekend Days
    |--------------------------------------------------------------------------
    |
    | Days that are considered weekend (0-indexed, Saturday = 0).
    | In Iran, Friday (6) is the weekend.
    |
    */
    'weekend' => [6], // Friday

    /*
    |--------------------------------------------------------------------------
    | Locales that use Jalali
    |--------------------------------------------------------------------------
    |
    | List of locales that should automatically use Jalali calendar
    | when 'default_calendar' is set to 'auto'.
    |
    */
    'jalali_locales' => ['fa', 'fa_IR', 'fa-IR'],
];
