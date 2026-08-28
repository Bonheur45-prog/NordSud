<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Press</title>
</head>
<body>
    <main>
        <h1>Press</h1>

        @forelse ($posts as $post)
            @php($translation = $post->translation($locale))
            <article>
                @if ($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $translation?->title }}">
                @endif
                <h2>
                    <a href="{{ route('press.show', ['locale' => $locale, 'slug' => $post->slug]) }}">
                        {{ $translation?->title ?? $post->slug }}
                    </a>
                </h2>
                @if ($translation?->excerpt)
                    <p>{{ $translation->excerpt }}</p>
                @endif
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->translatedFormat('j F Y') }}
                </time>
            </article>
        @empty
            <p>No press articles are available yet.</p>
        @endforelse
    </main>
</body>
</html>
