<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $translation?->title ?? $post->slug }}</title>
</head>
<body>
    <main>
        <p>
            <a href="{{ route('press.index', ['locale' => $locale]) }}">Back to Press</a>
        </p>

        <article>
            @if ($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $translation?->title }}">
            @endif
            <h1>{{ $translation?->title ?? $post->slug }}</h1>
            <time datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ $post->published_at?->translatedFormat('j F Y') }}
            </time>
            <div>{!! $translation?->body !!}</div>
        </article>
    </main>
</body>
</html>
