@props(['locale', 'popularPosts', 'popularTags', 'search' => '', 'activeTag' => null])

<aside class="press-sidebar">
    <form action="{{ route('press.index', ['locale' => $locale]) }}" method="GET" class="press-sidebar__search reveal">
        <label for="press-search" class="sr-only">{{ t('press.search_placeholder') }}</label>
        <input
            id="press-search"
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="{{ t('press.search_placeholder') }}"
            class="press-sidebar__search-input"
        >
        <button type="submit" class="press-sidebar__search-button" aria-label="{{ t('press.search_placeholder') }}">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </form>

    @if ($popularPosts->isNotEmpty())
        <div class="press-sidebar__box reveal">
            <h3 class="press-sidebar__box-title">{{ t('press.popular_posts') }}</h3>
            <ul class="press-sidebar__posts">
                @foreach ($popularPosts as $popular)
                    @php $popularTranslation = $popular->translation($locale); @endphp
                    @if ($popularTranslation)
                        <li class="press-sidebar__post">
                            <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $popular->slug]) }}" class="press-sidebar__post-thumb">
                                @if ($popular->featured_image)
                                    <img src="{{ asset('storage/'.$popular->featured_image) }}" alt="">
                                @else
                                    <span class="press-sidebar__post-thumb-placeholder" aria-hidden="true"></span>
                                @endif
                            </a>
                            <div class="press-sidebar__post-body">
                                <span class="press-sidebar__post-date">{{ $popular->published_at?->translatedFormat('d M') }}</span>
                                <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $popular->slug]) }}" class="press-sidebar__post-title">
                                    {{ $popularTranslation->title }}
                                </a>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    @if ($popularTags->isNotEmpty())
        <div class="press-sidebar__box reveal">
            <h3 class="press-sidebar__box-title">{{ t('press.popular_tags') }}</h3>
            <div class="press-sidebar__tags">
                @foreach ($popularTags as $tag)
                    <a
                        href="{{ route('press.index', ['locale' => $locale, 'tag' => $tag->slug]) }}"
                        class="press-tag-pill @if ($activeTag && $activeTag->id === $tag->id) press-tag-pill--active @endif"
                    >
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="press-tag-pill__icon" aria-hidden="true">
                            <path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3.24L4 3a1 1 0 0 0-1 1l.24 5.59a2 2 0 0 0 .59 1.41l9.58 9.58a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.82Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <circle cx="7.5" cy="7.5" r="1.25" fill="currentColor"/>
                        </svg>
                        {{ $tag->name($locale) }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</aside>
