<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $page?->title() ?? 'NSI Group' }}</title>
    <meta name="description" content="{{ $page?->metaDescription() ?? '' }}">

    {{-- hreflang alternates: same slug, both locales, so search engines
         know these are the same content in two languages, not duplicates --}}
    @if (isset($page))
        <link rel="alternate" hreflang="fr" href="{{ url('/fr/'.($page->slug === 'home' ? '' : $page->slug)) }}">
        <link rel="alternate" hreflang="en" href="{{ url('/en/'.($page->slug === 'home' ? '' : $page->slug)) }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-navbar />
    <x-mobile-menu />

    <main>
        @yield('content')
    </main>

    <x-footer />
</body>
</html>
