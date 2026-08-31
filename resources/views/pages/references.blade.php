@extends('layouts.app')

@php
    // Matched to ReferencesPage.php's admin schema: hero image + one
    // intro paragraph. The logo wall itself is NOT page content — it's
    // the same shared ClientReference list the homepage uses, already
    // passed in by PageController for this template.
    $content = $page?->content ?? [];
    $heroImage = data_get($content, 'hero.image');
    $introduction = $page->trans(data_get($content, 'introduction'));
@endphp

@section('content')

    <div class="references-hero" @if ($heroImage) style="background-image: url('{{ asset('storage/'.$heroImage) }}');" @endif>
        <div class="container">
            <h1 class="references-hero__title">{{ $page?->title() }}</h1>
        </div>
    </div>

    <div class="container references-page">
        @if ($introduction)
            <p class="references-intro reveal">{{ $introduction }}</p>
        @endif

        @if ($clientReferences->isNotEmpty())
            <div class="references-grid">
                @foreach ($clientReferences as $reference)
                    <div class="references-logo reveal">
                        @if ($reference->logo)
                            <img src="{{ asset('storage/'.$reference->logo) }}" alt="{{ $reference->name }}">
                        @else
                            <span>{{ $reference->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        (function () {
            var revealEls = document.querySelectorAll('.references-page .reveal');
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
                    el.style.transitionDelay = (Math.min(i % 8, 8) * 50) + 'ms';
                    observer.observe(el);
                });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }
        })();
    </script>

@endsection
