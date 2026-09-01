@extends('layouts.app')

@php
    // Everything below is bound to the exact fields defined in
    // app/Filament/Pages/PageContent/HomePage.php's contentSchema(). If a
    // section looks empty, that repeater/field hasn't been filled in via
    // /admin yet — nothing here is hardcoded content.
    $locale = app()->getLocale();
    $content = $page?->content ?? [];

    $resolveLink = function (?string $path) use ($locale) {
        if (blank($path)) {
            return '#';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $trimmed = trim($path, '/');

        return $trimmed === '' ? url("/{$locale}") : url("/{$locale}/{$trimmed}");
    };

    $slides = data_get($content, 'hero.slides', []);
    $heroFallbackCtaLabel = $page->trans(data_get($content, 'hero.cta_label'));
    $heroFallbackCtaLink = data_get($content, 'hero.cta_link');

    $spotlights = data_get($content, 'spotlights', []);
    $digitalBanking = data_get($content, 'digital_banking', []);
    $secondaryBanner = data_get($content, 'secondary_banner', []);
    $highlights = data_get($content, 'highlights', []);
    $statsBanner = data_get($content, 'stats_banner', []);
    $contactCta = data_get($content, 'contact', []);
@endphp

@section('content')

    <div class="home-page">

    {{-- Hero Slider --}}
    @if ($slides)
        <section class="home-hero" id="home-hero">
            @foreach ($slides as $i => $slide)
                @php
                    $ctaLabel = $page->trans(data_get($slide, 'cta_label')) ?: $heroFallbackCtaLabel;
                    $ctaLink = data_get($slide, 'cta_link') ?: $heroFallbackCtaLink;
                    $slideImage = data_get($slide, 'image');
                @endphp
                <div class="home-hero__slide {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}">
                    <div class="home-hero__bg" @if ($slideImage) style="background-image: url('{{ asset('storage/'.$slideImage) }}');" @endif></div>
                    <div class="home-hero__overlay"></div>
                    <div class="container home-hero__content">
                        @if ($eyebrow = $page->trans(data_get($slide, 'eyebrow')))
                            <p class="home-hero__eyebrow">{{ $eyebrow }}</p>
                        @endif
                        <h1 class="home-hero__headline">{{ $page->trans(data_get($slide, 'headline')) }}</h1>
                        @if ($subline = $page->trans(data_get($slide, 'subline')))
                            <p class="home-hero__subline">{{ $subline }}</p>
                        @endif
                        @if ($ctaLabel)
                            <a href="{{ $resolveLink($ctaLink) }}" class="home-hero__cta">{{ $ctaLabel }}</a>
                        @endif
                    </div>
                </div>
            @endforeach

            @if (count($slides) > 1)
                <button type="button" class="home-hero__arrow home-hero__arrow--prev" onclick="nsiHeroSlide(-1)" aria-label="{{ t('home.hero_prev') }}">&#10094;</button>
                <button type="button" class="home-hero__arrow home-hero__arrow--next" onclick="nsiHeroSlide(1)" aria-label="{{ t('home.hero_next') }}">&#10095;</button>
                <div class="home-hero__dots" id="home-hero-dots">
                    @foreach ($slides as $i => $slide)
                        <button type="button" class="home-hero__dot {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}" onclick="nsiHeroGoTo({{ $i }})" aria-label="{{ t('home.hero_next') }} {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Featured Spotlights --}}
    @if ($spotlights)
        <section class="home-spotlight">
            <div class="container">
                <div class="home-spotlight__grid">
                    @foreach ($spotlights as $spotlight)
                        @php
                            $spotlightLink = $resolveLink(data_get($spotlight, 'link'));
                            $spotlightImage = data_get($spotlight, 'image');
                            $spotlightBody = $page->trans(data_get($spotlight, 'body'));
                        @endphp
                        <div class="home-spotlight__item reveal">
                            @if ($headline = $page->trans(data_get($spotlight, 'headline')))
                                <h2 class="home-spotlight__headline">{{ $headline }}</h2>
                            @endif
                            <div class="home-spotlight__row">
                                @if ($spotlightImage)
                                    <a href="{{ $spotlightLink }}" class="home-spotlight__image">
                                        <img src="{{ asset('storage/'.$spotlightImage) }}" alt="">
                                    </a>
                                @endif
                                @if ($spotlightBody)
                                    <a href="{{ $spotlightLink }}" class="home-spotlight__link">{{ $spotlightBody }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Digital Banking Feature (falls back to Secondary Banner if empty) --}}
    @php
        $videoSection = data_get($digitalBanking, 'headline') || data_get($digitalBanking, 'video')
            ? $digitalBanking
            : $secondaryBanner;
    @endphp
    @if (data_get($videoSection, 'headline') || data_get($videoSection, 'video'))
        <section class="home-video" id="home-video">
            @if ($bg = data_get($videoSection, 'background_image'))
                <div class="home-video__bg" id="home-video-bg" style="background-image: url('{{ asset('storage/'.$bg) }}');"></div>
            @endif
            <div class="home-video__overlay"></div>
            <div class="container home-video__content reveal">
                @if ($headline = $page->trans(data_get($videoSection, 'headline')))
                    <h2 class="home-video__headline">{{ $headline }}</h2>
                @endif
                @if ($body = $page->trans(data_get($videoSection, 'body')))
                    <p class="home-video__body">{{ $body }}</p>
                @endif
                @if ($video = data_get($videoSection, 'video'))
                    <div class="home-video__player">
                        <video controls
                               @if ($poster = data_get($videoSection, 'poster')) poster="{{ asset('storage/'.$poster) }}" @endif>
                            <source src="{{ asset('storage/'.$video) }}">
                        </video>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Section Highlights --}}
    @if ($highlights)
        <section class="home-highlights">
            <div class="container">
                <h2 class="home-highlights__heading reveal">{{ t('home.highlights_heading') }}</h2>
                <p class="home-highlights__intro reveal">{{ t('home.highlights_intro') }}</p>
                <div class="home-highlights__grid">
                    @foreach ($highlights as $card)
                        <a href="{{ $resolveLink(data_get($card, 'link')) }}" class="home-highlight-card reveal">
                            @if ($image = data_get($card, 'image'))
                                <img src="{{ asset('storage/'.$image) }}" alt="" class="home-highlight-card__image">
                            @endif
                            <h3 class="home-highlight-card__title">{{ $page->trans(data_get($card, 'title')) }}</h3>
                            @if ($description = $page->trans(data_get($card, 'description')))
                                <p class="home-highlight-card__description">{{ $description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Stats / CTA Banner --}}
    @if (data_get($statsBanner, 'headline'))
        <section class="home-stats"
                 @if ($bg = data_get($statsBanner, 'background_image')) style="background-image: url('{{ asset('storage/'.$bg) }}');" @endif>
            <div class="container home-stats__row reveal">
                <p class="home-stats__headline">{{ $page->trans(data_get($statsBanner, 'headline')) }}</p>
                @if ($ctaLabel = $page->trans(data_get($statsBanner, 'cta_label')))
                    <a href="{{ $resolveLink(data_get($statsBanner, 'cta_link')) }}" class="home-stats__cta">{{ $ctaLabel }}</a>
                @endif
            </div>
        </section>
    @endif

    {{-- References Wall --}}
    @if ($clientReferences->isNotEmpty())
        <section class="home-references">
            <div class="container home-references__grid">
                @foreach ($clientReferences as $reference)
                    <div class="home-reference-logo reveal">
                        @if ($reference->logo)
                            <img src="{{ asset('storage/'.$reference->logo) }}" alt="{{ $reference->name }}">
                        @else
                            <span>{{ $reference->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- News Feed --}}
    @if ($pressPosts->isNotEmpty())
        <section class="home-news">
            <div class="container">
                <h2 class="home-news__heading reveal">{{ t('home.news_heading') }}</h2>
                <div class="home-news__grid">
                    @foreach ($pressPosts as $post)
                        @php($translation = $post->translation($locale))
                        <article class="home-news-card reveal">
                            @if ($post->featured_image)
                                <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="home-news-card__image">
                            @endif
                            <span class="home-news-card__badge">{{ t('home.news_badge') }}</span>
                            <h3 class="home-news-card__title">
                                <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}">
                                    {{ $translation?->title ?? $post->slug }}
                                </a>
                            </h3>
                            @if ($translation?->excerpt)
                                <p class="home-news-card__excerpt">{{ $translation->excerpt }}</p>
                            @endif
                            <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}" class="home-news-card__more">
                                {{ t('home.read_more') }}
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact CTA — elevated "digital fintech" treatment: animated
         gradient, floating accent shapes, no new content fields, purely
         visual on top of the same headline/body/link data as before. --}}
    @if (data_get($contactCta, 'headline') || data_get($contactCta, 'body'))
        <section class="home-contact-cta">
            @if ($bg = data_get($contactCta, 'background_image'))
                <div class="home-contact-cta__bg" style="background-image: url('{{ asset('storage/'.$bg) }}');"></div>
            @endif
            <div class="home-contact-cta__glow home-contact-cta__glow--one"></div>
            <div class="home-contact-cta__glow home-contact-cta__glow--two"></div>
            <div class="home-contact-cta__grid-pattern"></div>
            <div class="container home-contact-cta__content reveal">
                @if ($headline = $page->trans(data_get($contactCta, 'headline')))
                    <h2 class="home-contact-cta__headline">{{ $headline }}</h2>
                @endif
                @if ($body = $page->trans(data_get($contactCta, 'body')))
                    <p class="home-contact-cta__body">{{ $body }}</p>
                @endif
                <a href="{{ $resolveLink(data_get($contactCta, 'link', '/contact')) }}" class="home-contact-cta__button">
                    <span>{{ t('contact.title') }}</span>
                    <svg class="home-contact-cta__button-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </section>
    @endif

    <script>
        (function () {
            // ---- Hero: auto-advance + dots (only wired up if >1 slide) ----
            var slideEls = document.querySelectorAll('#home-hero .home-hero__slide');
            if (slideEls.length > 1) {
                var AUTO_ADVANCE_MS = 6000;
                var timer = null;

                function activeIndex() {
                    var idx = 0;
                    slideEls.forEach(function (slide, i) {
                        if (slide.classList.contains('is-active')) idx = i;
                    });
                    return idx;
                }

                function setActive(index) {
                    var dots = document.querySelectorAll('#home-hero-dots .home-hero__dot');
                    slideEls.forEach(function (slide, i) {
                        slide.classList.toggle('is-active', i === index);
                    });
                    dots.forEach(function (dot, i) {
                        dot.classList.toggle('is-active', i === index);
                    });
                }

                function goTo(direction) {
                    var next = (activeIndex() + direction + slideEls.length) % slideEls.length;
                    setActive(next);
                }

                function start() {
                    stop();
                    timer = window.setInterval(function () { goTo(1); }, AUTO_ADVANCE_MS);
                }

                function stop() {
                    if (timer) window.clearInterval(timer);
                }

                window.nsiHeroSlide = function (direction) {
                    goTo(direction);
                    start();
                };

                window.nsiHeroGoTo = function (index) {
                    setActive(index);
                    start();
                };

                var heroEl = document.getElementById('home-hero');
                heroEl.addEventListener('mouseenter', stop);
                heroEl.addEventListener('mouseleave', start);

                start();
            }

            // ---- Digital Banking: lightweight scroll parallax on the bg layer ----
            var videoBg = document.getElementById('home-video-bg');
            var videoSection = document.getElementById('home-video');
            if (videoBg && videoSection) {
                var ticking = false;
                function updateParallax() {
                    var rect = videoSection.getBoundingClientRect();
                    var viewportH = window.innerHeight || document.documentElement.clientHeight;
                    // Only move while the section is anywhere near the viewport.
                    if (rect.bottom > 0 && rect.top < viewportH) {
                        var progress = (viewportH - rect.top) / (viewportH + rect.height);
                        var offset = (progress - 0.5) * 60; // px of vertical drift
                        videoBg.style.transform = 'translateY(' + offset + 'px) scale(1.15)';
                    }
                    ticking = false;
                }
                window.addEventListener('scroll', function () {
                    if (!ticking) {
                        window.requestAnimationFrame(updateParallax);
                        ticking = true;
                    }
                }, { passive: true });
                updateParallax();
            }

            // ---- Scroll-reveal for everything marked .reveal ----
            var revealEls = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window && revealEls.length) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

                revealEls.forEach(function (el, i) {
                    el.style.transitionDelay = (Math.min(i % 6, 6) * 60) + 'ms';
                    observer.observe(el);
                });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }
        })();
    </script>

    </div>

@endsection
