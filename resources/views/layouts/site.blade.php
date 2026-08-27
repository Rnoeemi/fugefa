<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', ($siteSettings->site_name ?? 'Tüsiszállás').' – vendégház, szoba és pihenés nyugodt környezetben. Online foglalás.')">
    <title>@yield('title', $siteSettings->site_name ?? 'Tüsiszállás')</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('site.partials.theme-fonts')
    {{-- Téma előbb: a Grapes oldal-CSS (@stack head) felülírhatja a szekció hátterét/színét --}}
    @include('site.partials.theme-css')
    @stack('head')
</head>
<body class="site-shell site-style-{{ $siteTheme['style_preset'] ?? 'soft-ui' }}">
    @include('site.partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('site.partials.footer')
    <script src="{{ asset('js/ts-nav.js') }}?v=3"></script>
    <script src="{{ asset('js/ts-hero-slider.js') }}?v=6"></script>
    <script src="{{ asset('js/ts-gallery.js') }}?v=2"></script>
    <script src="{{ asset('js/ts-ba-gallery.js') }}?v=4"></script>
    <script src="{{ asset('js/ts-reveal.js') }}?v=1"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.TsNav?.init?.(document);
            window.TsHeroSlider?.init?.(document);
            window.TsGallery?.init?.(document);
            window.TsBaGallery?.init?.(document);
            window.TsReveal?.init?.(document);
        });
        window.addEventListener('load', () => {
            window.TsNav?.init?.(document);
            window.TsHeroSlider?.init?.(document);
            window.TsGallery?.init?.(document);
            window.TsBaGallery?.init?.(document);
            window.TsReveal?.init?.(document);
        });
    </script>
    @stack('scripts')
</body>
</html>
