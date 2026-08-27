<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vizuális szerkesztő' }}</title>
    @livewireStyles
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.22.12/dist/css/grapes.min.css">
    <link rel="stylesheet" href="{{ asset('css/site-builder-studio.css') }}">
</head>
<body class="tsb-body">
    {{ $slot }}
    <script src="https://unpkg.com/grapesjs@0.22.12"></script>
    <script src="{{ asset('js/tsb-layout-wires.js') }}"></script>
    <script src="{{ asset('js/ts-hero-slider.js') }}?v=6"></script>
    <script src="{{ asset('js/ts-gallery.js') }}?v=2"></script>
    <script src="{{ asset('js/ts-reveal.js') }}?v=1"></script>
    @livewireScripts
</body>
</html>
