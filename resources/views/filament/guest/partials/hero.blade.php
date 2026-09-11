@php
    $brandName = ($siteSettings->site_name ?? null) ?: 'Tüsiszállás';
@endphp

<section class="guest-booking-hero">
    <div class="guest-booking-hero-inner">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] opacity-55">{{ $brandName }}</p>
        <h1 class="mt-3 font-display text-4xl md:text-5xl">Online foglalás</h1>
        <p class="mt-4 max-w-2xl opacity-70">
            Válassza ki a szállást és az időszakot. A foglalt napok a naptárban és a listában is megjelennek.
            Minden 14 év feletti vendéghez személyi előlap, hátlap és lakcímkártya előlap szükséges.
        </p>
    </div>
</section>
