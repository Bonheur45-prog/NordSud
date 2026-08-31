@php
    $nav = app(\App\Services\PublicNavigation::class);
    $groups = $nav->mainGroups();
    $locale = app()->getLocale();
@endphp

<header class="navbar" x-data="navbar" :class="{ 'navbar--scrolled': scrolled }" @click.away="closeGroups()">
    <div class="navbar__inner">
        <a href="{{ route('home', ['locale' => $locale]) }}" class="navbar__logo">
            <img src="{{ asset('images/nsi-logo.png') }}" alt="NSI Group">
        </a>

        <nav class="navbar__links">
            <a href="{{ route('home', ['locale' => $locale]) }}" class="navbar__link" @click="closeGroups()">
                {{ t('nav.home') }}
            </a>

            @foreach ($groups as $group)
                @continue($group['children']->isEmpty())
                <div class="navbar__group" :data-open="openGroup === '{{ $group['key'] }}'" @mouseenter="openGroupOnHover('{{ $group['key'] }}')" @mouseleave="closeGroupsOnLeave()">
                    <button
                        type="button"
                        class="navbar__link navbar__group-trigger"
                        @click="openGroupOnHover('{{ $group['key'] }}')"
                        :aria-expanded="openGroup === '{{ $group['key'] }}'"
                        :aria-controls="'dropdown-{{ $group['key'] }}'"
                    >
                        {{ t($group['label_key']) }}
                        <svg class="navbar__chevron" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div id="dropdown-{{ $group['key'] }}" class="navbar__dropdown" x-show="openGroup === '{{ $group['key'] }}'" x-cloak x-transition>
                        @foreach ($group['children'] as $child)
                            <a href="{{ route('page.show', ['locale' => $locale, 'slug' => $child->slug]) }}" class="navbar__dropdown-link" @click="closeGroups()">
                                {{ $child->title($locale) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <a href="{{ route('press.index', ['locale' => $locale]) }}" class="navbar__link" @click="closeGroups()">
                {{ t('nav.press') }}
            </a>
            <a href="{{ route('contact.show', ['locale' => $locale]) }}" class="navbar__link" @click="closeGroups()">
                {{ t('nav.contact') }}
            </a>
        </nav>

        <div class="navbar__actions">
            <x-language-switcher />

            <a href="{{ route('contact.show', ['locale' => $locale]) }}" class="navbar__cta">
                {{ t('cta.contact_us') }}
            </a>

            <button
                type="button"
                class="navbar__hamburger"
                @click="$dispatch('toggle-mobile-menu')"
                aria-label="{{ t('nav.menu') }}"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5 5H17.5M2.5 10H17.5M2.5 15H17.5" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>
</header>
