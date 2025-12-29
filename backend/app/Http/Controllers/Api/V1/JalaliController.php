<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\JalaliSettings;
use App\Services\JalaliDateService;
use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JalaliController extends Controller
{
    public function __construct(
        protected JalaliDateService $jalaliService
    ) {}

    /**
     * Get Jalali settings for current user/organization.
     *
     * GET /api/v1/jalali/settings
     */
    public function settings(Request $request): JsonResponse
    {
        $user = Auth::user();

        $settings = JalaliSettings::getEffectiveSettings(
            $user->id,
            $user->organization_id
        );

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Update Jalali settings.
     *
     * PATCH /api/v1/jalali/settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'sometimes|boolean',
            'calendar_system' => ['sometimes', Rule::in(['auto', 'jalali', 'gregorian'])],
            'persian_numerals' => 'sometimes|boolean',
            'first_day_of_week' => ['sometimes', Rule::in(['saturday', 'sunday', 'monday'])],
            'show_gregorian_alongside' => 'sometimes|boolean',
            'holiday_highlight' => 'sometimes|boolean',
            'scope' => ['sometimes', Rule::in(['user', 'organization'])],
        ]);

        $user = Auth::user();
        $scope = $validated['scope'] ?? 'user';
        unset($validated['scope']);

        if ($scope === 'organization' && $user->organization_id) {
            // Check if user has permission to update organization settings
            // For now, we'll allow it
            $settings = JalaliSettings::updateOrganizationSettings(
                $user->organization_id,
                $validated
            );
        } else {
            $settings = JalaliSettings::updateUserSettings($user->id, $validated);
        }

        return response()->json([
            'data' => JalaliSettings::getEffectiveSettings($user->id, $user->organization_id),
            'message' => __('jalali::settings.updated'),
        ]);
    }

    /**
     * Convert date between calendars.
     *
     * GET /api/v1/jalali/convert
     */
    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'from' => ['required', Rule::in(['jalali', 'gregorian'])],
            'to' => ['required', Rule::in(['jalali', 'gregorian'])],
            'format' => 'nullable|string',
        ]);

        $date = $validated['date'];
        $from = $validated['from'];
        $to = $validated['to'];
        $format = $validated['format'] ?? 'Y/m/d';

        try {
            if ($from === 'jalali') {
                $jalaliDate = $this->jalaliService->parse($date);
                $gregorianDate = $jalaliDate->toGregorian();
            } else {
                $gregorianDate = Carbon::parse($date);
                $jalaliDate = $this->jalaliService->toJalali($gregorianDate);
            }

            $output = $to === 'jalali'
                ? $jalaliDate->format($format)
                : $gregorianDate->format($format === 'Y/m/d' ? 'Y-m-d' : $format);

            return response()->json([
                'data' => [
                    'input' => $date,
                    'input_calendar' => $from,
                    'output' => $output,
                    'output_calendar' => $to,
                    'formatted' => $jalaliDate->format('j F Y'),
                    'components' => [
                        'year' => $jalaliDate->getYear(),
                        'month' => $jalaliDate->getMonth(),
                        'day' => $jalaliDate->getDay(),
                        'day_of_week' => $jalaliDate->getDayOfWeek(),
                        'day_name' => $jalaliDate->getDayName(),
                        'month_name' => $jalaliDate->getMonthName(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid date format',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get holidays for a year.
     *
     * GET /api/v1/jalali/holidays
     */
    public function holidays(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:1300|max:1500',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $user = Auth::user();
        $year = $validated['year'] ?? $this->jalaliService->currentYear();
        $month = $validated['month'] ?? null;

        if ($month) {
            $holidays = Holiday::getForMonth($year, $month, $user->organization_id);
        } else {
            $holidays = Holiday::getForYear($year, $user->organization_id);
        }

        $data = $holidays->map(function (Holiday $holiday) use ($year) {
            return $holiday->toArrayWithYear($year);
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'year' => $year,
                'month' => $month,
                'count' => $data->count(),
            ],
        ]);
    }

    /**
     * Format a date in Jalali.
     *
     * GET /api/v1/jalali/format
     */
    public function format(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'format' => ['nullable', Rule::in(['short', 'medium', 'long', 'full', 'custom'])],
            'custom_format' => 'nullable|string',
            'relative' => 'nullable|boolean',
            'persian_numerals' => 'nullable|boolean',
        ]);

        $date = $validated['date'];
        $formatType = $validated['format'] ?? 'medium';
        $customFormat = $validated['custom_format'] ?? null;
        $relative = $validated['relative'] ?? false;
        $persianNumerals = $validated['persian_numerals'] ?? true;

        try {
            // Try to parse as Jalali first, then as Gregorian
            try {
                $jalaliDate = $this->jalaliService->parse($date);
            } catch (\Exception $e) {
                $jalaliDate = $this->jalaliService->toJalali(Carbon::parse($date));
            }

            // Determine format pattern
            $pattern = match ($formatType) {
                'short' => config('jalali.formats.short', 'Y/m/d'),
                'medium' => config('jalali.formats.medium', 'j F Y'),
                'long' => config('jalali.formats.long', 'l، j F Y'),
                'full' => config('jalali.formats.full', 'l، j F Y H:i'),
                'custom' => $customFormat ?? 'Y/m/d',
                default => 'j F Y',
            };

            $formatted = $jalaliDate->format($pattern, $persianNumerals);
            $relativeText = $relative ? $jalaliDate->toRelative() : null;

            if ($persianNumerals && $relativeText) {
                $relativeText = JalaliDate::toPersianNumerals($relativeText);
            }

            return response()->json([
                'data' => [
                    'formatted' => $formatted,
                    'relative' => $relativeText,
                    'iso' => $jalaliDate->toISO(),
                    'jalali' => $jalaliDate->format('Y/m/d'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid date format',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get today's date information.
     *
     * GET /api/v1/jalali/today
     */
    public function today(Request $request): JsonResponse
    {
        $persianNumerals = $request->boolean('persian_numerals', true);

        $now = $this->jalaliService->now();

        return response()->json([
            'data' => [
                'date' => $now->format('Y/m/d', $persianNumerals),
                'formatted' => $now->format('l، j F Y', $persianNumerals),
                'year' => $now->getYear(),
                'month' => $now->getMonth(),
                'day' => $now->getDay(),
                'month_name' => $now->getMonthName(),
                'day_name' => $now->getDayName(),
                'is_leap_year' => $now->isLeapYear(),
                'gregorian' => $now->toGregorian()->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Get calendar data for a month.
     *
     * GET /api/v1/jalali/calendar
     */
    public function calendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:1300|max:1500',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $user = Auth::user();
        $year = $validated['year'] ?? $this->jalaliService->currentYear();
        $month = $validated['month'] ?? $this->jalaliService->currentMonth();

        $daysInMonth = JalaliDate::getDaysInMonth($year, $month);
        $firstDayOfMonth = JalaliDate::create($year, $month, 1);
        $firstDayOfWeek = $firstDayOfMonth->getDayOfWeek();

        // Get holidays for this month
        $holidays = Holiday::getForMonth($year, $month, $user->organization_id);
        $holidayDays = $holidays->pluck('jalali_date')
            ->map(fn($d) => (int) explode('-', $d)[1])
            ->toArray();

        $today = $this->jalaliService->today();
        $isCurrentMonth = $today->getYear() === $year && $today->getMonth() === $month;

        // Build calendar grid
        $days = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = JalaliDate::create($year, $month, $day);
            $days[] = [
                'day' => $day,
                'day_of_week' => $date->getDayOfWeek(),
                'is_today' => $isCurrentMonth && $day === $today->getDay(),
                'is_holiday' => in_array($day, $holidayDays),
                'is_weekend' => in_array($date->getDayOfWeek(), config('jalali.weekend', [6])),
                'gregorian' => $date->toGregorian()->format('Y-m-d'),
            ];
        }

        return response()->json([
            'data' => [
                'year' => $year,
                'month' => $month,
                'month_name' => JalaliDate::MONTH_NAMES[$month],
                'days_in_month' => $daysInMonth,
                'first_day_of_week' => $firstDayOfWeek,
                'is_leap_year' => JalaliDate::isLeapJalaliYear($year),
                'days' => $days,
                'holidays' => $holidays->map(fn($h) => $h->toArrayWithYear($year))->values(),
            ],
        ]);
    }
}
