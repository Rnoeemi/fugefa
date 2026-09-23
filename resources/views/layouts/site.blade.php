<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', ($siteSettings->site_name ?? 'Fügefa építésziroda').' – építészmérnöki tervezés Baja és környékén.')">
    <title>@yield('title', $siteSettings->site_name ?? 'Fügefa építésziroda')</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('site.partials.theme-fonts')
    {{-- Téma előbb: a Grapes oldal-CSS (@stack head) felülírhatja a szekció hátterét/színét --}}
    @include('site.partials.theme-css')
    <link rel="stylesheet" href="{{ asset('css/site-back-to-top.css') }}?v=2">
    @include('site.partials.analytics')
    @stack('head')
</head>
<body class="site-shell site-style-{{ $siteTheme['style_preset'] ?? 'soft-ui' }}">
    @include('site.partials.header')

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    @include('site.partials.footer')
    <x-cookie-consent />

    <button
        type="button"
        class="ts-back-to-top"
        data-back-to-top
        aria-label="Vissza az oldal tetejére"
        title="Vissza az oldal tetejére"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 19V5"/>
            <path d="m5 12 7-7 7 7"/>
        </svg>
    </button>

    <script src="{{ asset('js/ts-nav.js') }}?v=3"></script>
    <script src="{{ asset('js/ts-hero-slider.js') }}?v=6"></script>
    <script src="{{ asset('js/ts-gallery.js') }}?v=2"></script>
    <script src="{{ asset('js/ts-ba-gallery.js') }}?v=7"></script>
    <script src="{{ asset('js/ts-flipcards.js') }}?v=1"></script>
    <script src="{{ asset('js/ts-reveal.js') }}?v=1"></script>
    <script src="{{ asset('js/ts-back-to-top.js') }}?v=1"></script>
    @if (app(\App\Services\RecaptchaVerifier::class)->isEnabled())
        <script src="https://www.google.com/recaptcha/api.js?onload=__onRecaptchaLoad&render=explicit" async defer></script>
        <script>
            window.__onRecaptchaLoad = function () {
                document.querySelectorAll('.g-recaptcha').forEach(function (el) {
                    if (el.getAttribute('data-recaptcha-rendered') === '1') {
                        return;
                    }
                    var sitekey = el.getAttribute('data-sitekey');
                    if (! sitekey || typeof grecaptcha === 'undefined') {
                        return;
                    }
                    try {
                        grecaptcha.render(el, { sitekey: sitekey });
                        el.setAttribute('data-recaptcha-rendered', '1');
                    } catch (e) {}
                });
            };
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.TsNav?.init?.(document);
            window.TsHeroSlider?.init?.(document);
            window.TsGallery?.init?.(document);
            window.TsBaGallery?.init?.(document);
            window.TsFlipcards?.init?.(document);
            window.TsReveal?.init?.(document);
            window.TsBackToTop?.init?.(document);
        });
        window.addEventListener('load', () => {
            window.TsNav?.init?.(document);
            window.TsHeroSlider?.init?.(document);
            window.TsGallery?.init?.(document);
            window.TsBaGallery?.init?.(document);
            window.TsFlipcards?.init?.(document);
            window.TsReveal?.init?.(document);
            window.TsBackToTop?.init?.(document);
        });
    </script>
    @stack('scripts')
</body>
</html>
