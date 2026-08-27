@extends('layouts.site')

@section('title', 'Köszönjük – Tüsiszállás')

@section('content')
    <section class="section pt-32">
        <div class="section-narrow max-w-2xl reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brass">Foglalás elküldve</p>
            <h1 class="section-title mt-3">Köszönjük a foglalási igényét!</h1>
            <p class="section-lead">
                Hamarosan visszajelzünk e-mailben vagy telefonon. Az alábbi adatokat rögzítettük:
            </p>

            <dl class="mt-8 space-y-3 border border-pine/10 bg-white p-6 text-sm">
                <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                    <dt class="text-ink/55">Szállás</dt>
                    <dd class="font-medium text-pine">{{ $booking->accommodation?->name }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                    <dt class="text-ink/55">Érkezés</dt>
                    <dd class="font-medium text-pine">{{ $booking->check_in->format('Y.m.d.') }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                    <dt class="text-ink/55">Távozás</dt>
                    <dd class="font-medium text-pine">{{ $booking->check_out->format('Y.m.d.') }}</dd>
                </div>
                    <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                    <dt class="text-ink/55">Vendégek</dt>
                    <dd class="font-medium text-pine">{{ $booking->guests_count }} fő</dd>
                </div>
                @if ($booking->total_price)
                    <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                        <dt class="text-ink/55">Szállás</dt>
                        <dd class="font-medium text-pine">{{ number_format((float) $booking->accommodation_total, 0, ',', ' ') }} Ft</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-pine/10 pb-3">
                        <dt class="text-ink/55">IFA</dt>
                        <dd class="font-medium text-pine">{{ number_format((float) $booking->ifa_total, 0, ',', ' ') }} Ft</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink/55">Összesen</dt>
                        <dd class="font-medium text-pine">{{ number_format((float) $booking->total_price, 0, ',', ' ') }} Ft</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="btn-dark">Vissza a főoldalra</a>
                <a href="{{ route('contact') }}" class="btn-primary">Kapcsolat</a>
            </div>
        </div>
    </section>
@endsection
