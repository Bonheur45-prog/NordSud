{{--
    Home page "Show" — cinematic storytelling slider shown in the video section.
    Expects: $cfg (digital_banking content array), $page, $locale.
    Every scene's text is rendered here as real HTML (first for search engines,
    then enhanced by resources/js/home-show.js). Styling: components/home-show.css
--}}
@php
    $pick = fn ($value, array $allowed, string $default) => in_array($value, $allowed, true) ? $value : $default;
    $hl = fn (?string $text) => preg_replace('/\[([^\]]+)\]/u', '<em class="hs-hl">$1</em>', e((string) $text));
    $fr = $locale === 'fr';

    $scenes = collect(data_get($cfg, 'show_scenes', []))
        ->filter(fn ($s) => filled($page->trans(data_get($s, 'headline'))))
        ->values();

    $size = $pick(data_get($cfg, 'show_size'), ['medium', 'large', 'full'], 'large');
    $musicOn = (bool) data_get($cfg, 'show_music', true);
    $musicFile = data_get($cfg, 'show_music_file');

    $labels = [
        'pause' => $fr ? 'Pause' : 'Pause',
        'play' => $fr ? 'Lecture' : 'Play',
        'replay' => $fr ? 'Rejouer' : 'Replay',
        'soundOn' => $fr ? 'Activer le son' : 'Turn sound on',
        'soundOff' => $fr ? 'Couper le son' : 'Turn sound off',
        'hint' => $fr ? 'Cliquez pour le son' : 'Click for sound',
        'chapters' => $fr ? 'Chapitres' : 'Chapters',
        'tagline' => $fr ? 'Intégrateur panafricain de solutions monétiques' : 'Pan-African payment solutions integrator',
    ];
@endphp

@if ($scenes->isNotEmpty())
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap">
    <noscript><style>#home-show .hs-sc:first-of-type{visibility:visible;opacity:1}</style></noscript>

    <div class="hs hs--{{ $size }}" id="home-show"
         role="region" aria-roledescription="carousel"
         aria-label="{{ $page->trans(data_get($cfg, 'headline')) ?: 'NSI Group' }}"
         data-logo="{{ asset('images/nsi-logo-light.png') }}" data-tag="{{ $labels['tagline'] }}"
         style="--hs-logo: url('{{ asset('images/nsi-logo-light.png') }}');"
         data-music="{{ $musicOn ? '1' : '0' }}"
         @if ($musicFile) data-file="{{ asset('storage/'.$musicFile) }}" @endif
         data-l='@json($labels)'>
        <div class="hs-grain"></div>
        <div class="hs-sweep"></div>

        @foreach ($scenes as $i => $scene)
            @php
                $chapter = $page->trans(data_get($scene, 'chapter')) ?: '';
                $motion = $pick(data_get($scene, 'motion'), ['zi', 'zo', 'pl', 'pr', 'st'], 'zi');
                $transition = $pick(data_get($scene, 'transition'), ['fade', 'focus', 'zoom', 'wipe'], 'fade');
                $anim = $pick(data_get($scene, 'text_animation'), ['mask', 'fade', 'slide', 'words'], 'mask');
                $align = $pick(data_get($scene, 'align'), ['l', 'c', 'r'], 'l');
                $layout = $pick(data_get($scene, 'layout'), ['full', 'split'], 'full'); // old 'card' scenes render as 'full'
                $logo = data_get($scene, 'logo');
                $bgStyle = $pick(data_get($scene, 'background'), ['net', 'doc', 'code'], 'net');
                $focal = $pick(data_get($scene, 'focal'), ['center', 'top', 'bottom', 'left', 'right'], 'center');
                $duration = max(3, min(20, (float) (data_get($scene, 'duration') ?: 6.5)));
                $image = data_get($scene, 'image');
                $subline = $page->trans(data_get($scene, 'subline'));
                $ctaLabel = $page->trans(data_get($scene, 'cta_label'));
                $ctaLink = data_get($scene, 'cta_link');
            @endphp
            <section class="hs-sc hs-a-{{ $align }}{{ $layout === 'split' ? ' hs-split' : '' }}{{ $logo ? ' hs-hb' : '' }}"
                     data-a="{{ $anim }}" data-t="{{ $transition }}" data-chapter="{{ $chapter }}" data-dur="{{ $duration }}"
                     aria-hidden="true"
                     style="--d: {{ $duration }}s; --m: hs-{{ $motion }}; --fp: {{ $focal }};">
                <div class="hs-fx">
                    <div class="hs-bg{{ $image ? '' : ' hs-'.$bgStyle }}"
                         @if ($image) style="background-image: url('{{ asset('storage/'.$image) }}');" @endif></div>
                    <div class="hs-shade"></div>
                </div>

                @if ($logo)
                    <div class="hs-lwrap">
                        <div class="hs-logo"><img src="{{ asset('storage/'.$logo) }}" alt="{{ str_replace(['[', ']'], '', (string) $page->trans(data_get($scene, 'headline'))) }}"></div>
                    </div>
                @endif

                <div class="hs-tx">
                    <p class="hs-kk">{{ sprintf('%02d', $i + 1) }}{{ $chapter !== '' ? ' — '.$chapter : '' }}</p>
                    <h3>{!! $hl($page->trans(data_get($scene, 'headline'))) !!}</h3>
                    @if ($subline)
                        <p class="hs-sub" @if (data_get($scene, 'subline_style') === 'type') data-type @endif>{{ $subline }}</p>
                    @endif
                    @if ($ctaLabel && $ctaLink)
                        <a class="hs-cta" href="{{ $resolveLink($ctaLink) }}">{{ $ctaLabel }}</a>
                    @endif
                </div>
            </section>
        @endforeach
    </div>
@endif
