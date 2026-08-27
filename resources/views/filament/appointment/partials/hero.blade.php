@php
    $brandName = ($siteSettings->site_name ?? null) ?: 'Tüsiszállás';
@endphp

<section class="appointment-booking-hero">
    <div class="appointment-booking-hero-inner">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/50">{{ $brandName }}</p>
        <h1 class="mt-3 font-display text-4xl md:text-5xl">Időpontfoglalás</h1>
        <p class="mt-4 max-w-2xl text-white/70">
            Válasszon munkatársat és szabad időpontot. A már foglalt sávok nem választhatók.
        </p>
    </div>
</section>
