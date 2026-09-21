@extends('layouts.site')

@section('title', $accommodation->name.' – '.$siteSettings->site_name)

@push('head')
    <link rel="stylesheet" href="{{ asset('css/site-accommodation-show.css') }}?v=7">
    <style>{!! \App\Support\GrapesJs\SiteDynamicBlockStyles::css() !!}</style>
@endpush

@section('content')
    @php
        $bookingUrl = url('/foglalas-panel/foglalas/'.$accommodation->slug);
        $priceFrom = $accommodation->displayPriceFrom();
        $roomGroups = $accommodation->roomGalleryGroups();
        $mapQuery = filled($accommodation->map_embed_url)
            ? $accommodation->map_embed_url
            : (filled($accommodation->address)
                ? $accommodation->address
                : (filled($siteSettings->address) ? $siteSettings->address : ''));
        $mapEmbedUrl = \App\Support\GoogleMapsEmbed::normalize($mapQuery);
        $mapAddress = filled($accommodation->address)
            ? $accommodation->address
            : (filled($siteSettings->address) ? $siteSettings->address : null);
    @endphp

    <section class="acc-hero">
        <img
            class="acc-hero__media"
            src="{{ $accommodation->heroUrl() }}"
            alt="{{ $accommodation->name }}"
        >
        <div class="acc-hero__shade" aria-hidden="true"></div>
        <div class="acc-hero__inner">
            <p class="acc-hero__eyebrow">{{ $accommodation->type->getLabel() }}</p>
            <h1 class="acc-hero__title">{{ $accommodation->name }}</h1>
            @if (filled($accommodation->type->getDescription()))
                <p class="acc-hero__lead">{{ $accommodation->type->getDescription() }}</p>
            @endif
            <ul class="acc-hero__meta" aria-label="Főbb adatok">
                <li>{{ $accommodation->capacity }} fő</li>
                @if ($priceFrom)
                    <li>{{ number_format($priceFrom, 0, ',', ' ') }} Ft / éj</li>
                @endif
                @if ($accommodation->min_nights)
                    <li>min. {{ $accommodation->min_nights }} éjszaka</li>
                @endif
            </ul>
            <div class="acc-hero__actions">
                <a href="{{ $bookingUrl }}" class="btn-ghost">Foglalás</a>
                @if (count($roomGroups))
                    <a href="#galeria" class="btn-ghost btn-ghost--outline">Galéria</a>
                @endif
                <a href="#terkep" class="btn-ghost btn-ghost--outline">Térkép</a>
                <a href="#kapcsolat" class="btn-ghost btn-ghost--outline">Kapcsolat</a>
            </div>
        </div>
    </section>

    <section class="acc-main section">
        <div class="section-narrow acc-main__grid">
            <div class="acc-copy reveal">
                <h2 class="section-title">Leírás</h2>
                <div class="acc-copy__body">
                    {{ $accommodation->description ?: 'A részletes leírás hamarosan elérhető.' }}
                </div>
            </div>

            <aside class="acc-aside reveal" aria-label="Árak és jellemzők">
                <div class="acc-aside__panel">
                    <div class="acc-aside__block acc-aside__block--price">
                        <p class="acc-aside__kicker">Árak</p>
                        @if ($priceFrom)
                            <p class="acc-aside__price">
                                <span class="acc-aside__price-amount">{{ number_format($priceFrom, 0, ',', ' ') }} Ft</span>
                                <span class="acc-aside__price-unit">/ éj</span>
                            </p>
                        @endif
                        <dl class="acc-aside__facts">
                            <div>
                                <dt>Alapár</dt>
                                <dd>{{ number_format((float) ($accommodation->base_price ?? 0), 0, ',', ' ') }} Ft / éj</dd>
                            </div>
                            <div>
                                <dt>IFA</dt>
                                <dd>{{ number_format((float) $accommodation->ifa_per_person_night, 0, ',', ' ') }} Ft / fő / éj</dd>
                            </div>
                            <div>
                                <dt>Kapacitás</dt>
                                <dd>{{ $accommodation->capacity }} fő</dd>
                            </div>
                            <div>
                                <dt>Min. éjszakák</dt>
                                <dd>{{ $accommodation->min_nights }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="acc-aside__block">
                        <h3 class="acc-aside__heading">Jellemzők</h3>
                        @if (! empty($accommodation->amenities))
                            <ul class="acc-aside__amenities">
                                @foreach ($accommodation->amenities as $amenity)
                                    <li>{{ $amenity }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="acc-aside__empty">A jellemzők feltöltése folyamatban.</p>
                        @endif
                    </div>

                    <div class="acc-aside__block acc-aside__block--cta">
                        <a href="{{ $bookingUrl }}" class="btn-primary acc-aside__cta">Foglalás</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @if (count($roomGroups))
        <section id="galeria" class="acc-rooms section ts-gallery" aria-labelledby="acc-rooms-title">
            <div class="section-narrow">
                <header class="acc-rooms__header reveal">
                    <p class="acc-rooms__eyebrow">Galéria</p>
                    <h2 id="acc-rooms-title" class="section-title">Szobák képei</h2>
                    <p class="section-lead">Az ehhez a szálláshoz tartozó szobák és apartmanok hangulatképei.</p>
                </header>

                <div class="acc-rooms__groups">
                    @foreach ($roomGroups as $group)
                        <article class="acc-room reveal">
                            <header class="acc-room__head">
                                <h3 class="acc-room__title">{{ $group['name'] }}</h3>
                                @if ($group['capacity'])
                                    <p class="acc-room__meta">{{ $group['capacity'] }} fő</p>
                                @endif
                            </header>
                            <div class="acc-room__grid">
                                @foreach ($group['images'] as $image)
                                    <figure class="acc-room__figure">
                                        <button
                                            type="button"
                                            class="ts-gallery__trigger acc-room__trigger"
                                            data-ts-gallery-src="{{ $image['url'] }}"
                                            aria-label="Kép nagyítása: {{ $image['alt'] }}"
                                        >
                                            <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                        </button>
                                    </figure>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="terkep" class="acc-contact section" aria-labelledby="acc-contact-title">
        <div class="section-narrow">
            <header class="acc-contact__header reveal">
                <h2 id="acc-contact-title" class="section-title">Kapcsolat</h2>
            </header>

            <div class="acc-contact__grid reveal">
                <div class="acc-contact__map">
                    @if (filled($mapEmbedUrl))
                        <div class="acc-contact__map-frame">
                            <iframe
                                src="{{ $mapEmbedUrl }}"
                                title="Térkép – {{ $accommodation->name }}"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @else
                        <div class="acc-contact__map-empty">
                            <p>A térkép hamarosan elérhető. Az adminban a szállásnál megadható a pontos cím vagy Google Maps link.</p>
                        </div>
                    @endif
                </div>

                <aside class="acc-contact__info" aria-label="Elérhetőségek">
                    @if (filled($mapAddress))
                        <div class="acc-contact__info-row">
                            <span class="acc-contact__info-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11Z"/>
                                    <circle cx="12" cy="10" r="2.5"/>
                                </svg>
                            </span>
                            <div class="acc-contact__info-body">
                                <span class="acc-contact__info-label">Cím</span>
                                <p class="acc-contact__info-value">{{ $mapAddress }}</p>
                            </div>
                        </div>
                    @endif
                    @if (filled($siteSettings->phone))
                        <div class="acc-contact__info-row">
                            <span class="acc-contact__info-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.9v2.1a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 1h2.1a2 2 0 0 1 2 1.7c.1.9.3 1.8.7 2.6a2 2 0 0 1-.5 2.1L7.1 8.7a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.8.3 1.7.5 2.6.7a2 2 0 0 1 1.7 2Z"/>
                                </svg>
                            </span>
                            <div class="acc-contact__info-body">
                                <span class="acc-contact__info-label">Telefon</span>
                                <p class="acc-contact__info-value">
                                    <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}">{{ $siteSettings->phone }}</a>
                                </p>
                            </div>
                        </div>
                    @endif
                    @if (filled($siteSettings->email))
                        <div class="acc-contact__info-row">
                            <span class="acc-contact__info-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="m4 7 8 6 8-6"/>
                                </svg>
                            </span>
                            <div class="acc-contact__info-body">
                                <span class="acc-contact__info-label">E-mail</span>
                                <p class="acc-contact__info-value">
                                    <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a>
                                </p>
                            </div>
                        </div>
                    @endif
                </aside>

                <div id="kapcsolat" class="acc-contact__form ts-dyn-contact-form">
                    @include('site.dynamic.contact-form', [
                        'title' => '',
                        'text' => '',
                        'button' => 'küldés',
                        'privacyHref' => '/oldal/adatkezelesi-tajekoztato',
                        'settings' => $siteSettings,
                        'showTitle' => false,
                        'showText' => false,
                        'showButton' => true,
                        'showInfo' => false,
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection
