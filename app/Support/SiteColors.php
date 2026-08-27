<?php

namespace App\Support;

use Illuminate\Support\Str;

class SiteColors
{
    /**
     * @return list<string>
     */
    public static function baseKeys(): array
    {
        return ['primary', 'text', 'accent', 'light'];
    }

    /**
     * @return array<string, string>
     */
    public static function baseLabels(): array
    {
        return [
            'primary' => 'Primary',
            'text' => 'Szöveg',
            'accent' => 'Accent',
            'light' => 'Light',
        ];
    }

    /**
     * A builder szekciók jelenlegi (erdőzöld / bronz / krém) palettája.
     *
     * @return array{
     *     primary: string,
     *     text: string,
     *     accent: string,
     *     light: string,
     *     extra: list<array{key: string, label: string, value: string}>
     * }
     */
    public static function defaults(): array
    {
        return [
            'primary' => '#0f2920',
            'text' => '#1c1917',
            'accent' => '#8d6b3e',
            'light' => '#f7f4ef',
            'extra' => [
                [
                    'key' => 'primary-mid',
                    'label' => 'Primary közép',
                    'value' => '#1f3d32',
                ],
                [
                    'key' => 'surface',
                    'label' => 'Felület',
                    'value' => '#f3efe8',
                ],
                [
                    'key' => 'muted',
                    'label' => 'Muted',
                    'value' => '#d7cfc4',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $colors
     * @return array{
     *     primary: string,
     *     text: string,
     *     accent: string,
     *     light: string,
     *     extra: list<array{key: string, label: string, value: string}>
     * }
     */
    public static function normalize(?array $colors): array
    {
        $defaults = self::defaults();
        $colors ??= [];

        $normalized = [
            'primary' => self::normalizeHex($colors['primary'] ?? null) ?? $defaults['primary'],
            'text' => self::normalizeHex($colors['text'] ?? null) ?? $defaults['text'],
            'accent' => self::normalizeHex($colors['accent'] ?? null) ?? $defaults['accent'],
            'light' => self::normalizeHex($colors['light'] ?? null) ?? $defaults['light'],
            'extra' => [],
        ];

        // Ha még soha nem volt mentve extra szín, a builder paletta kiegészítőit használjuk.
        if (! array_key_exists('extra', $colors)) {
            $extras = $defaults['extra'];
        } else {
            $extras = is_array($colors['extra']) ? $colors['extra'] : [];
        }

        // Üres listánál is legyenek meg a szekciókhoz szükséges alap extrák.
        if ($extras === []) {
            $extras = $defaults['extra'];
        }

        $usedKeys = self::baseKeys();

        foreach ($extras as $extra) {
            if (! is_array($extra)) {
                continue;
            }

            $label = trim((string) ($extra['label'] ?? ''));
            $value = self::normalizeHex($extra['value'] ?? null);
            $key = self::normalizeKey((string) ($extra['key'] ?? ''), $label);

            if ($label === '' || $value === null || $key === null) {
                continue;
            }

            if (in_array($key, $usedKeys, true)) {
                continue;
            }

            $usedKeys[] = $key;
            $normalized['extra'][] = [
                'key' => $key,
                'label' => $label,
                'value' => $value,
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $colors
     * @return array<string, string>
     */
    public static function cssVariables(?array $colors = null): array
    {
        $normalized = self::normalize($colors);
        $variables = [];

        foreach (self::baseKeys() as $key) {
            $variables[self::cssVarName($key)] = $normalized[$key];
        }

        foreach ($normalized['extra'] as $extra) {
            $variables[self::cssVarName($extra['key'])] = $extra['value'];
        }

        return $variables;
    }

    /**
     * @param  array<string, mixed>|null  $colors
     * @return list<array{key: string, label: string, value: string, css_var: string, locked: bool}>
     */
    public static function palette(?array $colors = null): array
    {
        $normalized = self::normalize($colors);
        $labels = self::baseLabels();
        $palette = [];

        foreach (self::baseKeys() as $key) {
            $palette[] = [
                'key' => $key,
                'label' => $labels[$key],
                'value' => $normalized[$key],
                'css_var' => self::cssVarName($key),
                'locked' => true,
            ];
        }

        foreach ($normalized['extra'] as $extra) {
            $palette[] = [
                'key' => $extra['key'],
                'label' => $extra['label'],
                'value' => $extra['value'],
                'css_var' => self::cssVarName($extra['key']),
                'locked' => false,
            ];
        }

        return $palette;
    }

    /**
     * @param  array<string, mixed>|null  $colors
     * @return array{
     *     color_primary: string,
     *     color_text: string,
     *     color_accent: string,
     *     color_light: string,
     *     color_extra: list<array{key: string, label: string, value: string}>
     * }
     */
    public static function toFormState(?array $colors = null): array
    {
        $normalized = self::normalize($colors);

        return [
            'color_primary' => $normalized['primary'],
            'color_text' => $normalized['text'],
            'color_accent' => $normalized['accent'],
            'color_light' => $normalized['light'],
            'color_extra' => $normalized['extra'],
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array{
     *     primary: string,
     *     text: string,
     *     accent: string,
     *     light: string,
     *     extra: list<array{key: string, label: string, value: string}>
     * }
     */
    public static function fromFormState(array $state): array
    {
        return self::normalize([
            'primary' => $state['color_primary'] ?? null,
            'text' => $state['color_text'] ?? null,
            'accent' => $state['color_accent'] ?? null,
            'light' => $state['color_light'] ?? null,
            'extra' => $state['color_extra'] ?? [],
        ]);
    }

    /**
     * Régi hardcoded hex/rgb értékek cseréje CSS változókra (mentett oldal CSS migrációhoz).
     */
    public static function replaceHardcodedColors(string $css): string
    {
        $replacements = [
            '#0f2920' => 'var(--color-primary)',
            '#1f3d32' => 'var(--color-primary-mid)',
            '#1c1917' => 'var(--color-text)',
            '#8d6b3e' => 'var(--color-accent)',
            '#f7f4ef' => 'var(--color-light)',
            '#f3efe8' => 'var(--color-surface)',
            '#d7cfc4' => 'var(--color-muted)',
            'rgb(15, 41, 32)' => 'var(--color-primary)',
            'rgb(15,41,32)' => 'var(--color-primary)',
            'rgb(31, 61, 50)' => 'var(--color-primary-mid)',
            'rgb(31,61,50)' => 'var(--color-primary-mid)',
            'rgb(28, 25, 23)' => 'var(--color-text)',
            'rgb(28,25,23)' => 'var(--color-text)',
            'rgb(141, 107, 62)' => 'var(--color-accent)',
            'rgb(141,107,62)' => 'var(--color-accent)',
            'rgb(247, 244, 239)' => 'var(--color-light)',
            'rgb(247,244,239)' => 'var(--color-light)',
            'rgb(243, 239, 232)' => 'var(--color-surface)',
            'rgb(243,239,232)' => 'var(--color-surface)',
            'rgb(215, 207, 196)' => 'var(--color-muted)',
            'rgb(215,207,196)' => 'var(--color-muted)',
        ];

        $css = str_ireplace(array_keys($replacements), array_values($replacements), $css);

        $rgbaMaps = [
            '28,25,23' => '--color-text',
            '15,41,32' => '--color-primary',
            '141,107,62' => '--color-accent',
        ];

        foreach ($rgbaMaps as $rgb => $var) {
            [$r, $g, $b] = explode(',', $rgb);
            $pattern = '/rgba\(\s*'.$r.'\s*,\s*'.$g.'\s*,\s*'.$b.'\s*,\s*(0?\.\d+|0|1)\s*\)/i';
            $css = preg_replace_callback(
                $pattern,
                static function (array $matches) use ($var): string {
                    $pct = (int) round(((float) $matches[1]) * 100);

                    return "color-mix(in srgb, var({$var}) {$pct}%, transparent)";
                },
                $css
            ) ?? $css;
        }

        return $css;
    }

    public static function cssVarName(string $key): string
    {
        return '--color-'.$key;
    }

    public static function normalizeHex(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value) !== 1) {
            return null;
        }

        if (strlen($value) === 4) {
            $value = '#'.$value[1].$value[1].$value[2].$value[2].$value[3].$value[3];
        }

        return strtolower($value);
    }

    public static function normalizeKey(string $key, string $label = ''): ?string
    {
        $candidate = trim($key);

        if ($candidate === '' && $label !== '') {
            $candidate = $label;
        }

        $candidate = Str::slug($candidate, '-');

        if ($candidate === '' || preg_match('/^[a-z][a-z0-9-]{0,40}$/', $candidate) !== 1) {
            return null;
        }

        if (in_array($candidate, self::baseKeys(), true)) {
            return null;
        }

        return $candidate;
    }
}
