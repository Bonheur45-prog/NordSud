<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        return $next($request);
    }
}
