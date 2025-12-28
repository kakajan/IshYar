<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TranslatableValue implements ValidationRule
{
    /**
     * @param array<int, string> $locales
     */
    public function __construct(
        private readonly array $locales = ['en', 'fa'],
        private readonly ?int $max = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value)) {
            if ($this->max !== null && mb_strlen($value) > $this->max) {
                $fail("The {$attribute} may not be greater than {$this->max} characters.");
            }

            return;
        }

        if (! is_array($value) || $value === []) {
            $fail("The {$attribute} must be a string or a translation object.");
            return;
        }

        foreach ($value as $locale => $translation) {
            if (! is_string($locale) || ! in_array($locale, $this->locales, true)) {
                $fail("The {$attribute} contains an unsupported locale.");
                return;
            }

            if (! is_string($translation) || $translation === '') {
                $fail("The {$attribute} translation for {$locale} must be a non-empty string.");
                return;
            }

            if ($this->max !== null && mb_strlen($translation) > $this->max) {
                $fail("The {$attribute} translation for {$locale} may not be greater than {$this->max} characters.");
                return;
            }
        }
    }
}
