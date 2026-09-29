@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $content = $page->content ?? [];
    $heroImage = $content['hero']['image'] ?? null;
    $isSecuredDocumentsStyle = isset($content['security']) || isset($content['services']);
@endphp

@section('content')
    <div class="product-page">
    <div class="product-hero" @if($heroImage) style="background-image: url('{{ asset('storage/'.$heroImage) }}');" @endif>
        <div class="container">
            <h1 class="product-hero__title" x-data x-intersect.once="$el.classList.add('is-visible')">{{ $page->title() }}</h1>
        </div>
    </div>

    <p class="product-intro container" x-data x-intersect.once="$el.classList.add('is-visible')">
        {{ $page->trans($content['introduction'] ?? null, $locale) }}
    </p>

    @if ($isSecuredDocumentsStyle)
        {{-- Secured Documents layout: two fixed bands + closing statement --}}
        @foreach (['security', 'services'] as $i => $key)
            @continue(! isset($content[$key]))
            <div class="product-band container product-band--{{ $i % 2 === 0 ? 'right' : 'left' }}">
                @if ($img = $content[$key]['image'] ?? null)
                    <img src="{{ asset('storage/'.$img) }}" alt="" class="product-band__image" x-data x-intersect.once="$el.classList.add('is-visible')">
                @endif
                <div class="product-band__text" x-data x-intersect.once="$el.classList.add('is-visible')">
                    <div class="product-band__description">{!! $page->trans($content[$key]['description'] ?? null, $locale) !!}</div>
                    <ul class="product-band__bullets">
                        @foreach ($content[$key]['bullets'] ?? [] as $bullet)
                            <li>{{ $page->trans($bullet, $locale) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach

        @if (isset($content['closing']))
            <div class="product-closing container" x-data x-intersect.once="$el.classList.add('is-visible')">
                {!! $page->trans($content['closing'], $locale) !!}
            </div>
        @endif
    @else
        {{-- Generic sections layout (Bank Card, and any future product using the same shape) --}}
        @foreach ($content['sections'] ?? [] as $i => $section)
            <div class="product-band container product-band--{{ $i % 2 === 0 ? 'right' : 'left' }}">
                @if ($img = $section['image'] ?? null)
                    <img src="{{ asset('storage/'.$img) }}" alt="" class="product-band__image" x-data x-intersect.once="$el.classList.add('is-visible')">
                @endif
                <div class="product-band__text" x-data x-intersect.once="$el.classList.add('is-visible')">
                    <h2 class="product-band__title">{{ $page->trans($section['title'] ?? null, $locale) }}</h2>
                    @if (! empty($section['description']))
                        <div class="product-band__description">{!! $page->trans($section['description'], $locale) !!}</div>
                    @endif
                    <ul class="product-band__bullets">
                        @foreach ($section['bullets'] ?? [] as $bullet)
                            <li>{{ $page->trans($bullet, $locale) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @endif
    </div>
@endsection