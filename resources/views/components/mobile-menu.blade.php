@php
    $nav = app(\App\Services\PublicNavigation::class);
    $groups = $nav->mainGroups();
    $locale = app()->getLocale();
@endphp

<div x-data="mobileMenuData" @open-mobile-menu.window="show()" @close-mobile-menu.window="hide()" @toggle-mobile-menu.window="toggle()">
    <div class="mobile-menu-backdrop" x-show="open" x-cloak x-transition.opacity @click="hide()"></div>

    <aside class="mobile-menu" x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
        <div class="mobile-menu__header">
            <img src="{{ asset('images/nsi-logo.png') }}" alt="NSI Group" style="height: 28px;">
            <button type="button" class="mobile-menu__close" @click="hide()" aria-label="{{ t('nav.close') }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L15 15M15 1L1 15" stroke="#1f2937" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="mobile-menu__body">
            <a href="{{ route('home', ['locale' => $locale]) }}" class="mobile-menu__link" @click="hide()">
                {{ t('nav.home') }}
            </a>

            @foreach ($groups as $group)
                @continue($group['children']->isEmpty())
                <div class="mobile-menu__group" :data-open="openGroups.includes('{{ $group['key'] }}')">
                    <button type="button" class="mobile-menu__group-trigger" @click="toggleGroup('{{ $group['key'] }}')">
                        <span>{{ t($group['label_key']) }}</span>
                        <svg class="mobile-menu__chevron" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="mobile-menu__group-content" x-show="openGroups.includes('{{ $group['key'] }}')">
                        @foreach ($group['children'] as $child)
                            <a href="{{ route('page.show', ['locale' => $locale, 'slug' => $child->slug]) }}" class="mobile-menu__sublink" @click="hide()">
                                {{ $child->title($locale) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <a href="{{ route('press.index', ['locale' => $locale]) }}" class="mobile-menu__link" @click="hide()">
                {{ t('nav.press') }}
            </a>
            <a href="{{ route('contact.show', ['locale' => $locale]) }}" class="mobile-menu__link" @click="hide()">
                {{ t('nav.contact') }}
            </a>

            <a href="{{ route('contact.show', ['locale' => $locale]) }}" class="mobile-menu__cta" @click="hide()">
                {{ t('cta.contact_us') }}
            </a>
        </div>
    </aside>
</div>
