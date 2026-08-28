<?php

use App\Services\TranslationResolver;

if (! function_exists('t')) {
    /**
     * Shortcut for resolving a UI translation string in Blade views.
     * {{ t('nav.solutions') }}
     */
    function t(string $key, ?string $locale = null): string
    {
        return app(TranslationResolver::class)->get($key, $locale);
    }
}
