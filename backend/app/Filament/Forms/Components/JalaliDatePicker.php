<?php

namespace App\Filament\Forms\Components;

use App\Facades\Jalali;
use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Support\RawJs;

class JalaliDatePicker extends DatePicker
{
    protected string $view = 'filament-forms::components.date-time-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->displayFormat('Y/m/d');

        // Format the date for display as Jalali
        $this->formatStateUsing(function ($state): ?string {
            if (! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            return Jalali::format($state, 'Y/m/d');
        });

        // Parse user input back to Gregorian
        $this->dehydrateStateUsing(function ($state): ?string {
            if (! $state) {
                return null;
            }

            // Check if it's already in Gregorian format
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $state)) {
                return $state;
            }

            // Parse as Jalali
            try {
                $jalali = JalaliDate::parse($state);
                return $jalali->toGregorian()->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Set Persian numerals display.
     */
    public function persianNumerals(bool $condition = true): static
    {
        $this->formatStateUsing(function ($state) use ($condition): ?string {
            if (! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            $formatted = Jalali::format($state, 'Y/m/d');

            if ($condition) {
                $formatted = JalaliDate::toPersianNumerals($formatted);
            }

            return $formatted;
        });

        return $this;
    }

    /**
     * Show full Jalali date with month name.
     */
    public function fullFormat(): static
    {
        $this->formatStateUsing(function ($state): ?string {
            if (! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            return Jalali::format($state, 'j F Y');
        });

        return $this;
    }

    /**
     * Set custom Jalali display format.
     */
    public function jalaliDisplayFormat(string $format): static
    {
        $this->formatStateUsing(function ($state) use ($format): ?string {
            if (! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            return Jalali::format($state, $format);
        });

        return $this;
    }
}
