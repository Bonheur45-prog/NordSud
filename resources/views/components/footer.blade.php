@php
    $nav = app(\App\Services\PublicNavigation::class);
    $locale = app()->getLocale();
    $companyLinks = $nav->footerCompanyLinks();
    $solutionLinks = $nav->footerSolutionLinks();

    // Static per-country contact summary — matches the old site's 4
    // countries. Kept here rather than modeled as pages, since it's a
    // small, rarely-changing fixed list, not editable content per se.
    $countries = [
        ['name' => 'Rwanda', 'phone' => '+250 731 552 696'],
        ['name' => 'RD Congo', 'phone' => '+243 999 854 064'],
        ['name' => 'Burundi', 'phone' => '+257 79 591 466'],
        ['name' => 'Tanzania', 'phone' => '+255 713 689 686'],
    ];
@endphp

<footer class="footer">
    <div class="footer__inner">
        <div class="footer__grid">
            <div>
                <div class="footer__brand-logo">
                    <img src="{{ asset('images/nsi-logo.png') }}" alt="NSI Group">
                </div>
                <p class="footer__tagline">{{ t('footer.tagline') }}</p>
            </div>

            <div>
                <div class="footer__heading">{{ t('footer.company') }}</div>
                <ul class="footer__links">
                    @foreach ($companyLinks as $page)
                        <li>
                            <a href="{{ route('page.show', ['locale' => $locale, 'slug' => $page->slug]) }}" class="footer__link">
                                {{ $page->title($locale) }}
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('press.index', ['locale' => $locale]) }}" class="footer__link">
                            {{ t('nav.press') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="footer__heading">{{ t('footer.solutions') }}</div>
                <ul class="footer__links">
                    @foreach ($solutionLinks as $page)
                        <li>
                            <a href="{{ route('page.show', ['locale' => $locale, 'slug' => $page->slug]) }}" class="footer__link">
                                {{ $page->title($locale) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <div class="footer__heading">{{ t('footer.get_in_touch') }}</div>
                @foreach ($countries as $country)
                    <div class="footer__country">
                        <strong>{{ $country['name'] }}</strong>
                        {{ $country['phone'] }}
                    </div>
                @endforeach
                <a href="{{ route('contact.show', ['locale' => $locale]) }}" class="footer__contact-cta">
                    {{ t('cta.contact_us') }}
                </a>
            </div>
        </div>

        <div class="footer__bottom">
            <span>&copy; {{ date('Y') }} NSI Group — {{ t('footer.rights_reserved') }}</span>
            <div class="footer__bottom-links">
                <a href="{{ route('page.show', ['locale' => $locale, 'slug' => 'legal-notices']) }}">
                    {{ t('footer.legal_notices') }}
                </a>
                <x-language-switcher variant="dark" />
            </div>
        </div>
    </div>
</footer>
