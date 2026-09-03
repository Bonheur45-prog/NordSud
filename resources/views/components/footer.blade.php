@php
    $nav = app(\App\Services\PublicNavigation::class);
    $locale = app()->getLocale();
    $companyLinks = $nav->footerCompanyLinks();
    $solutionLinks = $nav->footerSolutionLinks();
    $partnerLinks = $nav->footerPartnerLinks();

    // Static per-country contact summary for the footer.
    $countries = [
        ['name' => 'Rwanda', 'phone' => '+250 731 552 696'],
        ['name' => 'Burundi', 'phone' => '+257 79 591 466'],
        ['name' => 'Tanzania', 'phone' => '+255 713 689 686'],
        ['name' => 'Kenya', 'phone' => '+254 768 068326', 'location' => 'Tatu City, Nairobi, Kenya'],
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
                <div class="footer__social-icons">
                    <a href="https://www.instagram.com/nsigroup/" target="_blank" rel="noopener noreferrer" class="footer__social-icon" title="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.75 2H16.25C19.4138 2 22 4.5862 22 7.75V16.25C22 19.4138 19.4138 22 16.25 22H7.75C4.5862 22 2 19.4138 2 16.25V7.75C2 4.5862 4.5862 2 7.75 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.75 6.5H16.76" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/company/nsi-group/" target="_blank" rel="noopener noreferrer" class="footer__social-icon" title="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 8C17.5913 8 19.1174 8.63214 20.2426 9.75736C21.3679 10.8826 22 12.4087 22 14V21H18V14C18 13.4696 17.7893 12.9609 17.4142 12.5858C17.0391 12.2107 16.5304 12 16 12C15.4696 12 14.9609 12.2107 14.5858 12.5858C14.2107 12.9609 14 13.4696 14 14V21H10V14C10 12.4087 10.6321 10.8826 11.7574 9.75736C12.8826 8.63214 14.4087 8 16 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 9H2V21H6V9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 6C5.10457 6 6 5.10457 6 4C6 2.89543 5.10457 2 4 2C2.89543 2 2 2.89543 2 4C2 5.10457 2.89543 6 4 6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com/nsigroup" target="_blank" rel="noopener noreferrer" class="footer__social-icon" title="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 4.00001C21.0424 4.67552 19.9821 5.16149 18.86 5.41001C18.2577 4.74458 17.4573 4.29364 16.556 4.11975C15.6546 3.94585 14.7145 4.05771 13.8787 4.43014C13.0429 4.80257 12.3635 5.41769 12.0948 6.17659C11.8261 6.93549 11.8792 7.75833 12.1445 8.51001C10.3144 8.40915 8.51807 7.84952 6.93689 6.87549C5.35571 5.90146 4.03622 4.54165 3.10872 2.90501C2.71769 3.60188 2.52542 4.38338 2.52542 5.18501C2.52542 6.94999 3.37128 8.54498 4.75 9.50001C3.97953 9.5035 3.22882 9.31276 2.54 8.95501V9.01501C2.54 10.8553 3.37128 12.5881 4.75 13.6384C6.12872 14.6887 7.84622 15.0746 9.54 14.714C8.35394 15.8999 6.75711 16.5306 5.09 16.5306C4.71794 16.5306 4.35096 16.5084 3.99 16.4649C5.70294 17.7788 7.84622 18.5745 10.1925 18.5745C16.0553 18.5745 19.3635 14.999 19.3635 11.3955C19.3635 11.2901 19.3635 11.1847 19.354 11.0793C20.0504 10.5644 20.6423 9.92526 21.1 9.20001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/nsigroup" target="_blank" rel="noopener noreferrer" class="footer__social-icon" title="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 2H15C13.6739 2 12.4021 2.52678 11.4645 3.46447C10.5268 4.40215 10 5.67392 10 7V10H7V14H10V22H14V14H17L18 10H14V7C14 6.73478 14.1054 6.48043 14.2929 6.29289C14.4804 6.10536 14.7348 6 15 6H18V2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
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
                <div class="footer__heading">{{ t('nav.partners') }}</div>
                <ul class="footer__links">
                    @foreach ($partnerLinks as $page)
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
                        @if (! empty($country['location']))
                            <span>{{ $country['location'] }}</span>
                        @endif
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
