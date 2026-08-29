@props(['variant' => 'light'])
@php
    $current = app()->getLocale();
    $other = $current === 'fr' ? 'en' : 'fr';
    $class = $variant === 'dark' ? 'footer__lang-switch' : 'navbar__lang-switch';
@endphp

<a href="{{ route('language.switch', ['locale' => $other]) }}" class="{{ $class }}">
    {{ strtoupper($other) }}
</a>
