@extends('layouts.app')

@php
    // Everything below is bound to the exact fields defined in each
    // dedicated editor (GroupJoosPage / KonaIPage / VervePage — see
    // app/Filament/Pages/PageContent/). This ONE template renders every
    // partner page: each partner is its own Page row with its own
    // slug/URL/CMS editor, they simply share the same "partner" template
    // file — exactly like every other page type on this site (home,
    // contact, etc. work the same way). If a section looks empty, that
    // field hasn't been filled in via /admin yet — nothing here is
    // hardcoded content.
    $content = $page?->content ?? [];

    $hero = data_get($content, 'hero', []);
    $intro = data_get($content, 'introduction', []);
    $paragraphs = data_get($content, 'paragraphs', []); // simple partners (KONA I, VERVE)
    $introText = $page->trans(data_get($intro, 'text'));  // rich partners (Group JOOS)
    $introLogo = data_get($intro, 'logo');

    $facts = data_get($content, 'facts', []);
    $facilityImage = data_get($content, 'facility.image');

    $certificates = data_get($content, 'certificates', []);
    $certTitle = $page->trans(data_get($certificates, 'title'));
    $certItems = data_get($certificates, 'items', []);
    $certImage = data_get($certificates, 'image');

    $productionSites = data_get($content, 'production_sites', []);
    $sitesTitle = $page->trans(data_get($productionSites, 'title'));
    $sites = data_get($productionSites, 'items', []);

    $capacity = data_get($content, 'capacity', []);
    $capacityTitle = $page->trans(data_get($capacity, 'title'));
    $capacityPoints = data_get($capacity, 'points', []);
    $capacityImages = data_get($capacity, 'images', []);

    $secureDoc = data_get($content, 'secure_document_certification', []);
    $secureDocTitle = $page->trans(data_get($secureDoc, 'title'));
    $secureDocText = $page->trans(data_get($secureDoc, 'text'));
    $secureDocImage = data_get($secureDoc, 'image');

    $hasIntro = $introLogo || $introText || count($paragraphs);
    $introSingleCol = ! ($introLogo && ($introText || count($paragraphs)));

    $hasFacts = count($facts) > 0;
    $factsSingleCol = ! ($hasFacts && $facilityImage);

    $hasCertificates = count($certItems) > 0;
    $certsSingleCol = ! ($hasCertificates && $certImage);

    $hasSites = count($sites) > 0;

    $hasCapacity = count($capacityPoints) > 0 || count($capacityImages) > 0;
    $capacitySingleCol = ! (count($capacityPoints) && count($capacityImages));

    $hasSecureDoc = filled($secureDocTitle) || filled($secureDocText) || $secureDocImage;
    $secureDocSingleCol = ! ($secureDocImage && filled($secureDocText));
@endphp

