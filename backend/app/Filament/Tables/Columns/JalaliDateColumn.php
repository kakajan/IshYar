<?php

namespace App\Filament\Tables\Columns;

use App\Facades\Jalali;
use App\ValueObjects\JalaliDate;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;

class JalaliDateColumn extends TextColumn
{
    protected string $jalaliFormat = 'j F Y';
    protected bool $persianNumerals = true;
    protected bool $showRelative = false;
    protected bool $showGregorianInTooltip = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function ($state): ?string {
            if (! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            $formatted = Jalali::format($state, $this->jalaliFormat);

            if ($this->persianNumerals) {
                $formatted = JalaliDate::toPersianNumerals($formatted);
            }

            if ($this->showRelative) {
                $relative = Jalali::relative($state);
                if ($this->persianNumerals) {
                    $relative = JalaliDate::toPersianNumerals($relative);
                }
                $formatted .= " ({$relative})";
            }

            return $formatted;
        });

        // Add tooltip with Gregorian date if enabled
        $this->tooltip(function ($state): ?string {
            if (! $this->showGregorianInTooltip || ! $state) {
                return null;
            }

            if (is_string($state)) {
                $state = Carbon::parse($state);
            }

            return $state->format('Y-m-d');
        });
    }

    /**
     * Set the Jalali format string.
     */
    public function format(string $format): static
    {
        $this->jalaliFormat = $format;

        return $this;
    }

    /**
     * Enable/disable Persian numerals.
     */
    public function persianNumerals(bool $condition = true): static
    {
        $this->persianNumerals = $condition;

        return $this;
    }

    /**
     * Show relative time alongside the date.
     */
    public function relative(bool $condition = true): static
    {
        $this->showRelative = $condition;

        return $this;
    }

    /**
     * Show Gregorian date in tooltip.
     */
    public function showGregorian(bool $condition = true): static
    {
        $this->showGregorianInTooltip = $condition;

        return $this;
    }

    /**
     * Use short format (Y/m/d).
     */
    public function shortFormat(): static
    {
        $this->jalaliFormat = 'Y/m/d';

        return $this;
    }

    /**
     * Use medium format (j F Y).
     */
    public function mediumFormat(): static
    {
        $this->jalaliFormat = 'j F Y';

        return $this;
    }

    /**
     * Use long format (l، j F Y).
     */
    public function longFormat(): static
    {
        $this->jalaliFormat = 'l، j F Y';

        return $this;
    }

    /**
     * Use datetime format (Y/m/d H:i).
     */
    public function dateTimeFormat(): static
    {
        $this->jalaliFormat = 'Y/m/d H:i';

        return $this;
    }
}
