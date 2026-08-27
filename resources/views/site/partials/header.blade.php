@php
    $settings = $siteSettings ?? \App\Models\SiteSetting::current();
@endphp

@if ($settings->hasCustomHeader())
    @if ($cssUrl = $settings->headerCssUrl())
        <link rel="stylesheet" href="{{ $cssUrl }}">
    @elseif (filled($settings->header_css))
        <style>{!! \App\Support\GrapesJs\SiteLayoutDefaults::sanitizeHeaderCss((string) $settings->header_css) !!}</style>
    @endif
    <style>{!! \App\Support\GrapesJs\SiteLayoutDefaults::headerLayoutCss() !!}</style>
    <style>{!! \App\Support\GrapesJs\SiteLayoutDefaults::headerReadableCss() !!}</style>
    <style>{!! \App\Support\GrapesJs\SiteLayoutDefaults::mobileMenuCss() !!}</style>
    @php
        $headerHtml = \App\Support\GrapesJs\SiteLayoutDefaults::ensureHeaderChrome((string) $settings->header_html);
        $menuDesktop = view('site.partials.menu-primary-links', ['mode' => 'desktop'])->render();
        $menuMobile = view('site.partials.menu-primary-links', [
            'mode' => 'mobile',
            'linkClass' => 'ts-nav-panel__link',
        ])->render();
        $commentPlaceholder = '<!--TS_MENU_PRIMARY-->';

        if (str_contains($headerHtml, $commentPlaceholder)) {
            $headerHtml = str_replace($commentPlaceholder, $menuDesktop, $headerHtml);
        } elseif (preg_match('/<span[^>]*data-ts-menu-primary(?!-mobile)[^>]*>.*?<\/span>/is', $headerHtml)) {
            $headerHtml = preg_replace(
                '/<span[^>]*data-ts-menu-primary(?!-mobile)[^>]*>.*?<\/span>/is',
                $menuDesktop,
                $headerHtml,
                1
            ) ?? $headerHtml;
        } else {
            $headerHtml = preg_replace(
                '/(<nav[^>]*(?:ts-nav-links|data-ts-menu-slot="primary")[^>]*>)(.*?)(<\/nav>)/is',
                '$1'.$menuDesktop.'$3',
                $headerHtml,
                1
            ) ?? $headerHtml;
        }

        if (preg_match('/data-ts-menu-primary-mobile/i', $headerHtml)) {
            $headerHtml = preg_replace(
                '/<span[^>]*data-ts-menu-primary-mobile[^>]*>.*?<\/span>/is',
                $menuMobile,
                $headerHtml,
                1
            ) ?? $headerHtml;
        } elseif (preg_match('/data-ts-menu-slot="primary-mobile"/i', $headerHtml)) {
            $headerHtml = preg_replace(
                '/(<(?:div|nav)[^>]*data-ts-menu-slot="primary-mobile"[^>]*>)(.*?)(<\/(?:div|nav)>)/is',
                '$1'.$menuMobile.'$3',
                $headerHtml,
                1
            ) ?? $headerHtml;
        }

        $headerHtml = \App\Support\GrapesJs\SiteLayoutDefaults::hydrateHeaderTopbar($headerHtml, $settings);
        $headerHtml = \App\Support\PhoneNormalizer::sanitizeHtml($headerHtml);

        if (preg_match('/\bdata-brand=(["\'])([^"\']*)\1/i', $headerHtml, $bm)) {
            $brandLabel = trim($bm[2]);
            if ($brandLabel !== '') {
                $headerHtml = preg_replace(
                    '/(<p[^>]*\bclass="[^"]*ts-nav-panel__title[^"]*"[^>]*)(>.*?<\/p>)/is',
                    '$1>'.e($brandLabel).'</p>',
                    $headerHtml,
                    1
                ) ?? $headerHtml;
            }
        }

        // Logó URL → has-logo class a root headeren
        $headerHtml = preg_replace_callback(
            '/<header\b([^>]*)>/i',
            function (array $m): string {
                $attrs = $m[1];
                $hasLogo = preg_match('/\bdata-logo-url=(["\'])([^"\']+)\1/i', $attrs, $lm)
                    && trim((string) ($lm[2] ?? '')) !== '';

                if ($hasLogo) {
                    if (preg_match('/\bclass=(["\'])(.*?)\1/i', $attrs, $cm)) {
                        if (! preg_match('/(^|\s)has-logo(\s|$)/', $cm[2])) {
                            $attrs = preg_replace(
                                '/\bclass=(["\'])(.*?)\1/i',
                                'class="'.trim($cm[2].' has-logo').'"',
                                $attrs,
                                1
                            );
                        }
                    } else {
                        $attrs .= ' class="has-logo"';
                    }
                } else {
                    $attrs = preg_replace_callback(
                        '/\s*\bclass=(["\'])(.*?)\1/i',
                        static function (array $cm): string {
                            $classes = trim(preg_replace('/(^|\s)has-logo(\s|$)/', ' ', $cm[2]) ?? '');

                            return $classes === '' ? '' : ' class="'.$classes.'"';
                        },
                        $attrs
                    ) ?? $attrs;
                }

                return '<header'.$attrs.'>';
            },
            $headerHtml,
            1
        ) ?? $headerHtml;
    @endphp
    {!! $headerHtml !!}
@else
    @include('site.partials.nav-dynamic')
@endif
