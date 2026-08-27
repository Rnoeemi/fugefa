<?php

namespace App\Support\GrapesJs;

use App\Models\SiteSetting;
use App\Support\PhoneNormalizer;

class SiteLayoutDefaults
{
    public static function menuPlaceholderHtml(): string
    {
        return '<span class="ts-menu-placeholder" data-ts-menu-primary data-gjs-name="Menü" data-gjs-editable="false" data-gjs-removable="false" data-gjs-copyable="false" data-gjs-draggable="false" data-gjs-highlightable="true">Menü</span>';
    }

    public static function menuMobilePlaceholderHtml(): string
    {
        return '<span class="ts-menu-placeholder" data-ts-menu-primary-mobile data-gjs-name="Mobil menü" data-gjs-editable="false" data-gjs-removable="false" data-gjs-copyable="false" data-gjs-draggable="false" data-gjs-highlightable="true">Mobil menü</span>';
    }

    public static function menuPlaceholderComment(): string
    {
        return '<!--TS_MENU_PRIMARY-->';
    }

    /**
     * Régi fejléc HTML-hez hozzáadja a logó/hamburger vázat, ha hiányzik.
     */
    public static function ensureHeaderChrome(string $html): string
    {
        if ($html === '' || ! preg_match('/<header\b/i', $html)) {
            return $html;
        }

        // Régi ts-header-simple → egységes ts-header-bar
        if (preg_match('/\bts-header-simple\b/i', $html)) {
            $html = preg_replace('/\bts-header-simple__inner\b/', 'ts-nav-inner', $html) ?? $html;
            $html = preg_replace('/\bclass="([^"]*)\bts-header-simple\b([^"]*)"/i', 'class="$1site-nav$2"', $html) ?? $html;
            $html = preg_replace('/data-gjs-type="ts-header-simple"/i', 'data-gjs-type="ts-header-bar"', $html) ?? $html;
        }

        $layoutMap = [
            'brand-left' => 'standard',
            'menu-left' => 'standard',
            'with-cta' => 'standard',
            'with-topbar' => 'standard',
        ];
        if (preg_match('/\bdata-layout=(["\'])([^"\']+)\1/i', $html, $lm)) {
            $old = $lm[2];
            if ($old === 'with-cta' && ! preg_match('/\bdata-show-cta=(["\'])1\1/i', $html)) {
                $html = preg_replace('/<header\b/i', '<header data-show-cta="1"', $html, 1) ?? $html;
            }
            if ($old === 'with-topbar' && ! preg_match('/\bdata-show-topbar=(["\'])1\1/i', $html)) {
                $html = preg_replace('/<header\b/i', '<header data-show-topbar="1"', $html, 1) ?? $html;
            }
            if ($old === 'brand-center') {
                $html = preg_replace(
                    '/\bdata-layout=(["\'])brand-center\1/i',
                    'data-layout="brand-center" data-brand-align="center" data-desktop-menu="visible"',
                    $html,
                    1
                ) ?? $html;
            } elseif ($old === 'minimal') {
                $html = preg_replace(
                    '/\bdata-layout=(["\'])minimal\1/i',
                    'data-layout="minimal" data-brand-align="left" data-desktop-menu="hidden"',
                    $html,
                    1
                ) ?? $html;
            } elseif (isset($layoutMap[$old])) {
                $replacement = $layoutMap[$old];
                if ($old === 'menu-left' && ! preg_match('/\bdata-menu-align=/i', $html)) {
                    $html = preg_replace('/\bdata-layout=(["\'])'.preg_quote($old, '/').'\1/i', 'data-layout="'.$replacement.'" data-menu-align="left"', $html, 1) ?? $html;
                } else {
                    $html = preg_replace('/\bdata-layout=(["\'])'.preg_quote($old, '/').'\1/i', 'data-layout="'.$replacement.'"', $html, 1) ?? $html;
                }
            }
        } elseif (! preg_match('/\bdata-layout=/i', $html)) {
            $html = preg_replace('/<header\b/i', '<header data-layout="standard"', $html, 1) ?? $html;
        }

        foreach ([
            'data-brand-align' => 'left',
            'data-desktop-menu' => 'visible',
            'data-menu-align' => 'right',
            'data-mobile-menu-style' => 'dropdown',
            'data-show-topbar' => '0',
            'data-use-site-contact' => '1',
        ] as $attr => $default) {
            if (! preg_match('/\b'.preg_quote($attr, '/').'=/i', $html)) {
                $html = preg_replace('/<header\b/i', '<header '.$attr.'="'.$default.'"', $html, 1) ?? $html;
            }
        }

        if (! preg_match('/\bdata-nav-overlay\b/i', $html) && preg_match('/\bdata-nav-panel\b/i', $html)) {
            $html = preg_replace(
                '/(<div[^>]*\bdata-nav-panel\b[^>]*>)/i',
                '<div class="ts-nav-overlay" data-nav-overlay hidden aria-hidden="true"></div>$1',
                $html,
                1
            ) ?? $html;
        }

        if (! preg_match('/\bts-nav-actions\b/i', $html) && preg_match('/\bts-nav-toggle\b/i', $html)) {
            $html = preg_replace(
                '/(<button[^>]*\bdata-nav-toggle\b[^>]*>.*?<\/button>)/is',
                '<div class="ts-nav-actions">$1</div>',
                $html,
                1
            ) ?? $html;
            if (preg_match('/\bts-nav-cta\b/i', $html) && preg_match('/(<a[^>]*\bts-nav-cta\b[^>]*>.*?<\/a>)\s*(<div class="ts-nav-actions">)/is', $html)) {
                $html = preg_replace(
                    '/(<a[^>]*\bts-nav-cta\b(?!.*--panel)[^>]*>.*?<\/a>)\s*(<div class="ts-nav-actions">)/is',
                    '<div class="ts-nav-actions">$1$2',
                    $html,
                    1
                ) ?? $html;
                $html = preg_replace('/<\/div>\s*<\/div>\s*(<button[^>]*\bdata-nav-toggle)/is', '</div>$1', $html, 1) ?? $html;
            }
        }

        if (! preg_match('/\bts-nav-bar\b/i', $html)
            && preg_match('/\bts-nav-links\b/i', $html)
            && preg_match('/\bts-nav-actions\b/i', $html)) {
            $html = preg_replace(
                '/(<a[^>]*\bts-nav-brand\b[^>]*>.*?<\/a>)\s*(<nav[^>]*\bts-nav-links\b[^>]*>.*?<\/nav>)\s*(<div[^>]*\bts-nav-actions\b[^>]*>.*?<\/div>)/is',
                '$1<div class="ts-nav-bar">$2$3</div>',
                $html,
                1
            ) ?? $html;
        }

        // Régi különálló CTA → trait-alapú
        if (preg_match('/<a[^>]*\bts-nav-cta\b[^>]*>(.*?)<\/a>/is', $html, $cm)
            && ! preg_match('/\bdata-ts-text="cta_label"/i', $html)) {
            $label = trim(strip_tags($cm[1]));
            $href = '#';
            if (preg_match('/\bhref=(["\'])([^"\']*)\1/i', $cm[0], $hm)) {
                $href = $hm[2];
            }
            if ($label !== '' && ! preg_match('/\bdata-cta-label=/i', $html)) {
                $html = preg_replace('/<header\b/i', '<header data-cta-label="'.e($label).'" data-cta-href="'.e($href).'" data-show-cta="1"', $html, 1) ?? $html;
            }
        }

        if (! preg_match('/\bdata-menu-breakpoint=/i', $html)) {
            $html = preg_replace(
                '/<header\b([^>]*)>/i',
                '<header$1 data-menu-breakpoint="phone">',
                $html,
                1
            ) ?? $html;
        }

        if (! preg_match('/\bdata-logo-url=/i', $html)) {
            $html = preg_replace(
                '/<header\b([^>]*)>/i',
                '<header$1 data-logo-url="">',
                $html,
                1
            ) ?? $html;
        }

        if (! preg_match('/\bdata-logo-height=/i', $html)) {
            $html = preg_replace(
                '/<header\b([^>]*)>/i',
                '<header$1 data-logo-height="40" data-logo-max-width="180" style="--ts-logo-height:40px;--ts-logo-max-width:180px">',
                $html,
                1
            ) ?? $html;
        } else {
            // Inline CSS változók szinkron a data attribútumokkal
            $html = preg_replace_callback(
                '/<header\b([^>]*)>/i',
                static function (array $m): string {
                    $attrs = $m[1];
                    $height = '40';
                    $maxWidth = '180';
                    if (preg_match('/\bdata-logo-height=(["\'])([^"\']*)\1/i', $attrs, $hm)) {
                        $height = preg_replace('/[^\d.]/', '', $hm[2]) ?: '40';
                    }
                    if (preg_match('/\bdata-logo-max-width=(["\'])([^"\']*)\1/i', $attrs, $wm)) {
                        $maxWidth = preg_replace('/[^\d.]/', '', $wm[2]) ?: '180';
                    }
                    $vars = "--ts-logo-height:{$height}px;--ts-logo-max-width:{$maxWidth}px";
                    if (preg_match('/\bstyle=(["\'])(.*?)\1/i', $attrs, $sm)) {
                        $style = $sm[2];
                        $style = preg_replace('/--ts-logo-height\s*:\s*[^;]+;?/i', '', $style) ?? $style;
                        $style = preg_replace('/--ts-logo-max-width\s*:\s*[^;]+;?/i', '', $style) ?? $style;
                        $style = trim($style.';'.$vars, '; ');
                        $attrs = preg_replace('/\bstyle=(["\'])(.*?)\1/i', 'style="'.$style.'"', $attrs, 1);
                    } else {
                        $attrs .= ' style="'.$vars.'"';
                    }

                    return '<header'.$attrs.'>';
                },
                $html,
                1
            ) ?? $html;
        }

        // Márkanév szöveg wrap + logo img, ha még sima szöveges link
        if (! preg_match('/data-ts-logo|ts-nav-logo/i', $html) && preg_match('/<a([^>]*class="[^"]*ts-nav-brand[^"]*"[^>]*)>(.*?)<\/a>/is', $html)) {
            $html = preg_replace_callback(
                '/<a([^>]*class="[^"]*ts-nav-brand[^"]*"[^>]*)>(.*?)<\/a>/is',
                static function (array $m): string {
                    $inner = trim(strip_tags($m[2]));
                    $brand = $inner !== '' ? $inner : 'Tüsiszállás';

                    return '<a'.$m[1].'>'
                        .'<img class="ts-nav-logo" data-ts-logo data-ts-src-from="logo_url" src="" alt="'.e($brand).'">'
                        .'<span class="ts-nav-brand-text" data-ts-text="brand" data-ts-brand-text>'.e($brand).'</span>'
                        .'</a>';
                },
                $html,
                1
            ) ?? $html;
        }

        if (! preg_match('/data-nav-toggle/i', $html)) {
            $toggle = '<button type="button" class="ts-nav-toggle" data-nav-toggle aria-label="Menü megnyitása" aria-expanded="false"><span class="ts-nav-toggle__icon" aria-hidden="true"></span></button>';
            if (preg_match('/<\/nav>/i', $html)) {
                $html = preg_replace(
                    '/(<\/nav>)(\s*<\/div>)/i',
                    '$1'.$toggle.'$2',
                    $html,
                    1
                ) ?? $html;
            } else {
                $html = preg_replace(
                    '/(<\/div>\s*)(<\/header>)/i',
                    '$1'.$toggle.'$2',
                    $html,
                    1
                ) ?? $html;
            }
        }

        if (! preg_match('/data-nav-panel/i', $html)) {
            $html = preg_replace('/<\/header>/i', static::headerPanelShell(static::menuMobilePlaceholderHtml()).'</header>', $html, 1) ?? $html;
        }

        $html = static::upgradeHeaderPanelStructure($html);
        $html = static::ensureTopbarSocialMarkup($html);

        return static::sanitizeHeaderHtml(static::normalizeHeaderMenuHtml($html));
    }

