@extends('layouts.site')

@section('title', $accommodation->name.' – '.$siteSettings->site_name)

@push('head')
    <link rel="stylesheet" href="{{ asset('css/site-accommodation-show.css') }}?v=1">
@endpush

@section('content')
    @php
        $bookingUrl = url('/foglalas-panel/foglalas/'.$accommodation->slug);
        $priceFrom = $accommodation->displayPriceFrom();
        $roomGroups = $accommodation->roomGalleryGroups();
    @endphp

    <section class="acc-hero">
        <img
            class="acc-hero__media"
            src="{{ $accommodation->coverUrl() }}"
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
                <a href="{{ $bookingUrl }}" class="btn-ghost">Foglalás erre a szállásra</a>
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
        <section class="acc-rooms section ts-gallery" aria-labelledby="acc-rooms-title">
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
@endsection
