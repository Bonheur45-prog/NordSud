@props(['variant' => 'light'])
@php
    $current = app()->getLocale();
    $other = $current === 'fr' ? 'en' : 'fr';
    $locales = ['en' => 'English', 'fr' => 'Français'];
    $class = $variant === 'dark' ? 'footer__lang-group' : 'navbar__lang-group';
    $isNavbar = $variant !== 'dark';
@endphp

@if($isNavbar)
    <div class="{{ $class }}" x-data="languageDropdown" :data-open="open" @mouseenter="openLanguageDropdown()" @mouseleave="closeLanguageDropdown()">
        <button type="button" class="navbar__lang-switch" @click="open = !open">
            @if($current === 'en')
                🇬🇧 {{ strtoupper($current) }}
            @else
                🇫🇷 {{ strtoupper($current) }}
            @endif
            <svg class="navbar__lang-chevron" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="navbar__lang-dropdown">
            @foreach(['en', 'fr'] as $locale)
                @if($locale !== $current)
                    <a href="{{ route('language.switch', ['locale' => $locale]) }}" class="navbar__lang-dropdown-link" @click="open = false">
                        @if($locale === 'en')
                            🇬🇧 English
                        @else
                            🇫🇷 Français
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@else
    <a href="{{ route('language.switch', ['locale' => $other]) }}" class="{{ $class }}">
        {{ strtoupper($other) }}
    </a>
@endif
