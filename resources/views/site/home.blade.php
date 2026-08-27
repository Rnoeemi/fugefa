@extends('layouts.site')

@section('title', 'Tüsiszállás – Vendégház és szobák')
@section('meta_description', 'Pihenjen a Tüsiszálláson: vendégház és szoba foglalás nyugodt környezetben.')

@section('content')
    <section class="hero" style="background-image: linear-gradient(180deg, rgba(15, 41, 32, 0.28) 0%, rgba(15, 41, 32, 0.78) 70%, rgba(15, 41, 32, 0.94) 100%), url('{{ $siteSettings->heroUrl() }}'); background-size: cover; background-position: center;">
        <div class="hero-grain" aria-hidden="true"></div>

        <div class="section-narrow relative z-10 w-full px-5 pb-16 pt-36 md:px-8 md:pb-24 md:pt-44">
            <p class="reveal font-display text-5xl leading-[0.95] text-white md:text-7xl lg:text-8xl">
                {{ $siteSettings->site_name }}
            </p>
            <h1 class="reveal mt-5 max-w-xl text-lg font-medium text-white/90 md:text-2xl" style="transition-delay: 120ms">
                Élvezze a pihenést – vendégház és szobák egy helyen.
            </h1>
            <p class="reveal mt-4 max-w-lg text-sm leading-relaxed text-white/70 md:text-base" style="transition-delay: 200ms">
                Foglaljon néhány kattintással, vagy kérjen személyre szabott ajánlatot.
            </p>
            <div class="reveal mt-8 flex flex-wrap gap-3" style="transition-delay: 280ms">
                @if ($accommodationModuleEnabled ?? true)
                    <a href="{{ url('/foglalas-panel/foglalas') }}" class="btn-primary">Időpontfoglalás</a>
                    <a href="#szallasok" class="btn-ghost">Szálláshelyeink</a>
                @else
                    <a href="{{ route('contact') }}" class="btn-primary">Kapcsolat</a>
                @endif
            </div>
        </div>
    </section>

    <section class="section bg-white" id="rolunk">
        <div class="section-narrow reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brass">Rólunk</p>
            <h2 class="section-title mt-3">Nyugalom, amelyre emlékezni fog</h2>
            <p class="section-lead">
                A Tüsiszállás vendégeinek a teljes ház vagy külön szoba is elérhető.
                Családi pihenéshez, hosszabb tartózkodáshoz és csendes kikapcsolódáshoz egyaránt.
            </p>

            <div class="amenity-row mt-10">
                <span>Ingyenes Wi‑Fi</span>
                <span>Parkoló</span>
                <span>Felszerelt konyha</span>
                <span>Klímás szobák</span>
                <span>Kerti pihenő</span>
            </div>
        </div>
    </section>

    @if ($accommodationModuleEnabled ?? true)
    <section class="section" id="szallasok">
        <div class="section-narrow">
            <div class="reveal flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brass">Szálláshelyeink</p>
                    <h2 class="section-title mt-3">Válassza ki a megfelelő típust</h2>
                    <p class="section-lead">Vendégház a teljes ház bérléséhez, szoba egyéni vagy páros tartózkodáshoz.</p>
                </div>
                <a href="{{ route('accommodations.index') }}" class="btn-dark shrink-0 self-start md:self-auto">Összes szállás</a>
            </div>

            <div class="mt-12 grid gap-10 md:grid-cols-2">
                @forelse ($accommodations as $accommodation)
                    <article class="reveal border-t border-pine/15 pt-6">
                        <a href="{{ route('accommodations.show', $accommodation) }}" class="mb-5 block overflow-hidden">
                            <img
                                src="{{ $accommodation->coverUrl() }}"
                                alt="{{ $accommodation->name }}"
                                class="aspect-[4/3] w-full object-cover"
                                loading="lazy"
                            >
                        </a>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-moss">
                            {{ $accommodation->type->getLabel() }}
                        </p>
                        <h3 class="mt-2 font-display text-2xl text-pine md:text-3xl">
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="hover:text-moss">
                                {{ $accommodation->name }}
                            </a>
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-ink/70">
                            {{ \Illuminate\Support\Str::limit(strip_tags($accommodation->description ?? $accommodation->type->getDescription()), 160) }}
                        </p>
                        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-ink/80">
                            <span>{{ $accommodation->capacity }} fő</span>
                            @if ($accommodation->displayPriceFrom())
                                <span>tól {{ number_format($accommodation->displayPriceFrom(), 0, ',', ' ') }} Ft / éj</span>
                            @endif
                            <span>min. {{ $accommodation->min_nights }} éj</span>
                            <a href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}" class="font-semibold text-brass hover:text-pine">
                                Foglalás →
                            </a>
                        </div>
                    </article>
                @empty
                    <p class="reveal text-ink/70 md:col-span-2">
                        A szállások feltöltése folyamatban van. Addig is kérjük, keressen minket a kapcsolat menüponton.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-pine py-20 text-white">
        <div class="hero-grain opacity-30" aria-hidden="true"></div>
        <div class="section-narrow relative z-10 px-5 text-center md:px-8">
            <h2 class="reveal font-display text-3xl md:text-5xl">Foglaljon nálunk most</h2>
            <p class="reveal mx-auto mt-4 max-w-xl text-white/75">
                Néhány perc alatt elküldheti foglalási igényét – visszaigazolásunkkal hamarosan jelentkezünk.
            </p>
            <div class="reveal mt-8">
                <a href="{{ url('/foglalas-panel/foglalas') }}" class="btn-primary">Foglalás indítása</a>
            </div>
        </div>
    </section>
    @endif
@endsection
