@extends('layouts.app')

@section('content')
    <div class="press-hero">
        <div class="press-hero__overlay"></div>
        <div class="container press-hero__inner">
            <h1 class="press-hero__title">{{ t('press.index_title') }}</h1>
            <nav class="press-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home', ['locale' => $locale]) }}">{{ t('press.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span class="press-hero__breadcrumb-current">{{ t('press.index_title') }}</span>
            </nav>
        </div>
    </div>

    <section class="press-index">
        <div class="container press-index__layout">
            <div class="press-index__main">
                @if (! $search && ! $activeTag)
                    <div class="press-index__heading reveal">
                        <span class="press-index__eyebrow">{{ t('press.eyebrow') }}</span>
                        <h2>{{ t('press.index_heading') }}</h2>
                    </div>
                @else
                    <div class="press-index__filter-bar reveal">
                        <p class="press-index__filter-label">
                            @if ($search)
                                {{ t('press.search_results_for') }} "{{ $search }}"
                            @elseif ($activeTag)
                                {{ $activeTag->name($locale) }}
                            @endif
                            <span class="press-index__filter-count">({{ $posts->count() }})</span>
                        </p>
                        <a href="{{ route('press.index', ['locale' => $locale]) }}" class="press-index__clear">
                            {{ t('press.clear_filters') }}
                        </a>
                    </div>
                @endif

                @if ($posts->isEmpty())
                    <p class="press-index__empty reveal">{{ t('press.no_results') }}</p>
                @else
                    <div class="press-grid">
                        @foreach ($posts as $post)
                            @php $cardTranslation = $post->translation($locale); @endphp
                            @continue(! $cardTranslation)
                            <article class="press-card reveal">
                                <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}" class="press-card__image-link">
                                    @if ($post->featured_image)
                                        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="press-card__image">
                                    @else
                                        <span class="press-card__image press-card__image--placeholder" aria-hidden="true"></span>
                                    @endif
                                    @if ($post->published_at)
                                        <span class="press-card__date-badge">
                                            <span class="press-card__date-day">{{ $post->published_at->format('d') }}</span>
                                            <span class="press-card__date-month">{{ $post->published_at->translatedFormat('M') }}</span>
                                        </span>
                                    @endif
                                </a>

                                <div class="press-card__meta">
                                    @if ($post->author)
                                        <span class="press-card__meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                            {{ $post->author->name }}
                                        </span>
                                    @endif
                                    <span class="press-card__meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 1 1-3.8-7.07L21 3l-1 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $post->comments_count }}
                                    </span>
                                </div>

                                <h3 class="press-card__title">
                                    <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}">
                                        {{ $cardTranslation->title }}
                                    </a>
                                </h3>

                                <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}" class="press-card__more">
                                    {{ t('press.read_more') }}
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            <x-press-sidebar
                :locale="$locale"
                :popular-posts="$popularPosts"
                :popular-tags="$popularTags"
                :search="$search"
                :active-tag="$activeTag"
            />
        </div>
    </section>

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
                }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

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
