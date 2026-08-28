<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Swaps the {locale} segment of the current URL and redirects back,
     * so switching language keeps the visitor on the equivalent page
     * (e.g. /fr/solutions-monetiques -> /en/solutions-monetiques)
     * instead of dropping them to the homepage like some old WP setups do.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::SUPPORTED, true), 404);

        $previous = $request->headers->get('referer', '/');
        $path = parse_url($previous, PHP_URL_PATH) ?? '/'.SetLocale::DEFAULT;
        $segments = explode('/', trim($path, '/'));

        if (in_array($segments[0] ?? null, SetLocale::SUPPORTED, true)) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        return redirect('/'.implode('/', $segments));
    }
}
