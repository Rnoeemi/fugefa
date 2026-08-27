@extends('layouts.site')

@section('title', 'Szállások – '.$siteSettings->site_name)

@push('head')
    <style>{!! \App\Support\GrapesJs\SiteDynamicBlockStyles::css() !!}</style>
@endpush

@section('content')
    <section class="bg-pine-deep pt-28 text-white">
        <div class="section-narrow px-5 pb-12 md:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/50">Szálláshelyeink</p>
            <h1 class="mt-3 font-display text-4xl md:text-5xl">Vendégház és szobák</h1>
            <p class="mt-4 max-w-2xl text-white/70">
                Válassza ki a megfelelő egységet, majd foglaljon online. A munkásszállás csak telefonon / adminisztrátoron keresztül érhető el.
            </p>
        </div>
    </section>

    <section class="ts-dyn-cards">
        <div class="ts-dyn-cards__inner">
            <div class="ts-dyn-cards__grid">
                @forelse ($accommodations as $accommodation)
                    <article class="ts-dyn-card reveal">
                        <a class="ts-dyn-card__media" href="{{ route('accommodations.show', $accommodation) }}">
                            <img src="{{ $accommodation->coverUrl() }}" alt="{{ $accommodation->name }}" loading="lazy">
                        </a>
                        <div class="ts-dyn-card__body">
                            <p class="ts-dyn-card__type">{{ $accommodation->type->getLabel() }}</p>
                            <h2 class="ts-dyn-card__name">
                                <a href="{{ route('accommodations.show', $accommodation) }}">{{ $accommodation->name }}</a>
                            </h2>
                            <p class="ts-dyn-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags($accommodation->description ?? ''), 140) }}</p>
                            @if ($accommodation->displayPriceFrom())
                                <p class="ts-dyn-card__price">
                                    <span class="ts-dyn-card__price-amount">{{ number_format($accommodation->displayPriceFrom(), 0, ',', ' ') }} Ft</span>
                                    <span class="ts-dyn-card__price-unit">/ éj</span>
                                    @if ($accommodation->min_nights)
                                        <span class="ts-dyn-card__price-note">min. {{ $accommodation->min_nights }} éj</span>
                                    @endif
                                </p>
                            @endif
                            <div class="ts-dyn-card__actions">
                                <a class="ts-dyn-card__cta" href="{{ route('accommodations.show', $accommodation) }}">Részletek</a>
                                <a class="ts-dyn-card__cta ts-dyn-card__cta--outline" href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}">Foglalás</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="ts-dyn-empty">Jelenleg nincs nyilvánosan foglalható szállás.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
