@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $content = $page->content ?? [];
    $heroImage = $content['hero']['image'] ?? null;
    $isChecklistStyle = isset($content['points']);
    $introHasHeading = is_array($content['introduction']['heading'] ?? null);
@endphp

@section('content')
    <div class="product-page">
    <div class="product-hero" @if($heroImage) style="background-image: url('{{ asset('storage/'.$heroImage) }}');" @endif>
        <div class="container">
            <h1 class="product-hero__title" x-data x-intersect.once="$el.classList.add('is-visible')">{{ $page->title() }}</h1>
        </div>
    </div>

    @if ($isChecklistStyle)
        {{-- Software Expertise layout: heading + intro text + checklist + single image --}}
        <div class="solution-checklist container">
            <div class="solution-checklist__text" x-data x-intersect.once="$el.classList.add('is-visible')">
                <h2 class="product-band__title">{{ $page->trans($content['introduction']['heading'] ?? null, $locale) }}</h2>
                <div class="product-band__description">{!! $page->trans($content['introduction']['text'] ?? null, $locale) !!}</div>
                <ul class="solution-checklist__points">
                    @foreach ($content['points'] ?? [] as $point)
                        <li>
                            <svg class="solution-checklist__icon" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 10.5L8 14.5L16 5.5" stroke="var(--nsi-blue, #1d5fae)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ $page->trans($point, $locale) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @if ($img = $content['image'] ?? null)
                <img src="{{ asset('storage/'.$img) }}" alt="" class="solution-checklist__image" x-data x-intersect.once="$el.classList.add('is-visible')">
            @endif
        </div>
    @else
        {{-- Generic sections layout for solution pages. --}}
        <div class="product-intro container" x-data x-intersect.once="$el.classList.add('is-visible')">
            @if ($introHasHeading)
                <h2 class="product-band__title">{{ $page->trans($content['introduction']['heading'] ?? null, $locale) }}</h2>
                <div class="product-band__description">{!! $page->trans($content['introduction']['text'] ?? null, $locale) !!}</div>
            @else
                <p>{{ $page->trans($content['introduction'] ?? null, $locale) }}</p>
            @endif
        </div>

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
