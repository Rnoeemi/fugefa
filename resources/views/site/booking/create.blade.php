@extends('layouts.site')

@section('title', 'Foglalás – Tüsiszállás')

@section('content')
    <section class="bg-pine-deep pt-28 text-white">
        <div class="section-narrow px-5 pb-12 md:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/50">Foglalás</p>
            <h1 class="mt-3 font-display text-4xl md:text-5xl">Időpontfoglalás</h1>
            <p class="mt-4 max-w-2xl text-white/70">
                Válassza ki a szállást és az időszakot. Egyes napokon a naptár jelzi a foglaltságot.
                A munkásszállás csak adminisztrátoron keresztül rögzíthető.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="section-narrow grid gap-12 lg:grid-cols-[1fr_1.1fr]">
            <div class="reveal" data-booking-calendar>
                <h2 class="font-display text-2xl text-pine">Naptár</h2>
                <p class="mt-2 text-sm text-ink/65">Először válassza ki az érkezés, majd a távozás napját.</p>

                <div class="mt-6 flex items-center justify-between gap-3">
                    <button type="button" class="btn-dark !px-3 !py-2 !text-xs" data-cal-prev>‹</button>
                    <p class="font-display text-xl text-pine capitalize" data-month-label></p>
                    <button type="button" class="btn-dark !px-3 !py-2 !text-xs" data-cal-next>›</button>
                </div>

                <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs font-semibold uppercase tracking-wide text-ink/45">
                    <span>H</span><span>K</span><span>Sze</span><span>Cs</span><span>P</span><span>Szo</span><span>V</span>
                </div>
                <div class="cal-grid mt-2" data-cal-grid></div>

                <div class="mt-4 flex flex-wrap gap-4 text-xs text-ink/55">
                    <span>Szabad nap kattintható</span>
                    <span class="line-through opacity-70">Foglalt</span>
                </div>
            </div>

            <div class="reveal border border-pine/10 bg-white p-6 md:p-8">
                <p class="text-sm uppercase tracking-[0.18em] text-brass">Új foglalási felület</p>
                <h2 class="mt-3 font-display text-2xl text-pine">A foglalás a paneles felületen érhető el</h2>
                <p class="mt-3 text-sm text-ink/70">
                    A korábbi publikus űrlap helyett mostantól a `/foglalas-panel/foglalas` útvonal szolgálja ki az online foglalást.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($selected)
                        <a href="{{ url('/foglalas-panel/foglalas/'.$selected->slug) }}" class="btn-primary">Tovább a kiválasztott szálláshoz</a>
                    @else
                        <a href="{{ url('/foglalas-panel/foglalas') }}" class="btn-primary">Tovább a foglalási panelre</a>
                    @endif
                    <a href="{{ route('accommodations.index') }}" class="btn-dark">Szállások megtekintése</a>
                </div>
            </div>
        </div>
    </section>
@endsection
