@extends('layouts.app')

@php
    // The "about" template serves two real pages with genuinely different
    // content shapes (see app/Filament/Pages/PageContent/NsiGroupPage.php
    // vs KinshasaProductionCenterPage.php) — this template detects which
    // shape is present and renders accordingly, rather than assuming one.
    $content = $page?->content ?? [];
    $heroImage = data_get($content, 'hero.image');

    $paragraphs = data_get($content, 'paragraphs', []); // NSI Group shape
    $bodyImage = data_get($content, 'body.image');

    $opening = data_get($content, 'opening', []);       // Kinshasa shape
    $facility = data_get($content, 'facility', []);
    $certifications = data_get($content, 'certifications', []);
    $closing = data_get($content, 'closing', []);

    $isNarrativeShape = filled($opening) || filled($facility) || filled($certifications);
@endphp

@section('content')

    <div class="about-page-shell">
    <div class="about-hero" @if ($heroImage) style="background-image: url('{{ asset('storage/'.$heroImage) }}');" @endif>
        <div class="container">
            <h1 class="about-hero__title">{{ $page?->title() }}</h1>
        </div>
    </div>

    <div class="container about-page">

        @if ($isNarrativeShape)
            {{-- Kinshasa Production Center shape: repeating image + rich-text blocks --}}
            @foreach ([$opening, $facility, $certifications] as $block)
                @continue(blank(data_get($block, 'image')) && blank($page->trans(data_get($block, 'text'))))
                <section class="about-block reveal">
                    @if ($image = data_get($block, 'image'))
                        <img src="{{ asset('storage/'.$image) }}" alt="" class="about-block__image">
                    @endif
                    @if ($text = $page->trans(data_get($block, 'text')))
                        <div class="about-block__text">{!! $text !!}</div>
                    @endif
                </section>
            @endforeach

            @if ($closingText = $page->trans(data_get($closing, 'text')))
                <section class="about-block about-block--closing reveal">
                    <div class="about-block__text">{!! $closingText !!}</div>
                </section>
            @endif
        @else
            {{-- NSI Group shape: centered plain-text paragraphs + one image --}}
            @if ($paragraphs)
                <section class="about-intro reveal">
                    @foreach ($paragraphs as $paragraph)
                        @if ($text = $page->trans($paragraph))
                            <p class="about-intro__paragraph">{{ $text }}</p>
                        @endif
                    @endforeach
                </section>
            @endif

            @if ($bodyImage)
                <section class="about-intro-image reveal">
                    <img src="{{ asset('storage/'.$bodyImage) }}" alt="">
                </section>
            @endif
        @endif

    </div>

    <script>
        (function () {
            var revealEls = document.querySelectorAll('.about-page .reveal');
            if ('IntersectionObserver' in window && revealEls.length) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
                revealEls.forEach(function (el) { observer.observe(el); });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }
        })();
    </script>

    </div>

@endsection
