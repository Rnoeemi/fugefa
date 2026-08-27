{{-- Globális tipográfia (korán töltődik) --}}
@php
    $theme = $siteTheme ?? app(\App\Services\SiteThemeService::class)->forFrontend($siteSettings ?? null);
@endphp

@if (! empty($theme['font_stylesheet']))
    <link href="{{ $theme['font_stylesheet'] }}" rel="stylesheet">
@endif
