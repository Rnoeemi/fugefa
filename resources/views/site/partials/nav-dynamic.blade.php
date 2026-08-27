@php
    $isHome = request()->routeIs('home');
    $isGuestBooking = request()->is('foglalas-panel*');
    $brandName = $siteSettings->site_name ?? 'Tüsiszállás';
@endphp

<header
    data-site-nav
    @class([
        'site-nav',
        'is-transparent' => $isHome && ! $isGuestBooking,
        'is-solid' => ! $isHome || $isGuestBooking,
    ])
>
    <div class="section-narrow flex items-center justify-between gap-4 px-5 py-4 md:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 font-display text-2xl tracking-tight text-white md:text-[1.75rem]">
            {{ $brandName }}
        </a>

        <nav class="hidden items-center gap-7 md:flex">
            @include('site.partials.menu-primary-links', ['mode' => 'desktop'])
        </nav>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-sm border border-white/40 px-3 py-2 text-white md:hidden"
            data-nav-toggle
            aria-label="Menü"
        >
            <span class="text-sm tracking-wide">Menü</span>
        </button>
    </div>

    <div class="hidden border-t border-white/10 bg-pine-deep/95 px-5 py-4 md:hidden" data-nav-panel>
        <div class="flex flex-col gap-4 text-white">
            @include('site.partials.menu-primary-links', ['mode' => 'mobile'])
        </div>
    </div>
</header>

