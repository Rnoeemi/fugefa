{{-- Téma + egyedi CSS: a Grapes oldal-CSS ELŐTT, hogy a Style Manager színei érvényesüljenek --}}
@php
    $theme = $siteTheme ?? app(\App\Services\SiteThemeService::class)->forFrontend($siteSettings ?? null);
@endphp

@if (! empty($theme['theme_css_url']))
    <link rel="stylesheet" href="{{ $theme['theme_css_url'] }}">
@elseif (! empty($theme['css_variables']))
    <style id="site-theme-vars">
        :root {
            @foreach ($theme['css_variables'] as $name => $value)
                {{ $name }}: {{ $value }};
            @endforeach
        }

        body, .site-shell {
            font-family: var(--font-sans) !important;
            font-size: var(--font-size-base);
            line-height: var(--line-height-base);
        }

        .font-display, h1, h2, h3 {
            font-family: var(--font-display) !important;
        }
    </style>
@endif

@if (! empty($theme['custom_css_url']))
    <link rel="stylesheet" href="{{ $theme['custom_css_url'] }}">
@endif
