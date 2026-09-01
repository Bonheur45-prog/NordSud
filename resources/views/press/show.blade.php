@extends('layouts.app')

@section('content')
    <div class="press-hero">
        <div class="press-hero__overlay"></div>
        <div class="container press-hero__inner">
            <h1 class="press-hero__title">{{ t('press.show_title') }}</h1>
            <nav class="press-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home', ['locale' => $locale]) }}">{{ t('press.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('press.index', ['locale' => $locale]) }}">{{ t('press.index_title') }}</a>
                <span aria-hidden="true">/</span>
                <span class="press-hero__breadcrumb-current">{{ t('press.show_title') }}</span>
            </nav>
        </div>
    </div>

    <section class="press-show">
        <div class="container press-show__layout">
            <div class="press-show__main">
                @if ($post->featured_image)
                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="press-show__cover reveal">
                @else
                    <div class="press-show__cover press-show__cover--placeholder reveal" aria-hidden="true"></div>
                @endif

                <h2 class="press-show__title reveal">{{ $translation?->title }}</h2>

                <div class="press-show__meta reveal">
                    @if ($post->author)
                        <span class="press-show__meta-item">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            {{ $post->author->name }}
                        </span>
                    @endif
                    @if ($post->published_at)
                        <span class="press-show__meta-item">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            {{ $post->published_at->translatedFormat('j F Y') }}
                        </span>
                    @endif
                    <span class="press-show__meta-item">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 1 1-3.8-7.07L21 3l-1 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $comments->count() }}
                    </span>
                </div>

                <div class="press-rich-text reveal">
                    {!! $translation?->body !!}
                </div>

                <div class="press-show__footer reveal">
                    @if ($post->tags->isNotEmpty())
                        <div class="press-show__tags">
                            @foreach ($post->tags as $tag)
                                <a href="{{ route('press.index', ['locale' => $locale, 'tag' => $tag->slug]) }}" class="press-tag-pill">
                                    {{ $tag->name($locale) }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="press-show__share">
                        <span class="press-show__share-label">{{ t('press.share') }}</span>
                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="press-icon-button" aria-label="Facebook"
                        >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 9h3V6h-3c-1.66 0-3 1.34-3 3v2H9v3h2v6h3v-6h3l1-3h-4V9c0-.55.45-1 1-1Z" fill="currentColor"/></svg>
                        </a>
                        <a
                            href="https://api.whatsapp.com/send?text={{ urlencode($translation?->title.' — '.request()->fullUrl()) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="press-icon-button" aria-label="WhatsApp"
                        >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.04 0C5.5 0 .2 5.3.2 11.85c0 2.09.55 4.13 1.6 5.93L0 24l6.4-1.68a11.83 11.83 0 0 0 5.64 1.44h.01c6.54 0 11.85-5.3 11.85-11.85 0-3.16-1.23-6.14-3.38-8.43ZM12.04 21.5h-.01a9.7 9.7 0 0 1-4.94-1.35l-.35-.21-3.8 1 1.01-3.7-.23-.38a9.66 9.66 0 0 1-1.49-5.16c0-5.36 4.36-9.72 9.73-9.72 2.6 0 5.04 1.01 6.88 2.86a9.65 9.65 0 0 1 2.85 6.87c0 5.37-4.37 9.73-9.73 9.73h.08Z" fill="currentColor"/></svg>
                        </a>
                        <a
                            href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($translation?->title ?? '') }}"
                            target="_blank" rel="noopener noreferrer"
                            class="press-icon-button" aria-label="X"
                        >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4l7.5 9.6L4.5 20H7l5.2-5.5L16.5 20H20l-8-10.2L18.5 4H16l-4.7 5L7.5 4H4Z" fill="currentColor"/></svg>
                        </a>
                        <a
                            href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="press-icon-button" aria-label="LinkedIn"
                        >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5a1.96 1.96 0 1 0 0 3.92 1.96 1.96 0 0 0 0-3.92ZM20.44 20h-3.38v-6.02c0-1.44-.03-3.28-2-3.28-2 0-2.31 1.56-2.31 3.18V20H9.38V8.5h3.24v1.57h.05c.45-.85 1.56-1.75 3.21-1.75 3.43 0 4.06 2.26 4.06 5.19V20Z" fill="currentColor"/></svg>
                        </a>
                    </div>
                </div>

                @if ($post->author)
                    <div class="press-author-card reveal">
                        @if ($post->author->avatar)
                            <img src="{{ asset('storage/'.$post->author->avatar) }}" alt="{{ $post->author->name }}" class="press-author-card__avatar">
                        @else
                            <span class="press-author-card__avatar press-author-card__avatar--placeholder" aria-hidden="true">
                                {{ collect(explode(' ', $post->author->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                            </span>
                        @endif
                        <div class="press-author-card__body">
                            <h3 class="press-author-card__name">{{ $post->author->name }}</h3>
                            @if ($role = $post->author->role($locale))
                                <span class="press-author-card__role">{{ $role }}</span>
                            @endif
                            @if ($bio = $post->author->bio($locale))
                                <p class="press-author-card__bio">{{ $bio }}</p>
                            @endif
                            @if ($links = $post->author->socialLinks())
                                <div class="press-author-card__socials">
                                    @foreach ($links as $network => $url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="press-icon-button" aria-label="{{ ucfirst($network) }}">
                                            @if ($network === 'facebook')
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 9h3V6h-3c-1.66 0-3 1.34-3 3v2H9v3h2v6h3v-6h3l1-3h-4V9c0-.55.45-1 1-1Z" fill="currentColor"/></svg>
                                            @elseif ($network === 'instagram')
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="3.6" stroke="currentColor" stroke-width="1.6"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></svg>
                                            @elseif ($network === 'x')
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4l7.5 9.6L4.5 20H7l5.2-5.5L16.5 20H20l-8-10.2L18.5 4H16l-4.7 5L7.5 4H4Z" fill="currentColor"/></svg>
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3.5a1.96 1.96 0 1 0 0 3.92 1.96 1.96 0 0 0 0-3.92ZM20.44 20h-3.38v-6.02c0-1.44-.03-3.28-2-3.28-2 0-2.31 1.56-2.31 3.18V20H9.38V8.5h3.24v1.57h.05c.45-.85 1.56-1.75 3.21-1.75 3.43 0 4.06 2.26 4.06 5.19V20Z" fill="currentColor"/></svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($previousPost || $nextPost)
                    <div class="press-neighbor-nav reveal">
                        <a
                            href="{{ $previousPost ? route('press.show', ['locale' => $locale, 'slug' => $previousPost->slug]) : '#' }}"
                            class="press-neighbor-nav__item @if (! $previousPost) press-neighbor-nav__item--disabled @endif"
                        >
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>
                                <small>{{ t('press.previous') }}</small>
                                @if ($previousPost)
                                    {{ $previousPost->translation($locale)?->title }}
                                @endif
                            </span>
                        </a>
                        <a
                            href="{{ $nextPost ? route('press.show', ['locale' => $locale, 'slug' => $nextPost->slug]) : '#' }}"
                            class="press-neighbor-nav__item press-neighbor-nav__item--next @if (! $nextPost) press-neighbor-nav__item--disabled @endif"
                        >
                            <span>
                                <small>{{ t('press.next') }}</small>
                                @if ($nextPost)
                                    {{ $nextPost->translation($locale)?->title }}
                                @endif
                            </span>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                @endif

                <div id="comments" class="press-comments reveal">
                    <h3 class="press-comments__heading">{{ t('press.comments_heading') }} ({{ $comments->count() }})</h3>

                    @if ($comments->isNotEmpty())
                        <ul class="press-comments__list">
                            @foreach ($comments as $comment)
                                <li class="press-comment">
                                    <span class="press-comment__avatar" aria-hidden="true">
                                        {{ collect(explode(' ', $comment->author_name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                    </span>
                                    <div class="press-comment__body">
                                        <div class="press-comment__header">
                                            <span class="press-comment__name">{{ $comment->author_name }}</span>
                                            <span class="press-comment__date">{{ $comment->commented_at?->translatedFormat('j F Y') }}</span>
                                        </div>
                                        <p class="press-comment__text">{{ $comment->body }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="press-comment-form">
                        <h3 class="press-comment-form__heading">{{ t('press.leave_a_comment') }}</h3>

                        @if (session('comment_success'))
                            <p class="press-comment-form__success" role="status">{{ t('press.comment_success') }}</p>
                        @endif

                        <form action="{{ route('press.comments.store', ['locale' => $locale, 'slug' => $post->slug]) }}" method="POST">
                            @csrf
                            <div class="press-comment-form__row">
                                <input type="text" name="author_name" placeholder="{{ t('press.your_name') }}" value="{{ old('author_name') }}" required>
                                <input type="email" name="author_email" placeholder="{{ t('press.email_address') }}" value="{{ old('author_email') }}">
                            </div>
                            {{-- Honeypot: real visitors never see or fill this field. --}}
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="press-comment-form__honeypot" aria-hidden="true">
                            <textarea name="body" rows="5" placeholder="{{ t('press.your_comment') }}" required>{{ old('body') }}</textarea>
                            <button type="submit" class="press-comment-form__submit">
                                {{ t('press.post_comment') }}
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>

                        @if ($errors->any())
                            <p class="press-comment-form__error" role="alert">
                                {{ $errors->first() }}
                            </p>
                        @endif

                        <p class="press-comment-form__note">{{ t('press.comment_moderation_note') }}</p>
                    </div>
                </div>
            </div>

            <x-press-sidebar
                :locale="$locale"
                :popular-posts="$popularPosts"
                :popular-tags="$popularTags"
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
