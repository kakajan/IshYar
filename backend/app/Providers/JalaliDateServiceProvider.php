<?php

namespace App\Providers;

use App\Services\JalaliDateService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class JalaliDateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the JalaliDateService as a singleton
        $this->app->singleton(JalaliDateService::class, function ($app) {
            return new JalaliDateService();
        });

        // Register alias
        $this->app->alias(JalaliDateService::class, 'jalali');

        // Merge config
        $this->mergeConfigFrom(
            config_path('jalali.php'),
            'jalali'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/jalali.php' => config_path('jalali.php'),
        ], 'jalali-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'jalali-migrations');

        // Publish translations
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'jalali');
        $this->publishes([
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/jalali'),
        ], 'jalali-translations');

        // Register validation rules
        $this->registerValidationRules();
    }

    /**
     * Register custom validation rules.
     */
    protected function registerValidationRules(): void
    {
        // jalali_date: Validates that the value is a valid Jalali date
        Validator::extend('jalali_date', function ($attribute, $value, $parameters, $validator) {
            return $this->validateJalaliDate($value);
        }, __('jalali::validation.jalali_date'));

        // jalali_after: Validates that the Jalali date is after another date
        Validator::extend('jalali_after', function ($attribute, $value, $parameters, $validator) {
            if (empty($parameters[0])) {
                return false;
            }

            $compareDate = $parameters[0];

            // Handle 'today' special value
            if ($compareDate === 'today') {
                $compareDate = app(JalaliDateService::class)->now()->format('Y/m/d');
            }

            // Get the compare value from another field if it exists
            $compareValue = $validator->getData()[$compareDate] ?? $compareDate;

            return $this->compareJalaliDates($value, $compareValue, '>');
        }, __('jalali::validation.jalali_after'));

        // jalali_before: Validates that the Jalali date is before another date
        Validator::extend('jalali_before', function ($attribute, $value, $parameters, $validator) {
            if (empty($parameters[0])) {
                return false;
            }

            $compareDate = $parameters[0];

            // Handle 'today' special value
            if ($compareDate === 'today') {
                $compareDate = app(JalaliDateService::class)->now()->format('Y/m/d');
            }

            // Get the compare value from another field if it exists
            $compareValue = $validator->getData()[$compareDate] ?? $compareDate;

            return $this->compareJalaliDates($value, $compareValue, '<');
        }, __('jalali::validation.jalali_before'));

        // jalali_between: Validates that the Jalali date is between two dates
        Validator::extend('jalali_between', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) < 2) {
                return false;
            }

            $startDate = $parameters[0];
            $endDate = $parameters[1];

            return $this->compareJalaliDates($value, $startDate, '>=')
                && $this->compareJalaliDates($value, $endDate, '<=');
        }, __('jalali::validation.jalali_between'));

        // Replace attribute names for better error messages
        Validator::replacer('jalali_after', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':date', $parameters[0] ?? '', $message);
        });

        Validator::replacer('jalali_before', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':date', $parameters[0] ?? '', $message);
        });

        Validator::replacer('jalali_between', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':start', ':end'], [$parameters[0] ?? '', $parameters[1] ?? ''], $message);
        });
    }

    /**
     * Validate that a value is a valid Jalali date.
     */
    protected function validateJalaliDate(string $value): bool
    {
        try {
            // Convert Persian numerals to Arabic
            $value = \App\ValueObjects\JalaliDate::toArabicNumerals($value);

            // Parse the date string
            $parts = preg_split('/[\/\-]/', $value);

            if (count($parts) < 3) {
                return false;
            }

            $year = (int) $parts[0];
            $month = (int) $parts[1];
            $day = (int) $parts[2];

            return \App\ValueObjects\JalaliDate::isValid($year, $month, $day);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Compare two Jalali dates.
     */
    protected function compareJalaliDates(string $date1, string $date2, string $operator): bool
    {
        try {
            $service = app(JalaliDateService::class);

            $jalali1 = $service->parse($date1);
            $jalali2 = $service->parse($date2);

            return match ($operator) {
                '>' => $jalali1->gt($jalali2),
                '>=' => $jalali1->gte($jalali2),
                '<' => $jalali1->lt($jalali2),
                '<=' => $jalali1->lte($jalali2),
                '==' => $jalali1->eq($jalali2),
                default => false,
            };
        } catch (\Exception $e) {
            return false;
        }
    }
}