@section('content')

    {{-- Hero Banner --}}
    <div class="partner-hero" @if ($heroImg = data_get($hero, 'image')) style="background-image: url('{{ asset('storage/'.$heroImg) }}');" @endif>
        <div class="partner-hero__overlay"></div>
        <div class="container partner-hero__inner">
            <h1 class="partner-hero__title">{{ $page->title() }}</h1>
        </div>
    </div>

    {{-- Partner Introduction --}}
    @if ($hasIntro)
        <section class="partner-intro">
            <div class="container partner-intro__grid @if ($introSingleCol) partner-intro__grid--no-logo @endif">
                @if ($introLogo)
                    <div class="partner-intro__logo reveal">
                        <img src="{{ asset('storage/'.$introLogo) }}" alt="{{ $page->title() }} logo">
                    </div>
                @endif

                <div class="partner-intro__body reveal">
                    @if ($introText)
                        <div class="partner-rich-text">{!! $introText !!}</div>
                    @elseif (count($paragraphs))
                        @foreach ($paragraphs as $paragraph)
                            @if ($text = $page->trans($paragraph))
                                <p>{{ $text }}</p>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Key Facts + Facility Image --}}
    @if ($hasFacts)
        <section class="partner-facts">
            <div class="container partner-facts__grid @if ($factsSingleCol) partner-facts__grid--single @endif">
                <ul class="partner-checklist reveal">
                    @foreach ($facts as $fact)
                        @if ($text = $page->trans($fact))
                            <li class="partner-checklist__item">
                                <span class="partner-icon partner-icon--check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span>{{ $text }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if ($facilityImage)
                    <div class="partner-facts__image reveal">
                        <img src="{{ asset('storage/'.$facilityImage) }}" alt="">
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Guarantees & Certificates --}}
    @if ($hasCertificates)
        <section class="partner-certificates">
            <div class="container">
                @if ($certTitle)
                    <h2 class="partner-section-heading reveal">{{ $certTitle }}</h2>
                @endif

                <div class="partner-certificates__grid @if ($certsSingleCol) partner-certificates__grid--single @endif">
                    <ul class="partner-checklist partner-checklist--certs reveal">
                        @foreach ($certItems as $item)
                            @php
                                $text = $page->trans($item);
                                $isHeading = (bool) data_get($item, 'heading', false);
                            @endphp
                            @if ($text)
                                @if ($isHeading)
                                    <li class="partner-checklist__heading">
                                        <span class="partner-icon partner-icon--bullet" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M12 7V17M7 12H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <span>{{ $text }}</span>
                                    </li>
                                @else
                                    <li class="partner-checklist__item">
                                        <span class="partner-icon partner-icon--check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <span>{{ $text }}</span>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    </ul>

                    @if ($certImage)
                        <div class="partner-certificates__badge reveal">
                            <img src="{{ asset('storage/'.$certImage) }}" alt="{{ $certTitle }}">
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Production Sites --}}
    @if ($hasSites)
        <section class="partner-sites">
            <div class="container">
                @if ($sitesTitle)
                    <h2 class="partner-section-heading reveal">{{ $sitesTitle }}</h2>
                @endif

                <div class="partner-sites__grid">
                    @foreach ($sites as $site)
                        <div class="partner-site-card reveal">
                            @if ($siteImage = data_get($site, 'image'))
                                <div class="partner-site-card__image">
                                    <img src="{{ asset('storage/'.$siteImage) }}" alt="">
                                </div>
                            @endif
                            @if ($siteName = $page->trans(data_get($site, 'name')))
                                <div class="partner-site-card__caption">
                                    <span class="partner-icon partner-icon--pin" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 21s6-5.686 6-11a6 6 0 1 0-12 0c0 5.314 6 11 6 11Zm0-8.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <span>{{ $siteName }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Production Capacity --}}
    @if ($hasCapacity)
        <section class="partner-capacity">
            <div class="container">
                @if ($capacityTitle)
                    <h2 class="partner-section-heading reveal">{{ $capacityTitle }}</h2>
                @endif

                <div class="partner-capacity__grid @if ($capacitySingleCol) partner-capacity__grid--single @endif">
                    @if (count($capacityPoints))
                        <ul class="partner-checklist reveal">
                            @foreach ($capacityPoints as $point)
                                @if ($text = $page->trans($point))
                                    <li class="partner-checklist__item">
                                        <span class="partner-icon partner-icon--check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <span>{{ $text }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    @if (count($capacityImages))
                        <div class="partner-capacity__gallery reveal">
                            @foreach ($capacityImages as $image)
                                @if ($src = data_get($image, 'image'))
                                    <div class="partner-capacity__gallery-item">
                                        <img src="{{ asset('storage/'.$src) }}" alt="">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Secure Document Certification --}}
    @if ($hasSecureDoc)
        <section class="partner-secure-doc">
            <div class="container">
                @if ($secureDocTitle)
                    <h2 class="partner-section-heading partner-section-heading--center reveal">{{ $secureDocTitle }}</h2>
                @endif

                <div class="partner-secure-doc__grid @if ($secureDocSingleCol) partner-secure-doc__grid--single @endif">
                    @if ($secureDocImage)
                        <div class="partner-secure-doc__image reveal">
                            <img src="{{ asset('storage/'.$secureDocImage) }}" alt="{{ $secureDocTitle }}">
                        </div>
                    @endif

                    @if ($secureDocText)
                        <div class="partner-rich-text partner-secure-doc__body reveal">
                            <span class="partner-icon partner-icon--check partner-icon--inline" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            {!! $secureDocText !!}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <script>
        (function () {
            var revealEls = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window && revealEls.length) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

                revealEls.forEach(function (el, i) {
                    el.style.transitionDelay = (Math.min(i % 6, 6) * 60) + 'ms';
                    observer.observe(el);
                });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }
        })();
    </script>
@endsection
