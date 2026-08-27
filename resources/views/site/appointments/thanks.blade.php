@extends('layouts.site')

@section('title', 'Időpont rögzítve')
@section('meta_description', 'Az időpontfoglalás sikeresen rögzítve.')

@section('content')
    <section class="section bg-white">
        <div class="section-narrow max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brass">Köszönjük</p>
            <h1 class="section-title mt-3">Időpont rögzítve</h1>
            <p class="section-lead mt-4">
                {{ $appointment->starts_at->format('Y.m.d. H:i') }}
                – {{ $appointment->ends_at->format('H:i') }}
                · {{ $appointment->worker?->name }}
            </p>
            @if ($appointment->package_name)
                <p class="mt-2 text-ink/80">
                    Csomag: {{ $appointment->package_name }}
                    @if ($appointment->duration_minutes)
                        ({{ $appointment->duration_minutes }} perc)
                    @endif
                    @if ($appointment->formattedPrice())
                        · {{ $appointment->formattedPrice() }}
                    @endif
                </p>
            @endif
            <p class="mt-4 text-ink/70">
                Hamarosan visszajelzünk a megadott elérhetőségeken:
                {{ $appointment->customer_email }}
                @if ($appointment->customer_phone)
                    · {{ $appointment->customer_phone }}
                @endif
            </p>
            <p class="mt-3 text-sm text-ink/55">Online fizetés nincs – a díjat a helyszínen rendezheti.</p>
            <div class="mt-8">
                <a href="{{ route('home') }}" class="btn-dark">Vissza a főoldalra</a>
            </div>
        </div>
    </section>
@endsection
