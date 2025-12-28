<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetRequestLocale
{
    /**
     * Handle an incoming request.
     *
     * Supported inputs (first match wins):
     * - Query: ?lang=fa | ?locale=fa
     * - Headers: X-Locale, X-Language, Accept-Language
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang') ?? $request->query('locale') ?? $request->header('X-Locale') ?? $request->header('X-Language') ?? $request->header('Accept-Language');

        if (is_string($locale) && $locale !== '') {
            // Normalize values like: fa-IR, en-US, "fa, en;q=0.9"
            $locale = strtolower(trim($locale));
            $locale = explode(',', $locale, 2)[0];
            $locale = explode(';', $locale, 2)[0];
            $locale = explode('-', $locale, 2)[0];

            if (in_array($locale, ['fa', 'en'], true)) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
