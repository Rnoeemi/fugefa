<?php

namespace App\Support;

/**
 * Elsődleges / másodlagos / inverse gombstílusok (Megjelenés + theme CSS).
 */
class SiteButtonStyles
{
    /**
     * @return list<string>
     */
    public static function variantKeys(): array
    {
        return ['primary', 'secondary', 'inverse'];
    }

    /**
     * @return array<string, string>
     */
    public static function variantLabels(): array
    {
        return [
            'primary' => 'Elsődleges',
            'secondary' => 'Másodlagos',
            'inverse' => 'Inverse',
        ];
    }

    /**
     * @return list<string>
     */
    public static function propertyKeys(): array
    {
        return ['bg', 'fg', 'hover_bg', 'border'];
    }

    /**
     * @return array<string, string>
     */
    public static function propertyLabels(): array
    {
        return [
            'bg' => 'Háttér',
            'fg' => 'Szöveg',
            'hover_bg' => 'Hover háttér',
            'border' => 'Keret',
        ];
    }

    /**
     * Alapértelmezett színek (hex vagy „transparent”) a globális palettához igazítva.
     *
     * @param  array<string, mixed>|null  $colors
     * @return array{
     *     primary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     secondary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     inverse: array{bg: string, fg: string, hover_bg: string, border: string}
     * }
     */
    public static function defaults(?array $colors = null): array
    {
        $palette = SiteColors::normalize($colors);
        $accent = $palette['accent'];
        $text = $palette['text'];
        $primary = $palette['primary'];
        $light = $palette['light'];

        return [
            'primary' => [
                'bg' => $accent,
                'fg' => '#ffffff',
                'hover_bg' => self::mixTowardBlack($accent, 18),
                'border' => 'transparent',
            ],
            'secondary' => [
                'bg' => 'transparent',
                'fg' => $text,
                'hover_bg' => 'transparent',
                'border' => self::mixTransparent($text, 22),
            ],
            'inverse' => [
                'bg' => '#ffffff',
                'fg' => $primary,
                'hover_bg' => $light,
                'border' => 'transparent',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $styles
     * @param  array<string, mixed>|null  $colors
     * @return array{
     *     primary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     secondary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     inverse: array{bg: string, fg: string, hover_bg: string, border: string}
     * }
     */
    public static function normalize(?array $styles, ?array $colors = null): array
    {
        $defaults = self::defaults($colors);
        $styles ??= [];
        $normalized = [];

        foreach (self::variantKeys() as $variant) {
            $source = is_array($styles[$variant] ?? null) ? $styles[$variant] : [];
            $row = [];
            foreach (self::propertyKeys() as $prop) {
                $row[$prop] = self::normalizeColorValue($source[$prop] ?? null)
                    ?? $defaults[$variant][$prop];
            }
            $normalized[$variant] = $row;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $styles
     * @param  array<string, mixed>|null  $colors
     * @return array<string, string>
     */
    public static function cssVariables(?array $styles = null, ?array $colors = null): array
    {
        $normalized = self::normalize($styles, $colors);
        $vars = [];

        foreach (self::variantKeys() as $variant) {
            foreach (self::propertyKeys() as $prop) {
                $vars['--btn-'.$variant.'-'.str_replace('_', '-', $prop)] = $normalized[$variant][$prop];
            }
        }

        // Régi token aliasok (blokk CSS / sharedCss)
        $vars['--btn-bg'] = 'var(--btn-primary-bg)';
        $vars['--btn-fg'] = 'var(--btn-primary-fg)';
        $vars['--btn-hover-bg'] = 'var(--btn-primary-hover-bg)';
        $vars['--btn-border'] = 'var(--btn-primary-border)';
        $vars['--btn-ghost-bg'] = 'var(--btn-secondary-bg)';
        $vars['--btn-ghost-fg'] = 'var(--btn-secondary-fg)';
        $vars['--btn-ghost-border'] = 'var(--btn-secondary-border)';
        $vars['--btn-on-dark-ghost-fg'] = 'var(--btn-inverse-fg)';
        $vars['--btn-on-dark-ghost-border'] = 'var(--btn-inverse-border)';

        return $vars;
    }

    /**
     * @param  array<string, mixed>|null  $styles
     * @param  array<string, mixed>|null  $colors
     * @return array<string, string>
     */
    public static function toFormState(?array $styles = null, ?array $colors = null): array
    {
        $normalized = self::normalize($styles, $colors);
        $state = [];

        foreach (self::variantKeys() as $variant) {
            foreach (self::propertyKeys() as $prop) {
                $value = $normalized[$variant][$prop];
                // Színválasztó csak hex-et kezel; transparent / color-mix → üres (mentéskor default)
                $state['btn_'.$variant.'_'.$prop] = SiteColors::normalizeHex($value);
            }
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<string, mixed>|null  $colors
     * @return array{
     *     primary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     secondary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     inverse: array{bg: string, fg: string, hover_bg: string, border: string}
     * }
     */
    public static function fromFormState(array $state, ?array $colors = null): array
    {
        $raw = [];

        foreach (self::variantKeys() as $variant) {
            $raw[$variant] = [];
            foreach (self::propertyKeys() as $prop) {
                $key = 'btn_'.$variant.'_'.$prop;
                $value = $state[$key] ?? null;
                if ($value === null || $value === '') {
                    // Üres mező: a normalize() a színpaletta alapértelmezését használja
                    continue;
                }
                $raw[$variant][$prop] = $value;
            }
        }

        return self::normalize($raw, $colors);
    }

    public static function normalizeColorValue(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (strcasecmp($value, 'transparent') === 0) {
            return 'transparent';
        }

        // color-mix(...) a másodlagos kerethez – engedjük át
        if (str_starts_with(strtolower($value), 'color-mix(')) {
            return $value;
        }

        return SiteColors::normalizeHex($value);
    }

    /**
     * Builder select opciók.
     *
     * @return list<array{id: string, name: string}>
     */
    public static function builderOptions(): array
    {
        $options = [];
        foreach (self::variantLabels() as $id => $name) {
            $options[] = ['id' => $id, 'name' => $name];
        }

        return $options;
    }

    public static function normalizeVariant(mixed $value, string $fallback = 'primary'): string
    {
        $value = is_string($value) ? strtolower(trim($value)) : '';
        if ($value === 'ghost') {
            return 'secondary';
        }

        return in_array($value, self::variantKeys(), true) ? $value : $fallback;
    }

    protected static function mixTowardBlack(string $hex, int $percent): string
    {
        $hex = SiteColors::normalizeHex($hex) ?? '#000000';
        $p = max(0, min(100, $percent));

        return sprintf('color-mix(in srgb, %s %d%%, black)', $hex, 100 - $p);
    }

    protected static function mixTransparent(string $hex, int $percent): string
    {
        $hex = SiteColors::normalizeHex($hex) ?? '#000000';
        $p = max(0, min(100, $percent));

        return sprintf('color-mix(in srgb, %s %d%%, transparent)', $hex, $p);
    }
}
