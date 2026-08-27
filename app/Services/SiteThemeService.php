<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\SiteButtonStyles;
use App\Support\SiteColors;
use App\Support\SiteFonts;
use App\Support\SiteStylePresets;
use App\Support\SiteTypography;
use Illuminate\Support\Facades\File;

class SiteThemeService
{
    /**
     * @return array{
     *     brand_name: string,
     *     font_sans: string,
     *     font_display: string,
     *     font_size_base: string,
     *     line_height: string,
     *     style_preset: string,
     *     font_stylesheet: ?string,
     *     css_variables: array<string, string>,
     *     global_colors: list<array{key: string, label: string, value: string, css_var: string, locked: bool}>,
     *     theme_css_url: ?string,
     *     custom_css_url: ?string
     * }
     */
    public function forFrontend(?SiteSetting $settings = null): array
    {
        $settings ??= SiteSetting::current();

        $fontSans = $settings->font_sans ?: 'Karla';
        $fontDisplay = $settings->font_display ?: 'Literata';
        $fontSizeBase = $settings->font_size_base ?: '16px';
        $lineHeight = $settings->line_height ?: '1.6';
        $stylePreset = SiteStylePresets::normalizeKey($settings->style_preset ?? null);
        $globalColors = $settings->resolvedGlobalColors();
        $buttonStyles = $settings->resolvedButtonStyles();
        $typography = $settings->resolvedTypography();

        return [
            'brand_name' => $settings->site_name ?: 'Tüsiszállás',
            'font_sans' => $fontSans,
            'font_display' => $fontDisplay,
            'font_size_base' => $fontSizeBase,
            'line_height' => $lineHeight,
            'style_preset' => $stylePreset,
            'font_stylesheet' => SiteFonts::stylesheet($fontSans, $fontDisplay),
            'css_variables' => [
                '--font-sans' => "'{$fontSans}', ui-sans-serif, system-ui, sans-serif",
                '--font-display' => "'{$fontDisplay}', ui-serif, Georgia, serif",
                '--font-size-base' => $fontSizeBase,
                '--line-height-base' => $lineHeight,
                ...SiteStylePresets::tokenVariables($stylePreset),
                ...SiteTypography::cssVariables($typography, $stylePreset),
                ...SiteColors::cssVariables($globalColors),
                ...SiteButtonStyles::cssVariables($buttonStyles, $globalColors),
                '--color-pine-deep' => 'var(--color-primary)',
                '--color-pine' => 'var(--color-primary-mid)',
                '--color-moss' => 'var(--color-primary-mid)',
                '--color-fern' => 'color-mix(in srgb, var(--color-primary-mid) 65%, white)',
                '--color-brass' => 'var(--color-accent)',
                '--color-mist' => 'var(--color-light)',
                '--color-sand' => 'var(--color-surface)',
                '--color-ink' => 'var(--color-text)',
            ],
            'global_colors' => SiteColors::palette($globalColors),
            'theme_css_url' => $this->themeCssUrl($settings),
            'custom_css_url' => $this->customCssUrl($settings),
        ];
    }

    public function writeThemeCss(?SiteSetting $settings = null): string
    {
        $settings ??= SiteSetting::current();

        $fontSans = $settings->font_sans ?: 'Karla';
        $fontDisplay = $settings->font_display ?: 'Literata';
        $fontSizeBase = $settings->font_size_base ?: '16px';
        $lineHeight = $settings->line_height ?: '1.6';
        $stylePreset = SiteStylePresets::normalizeKey($settings->style_preset ?? null);

        $globalColors = $settings->resolvedGlobalColors();
        $colorVariables = SiteColors::cssVariables($globalColors);
        $buttonVariables = SiteButtonStyles::cssVariables($settings->resolvedButtonStyles(), $globalColors);
        $typeVariables = SiteTypography::cssVariables($settings->resolvedTypography(), $stylePreset);

        $variableLines = collect([
            '--font-sans' => "'{$fontSans}', ui-sans-serif, system-ui, sans-serif",
            '--font-display' => "'{$fontDisplay}', ui-serif, Georgia, serif",
            '--font-size-base' => $fontSizeBase,
            '--line-height-base' => $lineHeight,
            ...SiteStylePresets::tokenVariables($stylePreset),
            ...$typeVariables,
            ...$colorVariables,
            ...$buttonVariables,
            // Klasszikus site / Tailwind token aliasok
            '--color-pine-deep' => 'var(--color-primary)',
            '--color-pine' => 'var(--color-primary-mid)',
            '--color-moss' => 'var(--color-primary-mid)',
            '--color-fern' => 'color-mix(in srgb, var(--color-primary-mid) 65%, white)',
            '--color-brass' => 'var(--color-accent)',
            '--color-mist' => 'var(--color-light)',
            '--color-sand' => 'var(--color-surface)',
            '--color-ink' => 'var(--color-text)',
        ])
            ->map(fn (string $value, string $name): string => "  {$name}: {$value};")
            ->implode("\n");

        $presetCss = SiteStylePresets::presetCss($stylePreset);
        $visibilityCss = \App\Support\GrapesJs\SiteBlockVisibility::css();
        $layoutCss = \App\Support\GrapesJs\SiteBlockLayouts::css();

        $css = <<<CSS
:root {
{$variableLines}
}

body, .site-shell {
  font-family: var(--font-sans);
  font-size: var(--font-size-base);
  line-height: var(--line-height-base);
  color: var(--color-text);
  background-color: var(--color-light);
}

.font-display, h1, h2, h3 {
  font-family: var(--font-display);
}

{$presetCss}

{$visibilityCss}

{$layoutCss}
CSS;

        $relative = 'css/site-theme.css';
        $path = public_path($relative);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $css);

        return $relative;
    }

    public function themeCssUrl(?SiteSetting $settings = null): ?string
    {
        $path = public_path('css/site-theme.css');

        if (! is_file($path)) {
            return null;
        }

        $fileVersion = filemtime($path);
        $dbVersion = $settings?->updated_at?->timestamp;
        $version = $dbVersion ? max($dbVersion, $fileVersion) : $fileVersion;

        return asset('css/site-theme.css').'?v='.$version;
    }

    /**
     * Egyedi CSS, ami a buildelt Tailwind után töltődik be (felülírás).
     */
    public function writeCustomCss(?SiteSetting $settings = null): string
    {
        $settings ??= SiteSetting::current();
        $css = (string) ($settings->custom_css ?? '');

        $relative = 'css/site-custom.css';
        $path = public_path($relative);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $css);

        return $relative;
    }

    public function customCssUrl(?SiteSetting $settings = null): ?string
    {
        $path = public_path('css/site-custom.css');

        if (! is_file($path)) {
            return null;
        }

        // Üres fájlt ne töltsünk be a frontendbe.
        if (trim((string) file_get_contents($path)) === '') {
            return null;
        }

        $version = $settings?->updated_at?->timestamp ?? filemtime($path);

        return asset('css/site-custom.css').'?v='.$version;
    }
}
