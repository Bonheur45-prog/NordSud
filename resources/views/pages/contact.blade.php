@extends('layouts.app')

@php
    // Every value below comes from the ContactByCountryPage admin editor
    // (content.hero / content.addresses / content.telephones / content.form).
    // Nothing here is hardcoded — if a section looks empty on the live site,
    // it means that repeater hasn't been filled in yet in /admin, not a bug.
    $content = $page?->content ?? [];
    $heroImage = data_get($content, 'hero.image');
    $addresses = data_get($content, 'addresses', []);
    $telephones = data_get($content, 'telephones', []);
    $formIntro = $page?->trans(data_get($content, 'form.introduction'));
    $formFields = data_get($content, 'form.fields', []);

    // Known submission columns (see contact_submissions migration). An
    // admin can relabel/reorder/hide these via the repeater, but adding a
    // brand new field key beyond this set won't be captured by the
    // controller yet — see the note left in the chat about this gap.
    $requiredKeys = ['name', 'email', 'message'];
@endphp

@section('content')
    <div class="contact-page-shell">
    <div class="contact-hero" @if ($heroImage) style="background-image: url('{{ asset('storage/'.$heroImage) }}');" @endif>
        <div class="contact-hero__pattern"></div>
        <div class="container">
            <div class="contact-hero__inner">
                <h1 class="contact-hero__title">{{ $page?->title() ?? t('contact.title') }}</h1>
            </div>
        </div>
    </div>

    <div class="container contact-page">
        <div class="contact-layout">
            <aside class="contact-sidebar">
                @if ($addresses)
                    <section class="contact-section contact-section--sidebar">
                        <h2 class="contact-section__heading">{{ t('contact.addresses_heading') }}</h2>
                        <div class="contact-addresses">
                            @foreach ($addresses as $entry)
                                <div class="contact-address">
                                    <div class="contact-address__icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M12 21s6-5.686 6-11a6 6 0 1 0-12 0c0 5.314 6 11 6 11Zm0-8.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <div class="contact-address__body">
                                        <div class="contact-card__country">{{ $page->trans(data_get($entry, 'country')) }}</div>
                                        <div class="contact-card__body">{{ $page->trans(data_get($entry, 'address')) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($telephones)
                    <section class="contact-section contact-section--sidebar">
                        <h2 class="contact-section__heading contact-section__heading--small">{{ t('contact.telephones_heading') }}</h2>
                        <div class="contact-phones">
                            @foreach ($telephones as $entry)
                                <div class="contact-phone-item">
                                    <span class="contact-phone-item__icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M6.6 10.8a15.43 15.43 0 0 0 6.6 6.6l2.2-2.2a1.1 1.1 0 0 1 1.13-.26c1.24.41 2.58.63 3.94.63a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.85 21 3 13.15 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.36.22 2.7.63 3.94.12.4.05.83-.27 1.12L6.6 10.8Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <div class="contact-phone-item__text">
                                        <span class="contact-phone-item__label">{{ $page->trans(data_get($entry, 'country')) }}</span>
                                        <span class="contact-phone-item__value">{{ $page->trans(data_get($entry, 'details')) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>

            <section class="contact-panel">
                @if ($formIntro)
                    <p class="contact-form__intro">{{ $formIntro }}</p>
                @endif

                @if (session('success'))
                    <p class="contact-alert contact-alert--success" role="status">{{ session('success') }}</p>
                @endif

                @if ($errors->any())
                    <div class="contact-alert contact-alert--error" role="alert">
                        <p>{{ t('contact.form_errors_heading') }}</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store', ['locale' => $locale]) }}" class="contact-form">
                    @csrf

                    <div class="contact-form__grid">
                        @forelse ($formFields as $field)
                            @php
                                $key = data_get($field, 'key');
                                $label = $page->trans(data_get($field, 'label'));
                                $placeholder = data_get($field, 'placeholder_'.$locale) ?: data_get($field, 'placeholder_fr');
                                $required = in_array($key, $requiredKeys, true);
                                $fieldClass = in_array($key, ['name', 'email'], true) ? 'contact-form__field--half' : 'contact-form__field--full';
                            @endphp

                            @if ($key)
                                <div class="contact-form__field {{ $fieldClass }}">
                                    <label for="{{ $key }}">{{ $label }}@if ($required) *@endif</label>

                                    @if ($key === 'message')
                                        <textarea
                                            id="{{ $key }}"
                                            name="{{ $key }}"
                                            placeholder="{{ $placeholder }}"
                                            maxlength="10000"
                                            @required($required)
                                        >{{ old($key) }}</textarea>
                                    @else
                                        <input
                                            id="{{ $key }}"
                                            name="{{ $key }}"
                                            type="{{ $key === 'email' ? 'email' : 'text' }}"
                                            value="{{ old($key) }}"
                                            placeholder="{{ $placeholder }}"
                                            maxlength="255"
                                            @required($required)
                                        >
                                    @endif
                                </div>
                            @endif
                        @empty
                            {{-- Admin hasn't configured any form fields yet — fall back to
                                 the minimum viable form so the page still works. --}}
                            <div class="contact-form__field contact-form__field--half">
                                <label for="name">{{ t('contact.field_name') }} *</label>
                                <input id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                            </div>
                            <div class="contact-form__field contact-form__field--half">
                                <label for="email">{{ t('contact.field_email') }} *</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255">
                            </div>
                            <div class="contact-form__field contact-form__field--full">
                                <label for="message">{{ t('contact.field_message') }} *</label>
                                <textarea id="message" name="message" required maxlength="10000">{{ old('message') }}</textarea>
                            </div>
                        @endforelse
                    </div>

                    <div class="contact-form__actions">
                        <button type="submit" class="contact-form__submit">{{ t('contact.submit') }}</button>
                    </div>
                </form>
            </section>
        </div>

    </div>
    </div>
@endsection
