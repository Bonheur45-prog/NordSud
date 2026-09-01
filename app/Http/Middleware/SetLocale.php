<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['fr', 'en'];

    public const DEFAULT = 'fr'; // client's stated default language

    /**
     * Routes are prefixed {locale}/... e.g. /fr/solutions-monetiques, /en/payment-solutions
     * This keeps URLs clean and lets each language have its own SEO-friendly slug
     * while both point at the SAME underlying Page record (see Page model).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        app()->setLocale($locale);
        // Without this, Carbon::translatedFormat() silently renders in
        // whatever locale Carbon booted with (APP_LOCALE), regardless of
        // which /fr or /en route the visitor is actually on.
        Carbon::setLocale($locale);

        return $next($request);
    }
}