    /**
     * Topbar közösségi ikonok: hiányzó platformok pótlása, régi master kapcsoló kivezetése.
     */
    public static function ensureTopbarSocialMarkup(string $html): string
    {
        if ($html === '' || ! preg_match('/\bts-nav-topbar__social\b/i', $html)) {
            return $html;
        }

        foreach ([
            'data-show-social-tiktok' => '0',
            'data-show-social-linkedin' => '0',
            'data-social-tiktok-url' => '',
            'data-social-linkedin-url' => '',
        ] as $attr => $default) {
            if (! preg_match('/\b'.preg_quote($attr, '/').'=/i', $html)) {
                $html = preg_replace('/<header\b/i', '<header '.$attr.'="'.$default.'"', $html, 1) ?? $html;
            }
        }

        $html = preg_replace('/\s*\bdata-show-topbar-social=(["\'])[^"\']*\1/i', '', $html) ?? $html;

        if (! preg_match('/\bts-nav-social--tiktok\b/i', $html) && preg_match('/\bts-nav-social--instagram\b/i', $html)) {
            $insert = self::topbarSocialLinkHtml('tiktok').self::topbarSocialLinkHtml('linkedin');
            $html = preg_replace(
                '/(<a[^>]*\bts-nav-social--instagram\b[^>]*>.*?<\/a>)/is',
                '$1'.$insert,
                $html,
                1
            ) ?? $html;
        }

        if (preg_match('/<div[^>]*\bts-nav-topbar__social\b[^>]*>\s*<\/div>/is', $html)) {
            $html = preg_replace(
                '/(<div[^>]*\bts-nav-topbar__social\b[^>]*>)\s*(<\/div>)/is',
                '$1'.self::topbarSocialLinksHtml().'$2',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    public static function topbarSocialLinksHtml(): string
    {
        return self::topbarSocialLinkHtml('facebook')
            .self::topbarSocialLinkHtml('instagram')
            .self::topbarSocialLinkHtml('tiktok')
            .self::topbarSocialLinkHtml('linkedin');
    }

    private static function topbarSocialLinkHtml(string $platform): string
    {
        return match ($platform) {
            'facebook' => <<<'HTML'
        <a class="ts-nav-social ts-nav-social--facebook" data-ts-show="social_facebook" data-ts-href="social_facebook_url" href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.027 4.388 11.026 10.125 11.926v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796v8.437C19.612 23.1 24 18.1 24 12.073z"/></svg>
        </a>
HTML,
            'instagram' => <<<'HTML'
        <a class="ts-nav-social ts-nav-social--instagram" data-ts-show="social_instagram" data-ts-href="social_instagram_url" href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
HTML,
            'tiktok' => <<<'HTML'
        <a class="ts-nav-social ts-nav-social--tiktok" data-ts-show="social_tiktok" data-ts-href="social_tiktok_url" href="#" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
        </a>
HTML,
            'linkedin' => <<<'HTML'
        <a class="ts-nav-social ts-nav-social--linkedin" data-ts-show="social_linkedin" data-ts-href="social_linkedin_url" href="#" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 114.126 0 2.063 2.063 0 01-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
HTML,
            default => '',
        };
    }

    public static function normalizeMobileMenuStyle(?string $value): string
    {
        $valid = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen'];
        $raw = trim((string) $value);

        foreach ($valid as $style) {
            if ($raw === $style || str_starts_with($raw, $style)) {
                return $style;
            }
        }

        return 'dropdown';
    }

    /**
     * GrapesJS mentés / régi HTML: sérült attribútumok, hidden a panelen, stb.
     */
    public static function sanitizeHeaderHtml(string $html): string
    {
        if ($html === '' || ! preg_match('/<header\b/i', $html)) {
            return $html;
        }

        $html = preg_replace_callback(
            '/<header\b([^>]*)>/i',
            static function (array $m): string {
                $attrs = $m[1];

                if (preg_match('/\bdata-mobile-menu-style=(["\'])([^"\']*)\1/i', $attrs, $mm)) {
                    $fixed = self::normalizeMobileMenuStyle($mm[2]);
                    $attrs = preg_replace(
                        '/\bdata-mobile-menu-style=(["\'])[^"\']*\1/i',
                        'data-mobile-menu-style="'.$fixed.'"',
                        $attrs,
                        1
                    ) ?? $attrs;
                }

                return '<header'.$attrs.'>';
            },
            $html,
            1
        ) ?? $html;

        $html = preg_replace(
            '/<div\b[^>]*\bdata-nav-panel\b[^>]*>/i',
            '<div class="ts-nav-panel" data-nav-panel aria-hidden="true">',
            $html,
            1
        ) ?? $html;

        return $html;
    }

    /**
     * Régi „logó középen” grid layout CSS eltávolítása mentett fejléc stílusból.
     */
    public static function sanitizeHeaderCss(string $css): string
    {
        if ($css === '') {
            return $css;
        }

        $patterns = [
            '/\.site-nav\[data-brand-align="center"\]\s*\.ts-nav-inner\{[^}]*display\s*:\s*grid[^}]*\}/i',
            '/\.site-nav\[data-brand-align="center"\]\s*\.ts-nav-brand\{[^}]*grid-column[^}]*\}/i',
            '/\.site-nav\[data-brand-align="center"\]\s*\.ts-nav-links\{[^}]*grid-column[^}]*\}/i',
            '/\.site-nav\[data-brand-align="center"\]\s*\.ts-nav-actions\{[^}]*grid-column[^}]*\}/i',
        ];

        foreach ($patterns as $pattern) {
            $css = preg_replace($pattern, '', $css) ?? $css;
        }

        $whiteColor = '/color\s*:\s*(?:#fff(?:fff)?\b|white|rgba\(\s*255\s*,\s*255\s*,\s*255(?:\s*,\s*[\d.]+)?\s*\)|rgb\(\s*255\s*,\s*255\s*,\s*255\s*\))/i';

        $css = preg_replace_callback(
            '/\.site-nav[^{]*\{([^}]*)\}/i',
            static function (array $m) use ($whiteColor): string {
                if (! preg_match($whiteColor, $m[1])) {
                    return $m[0];
                }
                $inner = preg_replace($whiteColor, 'color:var(--color-text,#3d3d3d)', $m[1]) ?? $m[1];

                return preg_replace('/\{[^}]*\}/', '{'.$inner.'}', $m[0]) ?? $m[0];
            },
            $css
        ) ?? $css;

        $css = preg_replace_callback(
            '/\.ts-menu-placeholder\{([^}]*)\}/i',
            static function (array $m) use ($whiteColor): string {
                $inner = $m[1];
                if (preg_match($whiteColor, $inner)) {
                    $inner = preg_replace($whiteColor, 'color:inherit', $inner) ?? $inner;
                }
                $inner = preg_replace(
                    '/border(?:-color)?\s*:\s*[^;]*rgba\(\s*255\s*,\s*255\s*,\s*255[^;]*/i',
                    'border:1px dashed color-mix(in srgb,currentColor 50%,transparent)',
                    $inner
                ) ?? $inner;

                return '.ts-menu-placeholder{'.$inner.'}';
            },
            $css
        ) ?? $css;

        return $css;
    }

    /**
     * Olvasható alap színek a fejléc builderben / mentett CSS végén (felülírja a régi fehér exportot).
     */
    public static function headerReadableCss(): string
    {
        return <<<'CSS'
/* TS_HEADER_READABLE */
header.site-nav[data-site-nav],
header.site-nav[data-site-nav] .ts-nav-topbar{
  background:#fff!important;
  color:#3d3d3d!important;
  backdrop-filter:none!important;
  -webkit-backdrop-filter:none!important;
  border-bottom:1px solid #ececec!important;
}
header.site-nav[data-site-nav] .ts-nav-brand,
header.site-nav[data-site-nav] .ts-nav-brand-text,
header.site-nav[data-site-nav] .ts-nav-links a,
header.site-nav[data-site-nav] .ts-nav-links .nav-link,
header.site-nav[data-site-nav] .ts-nav-topbar__item,
header.site-nav[data-site-nav] .ts-menu-placeholder,
header.site-nav[data-site-nav] .ts-nav-toggle{
  color:inherit!important;
}
header.site-nav[data-site-nav] .ts-menu-placeholder{
  border:1px dashed color-mix(in srgb,currentColor 50%,transparent)!important;
}
CSS;
    }

    protected static function headerPanelShell(string $menuInner, string $brand = 'Menü'): string
    {
        $brandEsc = e($brand);

        return <<<HTML
<div class="ts-nav-overlay" data-nav-overlay hidden aria-hidden="true"></div>
<div class="ts-nav-panel" data-nav-panel aria-hidden="true">
  <div class="ts-nav-panel__head">
    <p class="ts-nav-panel__title" data-ts-text="brand">{$brandEsc}</p>
    <button type="button" class="ts-nav-panel__close" data-nav-close aria-label="Menü bezárása">
      <span class="ts-nav-panel__close-icon" aria-hidden="true"></span>
    </button>
  </div>
  <div class="ts-nav-panel__body">
    <nav class="ts-nav-panel__menu" data-ts-menu-slot="primary-mobile">
      {$menuInner}
    </nav>
    <div class="ts-nav-panel__foot">
      <a class="ts-nav-cta ts-nav-cta--panel ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a>
    </div>
  </div>
</div>
HTML;
    }

    /**
     * Régi ts-nav-panel__inner → új head/body/menu/foot szerkezet.
     */
    protected static function upgradeHeaderPanelStructure(string $html): string
    {
        if (! preg_match('/\bts-nav-panel__menu\b/i', $html) && preg_match('/\bts-nav-panel__inner\b/i', $html)) {
            $html = preg_replace_callback(
                '/<div([^>]*\bclass="[^"]*ts-nav-panel__inner[^"]*"[^>]*)>(.*?)<\/div>/is',
                static function (array $m): string {
                    $attrs = $m[1];
                    $inner = $m[2];
                    $cta = '';
                    if (preg_match('/<a[^>]*\bts-nav-cta--panel\b[^>]*>.*?<\/a>/is', $inner, $cm)) {
                        $cta = $cm[0];
                        $inner = str_replace($cta, '', $inner);
                    }
                    $foot = $cta !== ''
                        ? '<div class="ts-nav-panel__foot">'.$cta.'</div>'
                        : '<div class="ts-nav-panel__foot"><a class="ts-nav-cta ts-nav-cta--panel ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a></div>';

                    return '<div class="ts-nav-panel__head">'
                        .'<p class="ts-nav-panel__title" data-ts-text="brand">Menü</p>'
                        .'<button type="button" class="ts-nav-panel__close" data-nav-close aria-label="Menü bezárása">'
                        .'<span class="ts-nav-panel__close-icon" aria-hidden="true"></span>'
                        .'</button>'
                        .'</div>'
                        .'<div class="ts-nav-panel__body">'
                        .'<nav class="ts-nav-panel__menu"'.$attrs.'>'.trim($inner).'</nav>'
                        .$foot
                        .'</div>';
                },
                $html,
                1
            ) ?? $html;
        }

        if (preg_match('/\bdata-nav-panel\b/i', $html)) {
            $html = preg_replace_callback(
                '/<div\b([^>]*\bdata-nav-panel\b[^>]*)>/i',
                static function (array $m): string {
                    $attrs = preg_replace('/\s*\bhidden(=(["\'])[^"\']*\2)?/i', '', $m[1]) ?? $m[1];

                    return '<div'.$attrs.'>';
                },
                $html,
                1
            ) ?? $html;

            if (! preg_match('/\bdata-nav-panel\b[^>]*\baria-hidden=/i', $html)) {
                $html = preg_replace(
                    '/(<div[^>]*\bdata-nav-panel\b)([^>]*>)/i',
                    '$1 aria-hidden="true"$2',
                    $html,
                    1
                ) ?? $html;
            }
        }

        if (preg_match('/\bts-nav-panel__menu\b/i', $html) && ! preg_match('/\bts-nav-panel__close\b/i', $html)) {
            $html = preg_replace(
                '/(<div[^>]*\bdata-nav-panel\b[^>]*>)/i',
                '$1<div class="ts-nav-panel__head"><p class="ts-nav-panel__title" data-ts-text="brand">Menü</p><button type="button" class="ts-nav-panel__close" data-nav-close aria-label="Menü bezárása"><span class="ts-nav-panel__close-icon" aria-hidden="true"></span></button></div>',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    /**
     * Hivatkozás érték → tel:/mailto: vagy abszolút URL (builder + publikus render).
     */
    public static function resolveBoundHref(string $rawValue, string $schemeHint = '', string $paramKey = ''): string
    {
        $value = trim($rawValue);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^(https?:|mailto:|tel:|sms:|\/\/|#|\/)/i', $value)) {
            return $value;
        }

        $scheme = strtolower(trim($schemeHint));
        if ($scheme === '' && $paramKey !== '') {
            if (in_array($paramKey, ['topbar_email', 'email'], true)) {
                $scheme = 'mailto';
            } elseif (in_array($paramKey, ['topbar_phone', 'phone'], true)) {
                $scheme = 'tel';
            }
        }
        if ($scheme === '' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $scheme = 'mailto';
        }
        if ($scheme === '' && preg_match('/^[\d\s+().-]+$/', $value) && strlen(preg_replace('/\D/', '', $value)) >= 6) {
            $scheme = 'tel';
        }
        if ($scheme === 'tel' || $scheme === 'mailto') {
            $normalized = $scheme === 'tel' ? (preg_replace('/\s+/', '', $value) ?: $value) : $value;

            return $scheme.':'.$normalized;
        }

        return $value;
    }

    /**
     * Publikus megjelenítés: topbar szöveg + href (tel:/mailto:) a fejléc traitjeiből vagy site beállításokból.
     */
    public static function hydrateHeaderTopbar(string $html, \App\Models\SiteSetting $settings): string
    {
        if ($html === '' || ! preg_match('/<header\b([^>]*)>/i', $html, $hm)) {
            return $html;
        }

        $headerAttrs = $hm[1];
        $useSite = preg_match('/\bdata-use-site-contact=(["\'])1\1/i', $headerAttrs);

        $phone = trim((string) ($settings->phone ?? ''));
        $email = trim((string) ($settings->email ?? ''));
        $address = trim((string) ($settings->address ?? ''));

        $getAttr = static function (string $name) use ($headerAttrs): string {
            if (preg_match('/\b'.preg_quote($name, '/').'=(["\'])([^"\']*)\1/i', $headerAttrs, $m)) {
                return trim($m[2]);
            }

            return '';
        };

        $topbarPhone = $getAttr('data-topbar-phone');
        $topbarEmail = $getAttr('data-topbar-email');
        $topbarAddress = $getAttr('data-topbar-address');

        if ($useSite) {
            $topbarPhone = $topbarPhone ?: $phone;
            $topbarEmail = $topbarEmail ?: $email;
            $topbarAddress = $topbarAddress ?: $address;
        }

        if ($topbarPhone !== '') {
            $html = self::hydrateTopbarLink($html, 'topbar_phone', 'tel', $topbarPhone);
        }

        if ($topbarEmail !== '') {
            $html = self::hydrateTopbarLink($html, 'topbar_email', 'mailto', $topbarEmail);
        }

        if ($topbarAddress !== '') {
            $html = preg_replace(
                '/(<span[^>]*\bdata-ts-(?:show|text)="topbar_address"[^>]*)>.*?<\/span>/is',
                '$1>'.e($topbarAddress).'</span>',
                $html,
                1
            ) ?? $html;
        }

        return $html;
    }

    /**
     * Topbar link szöveg + href frissítése (attribútum-sorrend független).
     */
    private static function hydrateTopbarLink(string $html, string $key, string $scheme, string $displayValue): string
    {
        $href = self::resolveBoundHref($displayValue, $scheme, $key);
        $pattern = '/(<a\b(?=[^>]*\bdata-ts-href="'.preg_quote($key, '/').'")[^>]*>)(.*?)(<\/a>)/is';

        return preg_replace_callback(
            $pattern,
            static function (array $m) use ($displayValue, $href): string {
                $openTag = $m[1];
                if (preg_match('/(?<![\w-])href=(["\'])[^"\']*\1/i', $openTag)) {
                    $openTag = preg_replace('/(?<![\w-])href=(["\'])[^"\']*\1/i', 'href="'.e($href).'"', $openTag, 1) ?? $openTag;
                } else {
                    $openTag = preg_replace('/<a\b/i', '<a href="'.e($href).'"', $openTag, 1) ?? $openTag;
                }

                return $openTag.e($displayValue).$m[3];
            },
            $html,
            1
        ) ?? $html;
    }

    /**
     * A fejléc szerkesztőben / mentéskor a menü linkek helyett mindig placeholdert hagyunk.
     * A tényleges menüpontokat a Menükezelő adja (publikus renderkor).
     */
    public static function normalizeHeaderMenuHtml(string $html): string
    {
        $placeholder = static::menuPlaceholderHtml();
        $mobilePlaceholder = static::menuMobilePlaceholderHtml();

        $normalized = preg_replace(
            '/(<nav[^>]*(?:ts-nav-links|data-ts-menu-slot="primary")[^>]*>)(.*?)(<\/nav>)/is',
            '$1'.$placeholder.'$3',
            $html,
            1
        );

        if ($normalized === null || $normalized === $html) {
            $normalized = preg_replace(
                '/(<nav(?:\s[^>]*)?>)(.*?)(<\/nav>)/is',
                '$1'.$placeholder.'$3',
                $html,
                1
            );
        }

        $normalized ??= $html;

        if (preg_match('/data-ts-menu-slot="primary-mobile"/i', $normalized)) {
            $normalized = preg_replace(
                '/(<(?:div|nav)[^>]*data-ts-menu-slot="primary-mobile"[^>]*>)(.*?)(<\/(?:div|nav)>)/is',
                '$1'.$mobilePlaceholder.'$3',
                $normalized,
                1
            ) ?? $normalized;
        } elseif (preg_match('/data-nav-panel/i', $normalized)) {
            $normalized = preg_replace(
                '/(<div[^>]*data-nav-panel[^>]*>)(.*?)(<\/div>)/is',
                '$1<div class="ts-nav-panel__inner" data-ts-menu-slot="primary-mobile">'.$mobilePlaceholder.'</div>$3',
                $normalized,
                1
            ) ?? $normalized;
        }

        return $normalized;
    }

    public static function headerHtml(string $brandName = 'Tüsiszállás'): string
    {
        $brand = e($brandName);
        $menu = static::menuPlaceholderHtml();
        $mobileMenu = static::menuMobilePlaceholderHtml();
        $socialLinks = static::topbarSocialLinksHtml();

        return <<<HTML
<header class="site-nav is-solid" data-site-nav data-gjs-type="ts-header-bar" data-gjs-name="Fejléc" data-layout="standard" data-brand-align="left" data-desktop-menu="visible" data-brand="{$brand}" data-logo-url="" data-logo-height="40" data-logo-max-width="180" data-menu-align="right" data-menu-breakpoint="phone" data-mobile-menu-style="dropdown" data-show-topbar="0" data-use-site-contact="1" data-cta-label="Foglalás" data-cta-href="/foglalas-panel" data-show-cta="0" style="--ts-logo-height:40px;--ts-logo-max-width:180px">
  <div class="ts-nav-topbar" data-ts-topbar>
    <div class="ts-nav-topbar__inner">
      <div class="ts-nav-topbar__contact">
        <a class="ts-nav-topbar__item ts-nav-topbar__phone" data-ts-show="topbar_phone" data-ts-text="topbar_phone" data-ts-href="topbar_phone" href="#" data-ts-href-scheme="tel">Telefon</a>
        <a class="ts-nav-topbar__item ts-nav-topbar__email" data-ts-show="topbar_email" data-ts-text="topbar_email" data-ts-href="topbar_email" href="#" data-ts-href-scheme="mailto">E-mail</a>
        <span class="ts-nav-topbar__item ts-nav-topbar__address" data-ts-show="topbar_address" data-ts-text="topbar_address">Cím</span>
      </div>
      <div class="ts-nav-topbar__social">{$socialLinks}</div>
    </div>
  </div>
  <div class="ts-nav-inner">
    <a class="ts-nav-brand" href="/">
      <img class="ts-nav-logo" data-ts-logo data-ts-src-from="logo_url" src="" alt="{$brand}">
      <span class="ts-nav-brand-text" data-ts-text="brand" data-ts-brand-text>{$brand}</span>
    </a>
    <div class="ts-nav-bar">
      <nav class="ts-nav-links" data-ts-menu-slot="primary">
        {$menu}
      </nav>
      <div class="ts-nav-actions">
        <a class="ts-nav-cta ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a>
        <button type="button" class="ts-nav-toggle" data-nav-toggle aria-label="Menü megnyitása" aria-expanded="false">
          <span class="ts-nav-toggle__icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>
  </div>
  <div class="ts-nav-overlay" data-nav-overlay hidden aria-hidden="true"></div>
  <div class="ts-nav-panel" data-nav-panel aria-hidden="true">
    <div class="ts-nav-panel__head">
      <p class="ts-nav-panel__title" data-ts-text="brand">{$brand}</p>
      <button type="button" class="ts-nav-panel__close" data-nav-close aria-label="Menü bezárása">
        <span class="ts-nav-panel__close-icon" aria-hidden="true"></span>
      </button>
    </div>
    <div class="ts-nav-panel__body">
      <nav class="ts-nav-panel__menu" data-ts-menu-slot="primary-mobile">
        {$mobileMenu}
      </nav>
      <div class="ts-nav-panel__foot">
        <a class="ts-nav-cta ts-nav-cta--panel ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a>
      </div>
    </div>
  </div>
</header>
HTML;
    }

    public static function mobileMenuCss(): string
    {
        return static::extractHeaderTemplateCss('TS_MOBILE_MENU_V2', 'END TS_MOBILE_MENU');
    }

    public static function headerLayoutCss(): string
    {
        return static::extractHeaderTemplateCss('TS_HEADER_LAYOUT', 'END TS_HEADER_LAYOUT');
    }

    protected static function extractHeaderTemplateCss(string $startMarker, string $endMarker): string
    {
        $path = resource_path('site-builder/templates/header-bar.html');

        if (! is_readable($path)) {
            return '';
        }

        $html = (string) file_get_contents($path);
        $pattern = '/\/\* '.preg_quote($startMarker, '/').' \*\/(.*?)\/\* '.preg_quote($endMarker, '/').' \*\//s';

        if (preg_match($pattern, $html, $matches)) {
            return '/* '.$startMarker.' */'.trim($matches[1]);
        }

        return '';
    }

    public static function headerCss(): string
    {
        return <<<'CSS'
.site-nav,.ts-header-simple{background:#fff;color:#3d3d3d;font-family:var(--font-sans,Karla,sans-serif);position:relative;z-index:40;border-bottom:1px solid #ececec}
.site-nav.has-logo,.ts-header-simple.has-logo{background:#fff;color:#3d3d3d}
.ts-nav-inner,.ts-header-simple__inner{max-width:72rem;margin:0 auto;padding:1rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem}
.ts-nav-bar{display:contents}
.ts-nav-brand{display:inline-flex;align-items:center;gap:.75rem;font-family:var(--font-display,Literata,serif);font-size:1.5rem;color:inherit;text-decoration:none;min-width:0}
.ts-nav-logo{display:none;height:var(--ts-logo-height,2.5rem);width:auto;max-width:var(--ts-logo-max-width,11rem);object-fit:contain}
.site-nav.has-logo .ts-nav-logo,.ts-header-simple.has-logo .ts-nav-logo{display:block}
.site-nav.has-logo .ts-nav-brand-text,.ts-header-simple.has-logo .ts-nav-brand-text{display:none}
.ts-nav-links{display:flex;flex-wrap:wrap;align-items:center;gap:1.5rem}
.ts-nav-links a,.ts-nav-links .nav-link{color:inherit;text-decoration:none;font-size:.9rem;opacity:.9}
.ts-nav-links a:hover,.ts-nav-links .nav-link:hover{opacity:1}
.ts-nav-cta{background:var(--btn-bg,var(--color-accent));color:var(--btn-fg,#fff)!important;padding:.65rem 1rem;font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;font-weight:600;opacity:1}
.ts-nav-toggle{display:none;align-items:center;justify-content:center;width:2.75rem;height:2.75rem;padding:0;border:1px solid color-mix(in srgb, currentColor 40%, transparent);background:transparent;color:inherit;cursor:pointer;border-radius:.25rem}
.ts-nav-toggle__icon,.ts-nav-toggle__icon:before,.ts-nav-toggle__icon:after{display:block;width:1.15rem;height:2px;background:currentColor;position:relative;transition:transform .2s ease,opacity .2s ease}
.ts-nav-toggle__icon:before,.ts-nav-toggle__icon:after{content:"";position:absolute;left:0}
.ts-nav-toggle__icon:before{top:-6px}
.ts-nav-toggle__icon:after{top:6px}
.ts-nav-toggle.is-open .ts-nav-toggle__icon{background:transparent}
.ts-nav-toggle.is-open .ts-nav-toggle__icon:before{top:0;transform:rotate(45deg)}
.ts-nav-toggle.is-open .ts-nav-toggle__icon:after{top:0;transform:rotate(-45deg)}
.ts-menu-placeholder{display:inline-flex;align-items:center;padding:.45rem .85rem;border:1px dashed color-mix(in srgb, currentColor 50%, transparent);border-radius:.25rem;opacity:.8;font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;user-select:none;pointer-events:none;color:inherit}
@media (max-width:767px){.site-nav[data-menu-breakpoint="phone"] .ts-nav-links,.site-nav:not([data-menu-breakpoint]) .ts-nav-links,.ts-header-simple[data-menu-breakpoint="phone"] .ts-nav-links,.ts-header-simple:not([data-menu-breakpoint]) .ts-nav-links{display:none}.site-nav[data-menu-breakpoint="phone"] .ts-nav-toggle,.site-nav:not([data-menu-breakpoint]) .ts-nav-toggle,.ts-header-simple[data-menu-breakpoint="phone"] .ts-nav-toggle,.ts-header-simple:not([data-menu-breakpoint]) .ts-nav-toggle{display:inline-flex}}
@media (max-width:1023px){.site-nav[data-menu-breakpoint="tablet"] .ts-nav-links,.ts-header-simple[data-menu-breakpoint="tablet"] .ts-nav-links{display:none}.site-nav[data-menu-breakpoint="tablet"] .ts-nav-toggle,.ts-header-simple[data-menu-breakpoint="tablet"] .ts-nav-toggle{display:inline-flex}}
CSS
            ."\n".static::mobileMenuCss();
    }

    public static function footerHtml(string $brandName = 'Tüsiszállás', ?string $footerText = null, ?string $address = null, ?string $phone = null, ?string $email = null): string
    {
        $brand = e($brandName);
        $text = e($footerText ?: 'Vendégház és szobák nyugodt környezetben.');
        $phone = PhoneNormalizer::toString($phone);
        $addressLine = $address
            ? '<li data-ts-site-field="address" data-ts-text="address">'.e($address).'</li>'
            : '<li data-ts-site-field="address" data-ts-text="address" hidden></li>';
        $phoneLine = $phone
            ? '<li data-ts-site-field="phone">Telefon: <a data-ts-text="phone" data-ts-href="phone" data-ts-href-scheme="tel" href="'.e(self::resolveBoundHref($phone, 'tel', 'phone')).'">'.e($phone).'</a></li>'
            : '<li data-ts-site-field="phone" hidden>Telefon: <a data-ts-text="phone" data-ts-href="phone" data-ts-href-scheme="tel" href="#"></a></li>';
        $emailLine = $email
            ? '<li data-ts-site-field="email">E-mail: <a data-ts-text="email" data-ts-href="email" data-ts-href-scheme="mailto" href="'.e(self::resolveBoundHref($email, 'mailto', 'email')).'">'.e($email).'</a></li>'
            : '<li data-ts-site-field="email" hidden>E-mail: <a data-ts-text="email" data-ts-href="email" data-ts-href-scheme="mailto" href="#"></a></li>';

        return <<<HTML
<footer class="ts-footer" data-gjs-type="ts-footer-full" data-gjs-name="Lábléc" data-use-site-contact="1" data-brand="{$brand}" data-text="{$text}">
  <div class="ts-footer__grid">
    <div>
      <p class="ts-footer__brand" data-ts-text="brand">{$brand}</p>
      <p class="ts-footer__text" data-ts-text="text">{$text}</p>
    </div>
    <div>
      <p class="ts-footer__label">Oldalak</p>
      <ul class="ts-footer__list">
        <li><a href="/szallasok">Szállások</a></li>
        <li><a href="/foglalas-panel">Foglalás</a></li>
        <li><a href="/kapcsolat">Kapcsolat</a></li>
      </ul>
    </div>
    <div data-ts-footer-contact>
      <p class="ts-footer__label">Elérhetőség</p>
      <ul class="ts-footer__list" data-ts-site-contact>
        {$addressLine}
        {$phoneLine}
        {$emailLine}
      </ul>
    </div>
  </div>
</footer>
HTML;
    }

    /**
     * Lábléc elérhetőség: weboldal beállításokból (telefon, e-mail, cím).
     */
    public static function hydrateFooterContact(string $html, SiteSetting $settings): string
    {
        if ($html === '' || ! preg_match('/<footer\b/i', $html)) {
            return $html;
        }

        $html = self::ensureFooterContactMarkup($html);
        $html = self::stripFooterCopyright($html);

        // Explicit kikapcsolás
        if (preg_match('/\bdata-use-site-contact=(["\'])0\1/i', $html)) {
            return $html;
        }

        $phone = PhoneNormalizer::toString($settings->phone) ?? '';
        $email = trim((string) ($settings->email ?? ''));
        $address = trim((string) ($settings->address ?? ''));

        $html = self::hydrateFooterSiteField($html, 'address', $address);
        $html = self::hydrateFooterSiteField($html, 'phone', $phone, 'tel');
        $html = self::hydrateFooterSiteField($html, 'email', $email, 'mailto');

        return PhoneNormalizer::sanitizeHtml($html);
    }

    /**
     * Régi lábléc HTML → data-ts-site-contact / field markerek.
     */
    public static function ensureFooterContactMarkup(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        if (! preg_match('/\bdata-use-site-contact=/i', $html) && preg_match('/<footer\b[^>]*\bts-footer\b/i', $html)) {
            $html = preg_replace(
                '/(<footer\b[^>]*)(>)/i',
                '$1 data-use-site-contact="1"$2',
                $html,
                1
            ) ?? $html;
        }

        if (preg_match('/\bdata-ts-site-contact\b|\bdata-ts-site-field=/i', $html)) {
            return $html;
        }

        $html = preg_replace(
            '/(<p[^>]*class="[^"]*ts-footer__label[^"]*"[^>]*>\s*Elérhetőség\s*<\/p>\s*)<ul[^>]*class="[^"]*ts-footer__list[^"]*"[^>]*>.*?<\/ul>/is',
            '$1<ul class="ts-footer__list" data-ts-site-contact>'
            .'<li data-ts-site-field="address" data-ts-text="address"></li>'
            .'<li data-ts-site-field="phone">Telefon: <a data-ts-text="phone" data-ts-href="phone" data-ts-href-scheme="tel" href="#"></a></li>'
            .'<li data-ts-site-field="email">E-mail: <a data-ts-text="email" data-ts-href="email" data-ts-href-scheme="mailto" href="#"></a></li>'
            .'</ul>',
            $html,
            1
        ) ?? $html;

        return $html;
    }

    /**
     * „Minden jog fenntartva” / copyright sáv eltávolítása a mentett lábléc HTML-ből.
     */
    public static function stripFooterCopyright(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $html = preg_replace(
            '/<div\b[^>]*class="[^"]*ts-footer__copy[^"]*"[^>]*>.*?<\/div>/is',
            '',
            $html
        ) ?? $html;

        $html = preg_replace(
            '/<[^>]+>\s*©[^<]*Minden jog fenntartva[^<]*<\/[^>]+>/iu',
            '',
            $html
        ) ?? $html;

        return $html;
    }

    protected static function hydrateFooterSiteField(string $html, string $field, string $value, string $scheme = ''): string
    {
        $filled = $value !== '';

        // <li data-ts-site-field="...">
        $html = preg_replace_callback(
            '/(<li\b[^>]*\bdata-ts-site-field="'.preg_quote($field, '/').'"[^>]*)(>)(.*?)(<\/li>)/is',
            static function (array $m) use ($field, $value, $filled, $scheme): string {
                $open = $m[1];
                if ($filled) {
                    $open = preg_replace('/\shidden\b/i', '', $open) ?? $open;
                    if (preg_match('/\bhidden=(["\'])[^"\']*\1/i', $open)) {
                        $open = preg_replace('/\bhidden=(["\'])[^"\']*\1/i', '', $open) ?? $open;
                    }
                } else {
                    if (! preg_match('/\bhidden\b/i', $open)) {
                        $open .= ' hidden';
                    }
                }

                $inner = $m[3];
                if ($field === 'address') {
                    if (preg_match('/\bdata-ts-text="address"/i', $open) || preg_match('/\bdata-ts-text="address"/i', $inner)) {
                        $inner = e($value);
                    } else {
                        $inner = e($value);
                    }

                    return $open.'>'.$inner.$m[4];
                }

                $label = $field === 'phone' ? 'Telefon: ' : 'E-mail: ';
                $href = $filled ? self::resolveBoundHref($value, $scheme, $field) : '#';

                if (preg_match('/(<a\b[^>]*>)(.*?)(<\/a>)/is', $inner, $am)) {
                    $aOpen = $am[1];
                    if (preg_match('/(?<![\w-])href=(["\'])[^"\']*\1/i', $aOpen)) {
                        $aOpen = preg_replace('/(?<![\w-])href=(["\'])[^"\']*\1/i', 'href="'.e($href).'"', $aOpen, 1) ?? $aOpen;
                    } else {
                        $aOpen = preg_replace('/<a\b/i', '<a href="'.e($href).'"', $aOpen, 1) ?? $aOpen;
                    }
                    $inner = $label.$aOpen.e($value).$am[3];
                } else {
                    $inner = $label.'<a data-ts-text="'.$field.'" data-ts-href="'.$field.'" data-ts-href-scheme="'.e($scheme).'" href="'.e($href).'">'.e($value).'</a>';
                }

                return $open.'>'.$inner.$m[4];
            },
            $html
        ) ?? $html;

        return $html;
    }

    public static function footerCss(): string
    {
        return <<<'CSS'
/* Lábléc: kompakt függőleges ritmus (felülírja a preset --section-y paddinget) */
.ts-footer{background:var(--color-primary);color:#fff;font-family:var(--font-sans,Karla,sans-serif);margin-top:auto}
body.site-shell .ts-footer .ts-footer__grid,
body.site-shell .ts-footer.ts-footer .ts-footer__grid{
  max-width:72rem;margin:0 auto;
  padding:1.75rem 1.5rem 1.1rem!important;
  display:grid;gap:1.25rem!important;
  grid-template-columns:repeat(auto-fit,minmax(200px,1fr))
}
.ts-footer__brand{font-family:var(--font-display,Literata,serif);font-size:1.75rem;margin:0 0 .45rem}
.ts-footer__text{margin:0;opacity:.7;line-height:1.5;max-width:22rem;font-size:.9rem}
.ts-footer__label{font-size:.7rem;letter-spacing:.16em;text-transform:uppercase;opacity:.5;margin:0 0 .55rem;font-weight:600}
.ts-footer__list{list-style:none;padding:0;margin:0;display:grid;gap:.35rem;font-size:.9rem;opacity:.85}
.ts-footer__list a{color:inherit;text-decoration:none}
.ts-footer__list [data-ts-site-field][hidden]{display:none!important}
.ts-footer__copy{display:none!important}

/* Extra szekciók / oszlopok a lábléc után: kis alap padding */
body.site-shell .ts-footer ~ .ts-layout,
body.site-shell .ts-footer + .ts-layout{
  background:var(--color-primary)!important;
  color:#fff!important;
  padding-top:.65rem!important;
  padding-bottom:.65rem!important;
  padding-left:max(var(--section-x),1.5rem)!important;
  padding-right:max(var(--section-x),1.5rem)!important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-layout__inner--2col,
body.site-shell .ts-footer ~ .ts-layout .ts-layout__inner--3col{
  gap:.85rem!important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-layout__col{
  min-height:0!important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-layout__placeholder{
  color:rgba(255,255,255,.55)!important;
  border-color:rgba(255,255,255,.25)!important;
  padding:.5rem!important;
  min-height:0!important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-layout__col > section,
body.site-shell .ts-footer ~ .ts-layout .ts-layout__col > .ts-text,
body.site-shell .ts-footer ~ .ts-layout .ts-layout__col > .ts-image,
body.site-shell .ts-footer ~ .ts-layout .ts-layout__col > .ts-spacer{
  padding-top:0!important;
  padding-bottom:0!important;
  padding-left:0!important;
  padding-right:0!important;
  margin-top:0!important;
  margin-bottom:0!important;
}

body.site-shell .ts-footer ~ .ts-text,
body.site-shell .ts-footer ~ .ts-layout .ts-text{
  background:var(--color-primary)!important;
  color:#fff!important;
  padding-top:.35rem!important;
  padding-bottom:.35rem!important;
  padding-left:0!important;
  padding-right:0!important;
}
body.site-shell .ts-footer ~ .ts-text{
  padding-left:max(var(--section-x),1.5rem)!important;
  padding-right:max(var(--section-x),1.5rem)!important;
  padding-top:.5rem!important;
  padding-bottom:.75rem!important;
}
body.site-shell .ts-footer ~ .ts-text .ts-text__inner,
body.site-shell .ts-footer ~ .ts-layout .ts-text .ts-text__inner{
  max-width:72rem;margin:0 auto;
}
body.site-shell .ts-footer ~ .ts-text h2,
body.site-shell .ts-footer ~ .ts-text p,
body.site-shell .ts-footer ~ .ts-layout .ts-text h2,
body.site-shell .ts-footer ~ .ts-layout .ts-text p{color:inherit!important;margin-top:0!important;margin-bottom:.35rem!important}
body.site-shell .ts-footer ~ .ts-text h2:last-child,
body.site-shell .ts-footer ~ .ts-text p:last-child,
body.site-shell .ts-footer ~ .ts-layout .ts-text h2:last-child,
body.site-shell .ts-footer ~ .ts-layout .ts-text p:last-child{margin-bottom:0!important}

body.site-shell .ts-footer ~ .ts-image,
body.site-shell .ts-footer ~ .ts-layout .ts-image{
  background:var(--color-primary)!important;
  padding:.25rem 0!important;
  margin:0!important;
}
body.site-shell .ts-footer ~ .ts-image{
  padding:.35rem max(var(--section-x),1.5rem) .75rem!important;
}
body.site-shell .ts-footer ~ .ts-image .ts-image__link,
body.site-shell .ts-footer ~ .ts-layout .ts-image .ts-image__link{display:inline-block;max-width:16rem}
body.site-shell .ts-footer ~ .ts-image:not([data-href]) .ts-image__link,
body.site-shell .ts-footer ~ .ts-image[data-href=""] .ts-image__link,
body.site-shell .ts-footer ~ .ts-layout .ts-image:not([data-href]) .ts-image__link,
body.site-shell .ts-footer ~ .ts-layout .ts-image[data-href=""] .ts-image__link{pointer-events:none;cursor:default}
body.site-shell .ts-footer ~ .ts-image img,
body.site-shell .ts-footer ~ .ts-layout .ts-image img{
  max-width:16rem;max-height:5.5rem!important;width:auto;height:auto;
  background:#fff;padding:.35rem;border-radius:.4rem;object-fit:contain
}

body.site-shell .ts-footer ~ .ts-spacer,
body.site-shell .ts-footer ~ .ts-layout .ts-spacer{
  padding:0!important;margin:0!important;min-height:0!important;height:auto!important
}
CSS;
    }
}
