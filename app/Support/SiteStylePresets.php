<?php

namespace App\Support;

class SiteStylePresets
{
    public const DEFAULT = 'soft-ui';

    /**
     * Régi szállás-centrikus kulcsok → új, általános stílusok.
     *
     * @return array<string, string>
     */
    public static function aliases(): array
    {
        return [
            'guesthouse' => 'warm-craft',
            'boutique' => 'classic',
            'nature' => 'organic',
            'loft' => 'industrial',
            'mediterranean' => 'coastal',
            'japandi' => 'literary',
        ];
    }

    /**
     * @return array<string, array{label: string, hint: string}>
     */
    public static function categories(): array
    {
        return [
            'clean' => [
                'label' => 'Letisztult',
                'hint' => 'Modern, tiszta felületek',
            ],
            'content' => [
                'label' => 'Tartalom',
                'hint' => 'Tipográfia és olvasmányosság',
            ],
            'surface' => [
                'label' => 'Felület',
                'hint' => 'Anyag- és mélységhatás',
            ],
            'natural' => [
                'label' => 'Természetes',
                'hint' => 'Meleg, organikus hangulat',
            ],
            'character' => [
                'label' => 'Karakteres',
                'hint' => 'Erősebb vizuális identitás',
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'minimal' => self::minimal(),
            'soft-ui' => self::softUi(),
            'precision' => self::precision(),
            'editorial' => self::editorial(),
            'classic' => self::classic(),
            'literary' => self::literary(),
            'glass' => self::glass(),
            'solid' => self::solid(),
            'matte' => self::matte(),
            'organic' => self::organic(),
            'warm-craft' => self::warmCraft(),
            'coastal' => self::coastal(),
            'industrial' => self::industrial(),
            'deco' => self::deco(),
            'brutal' => self::brutal(),
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn (array $preset, string $key): array => [$key => $preset['label']])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function descriptions(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn (array $preset, string $key): array => [$key => $preset['description']])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function get(?string $key): array
    {
        $presets = self::all();
        $key = self::normalizeKey($key);

        return $presets[$key];
    }

    public static function normalizeKey(?string $key): string
    {
        $key = is_string($key) ? trim($key) : '';

        if ($key === '') {
            return self::DEFAULT;
        }

        $aliases = self::aliases();
        if (isset($aliases[$key])) {
            $key = $aliases[$key];
        }

        return in_array($key, self::keys(), true) ? $key : self::DEFAULT;
    }

    /**
     * @return array<string, mixed>
     */
    public static function minimal(): array
    {
        return [
            'key' => 'minimal',
            'category' => 'clean',
            'label' => 'Minimal',
            'tagline' => 'Tiszta, hűvös, sok fehér',
            'shape' => 'Apró radius, nulla árnyék, nagy légtér',
            'description' => 'Hidegebb szürke, visszafogott CTA, városi letisztultság. Ideális termékoldalhoz, portfólióhoz, modern szolgáltatóhoz.',
            'font_sans' => 'Inter',
            'font_display' => 'Outfit',
            'font_size_base' => '15.5px',
            'line_height' => '1.65',
            'colors' => [
                'primary' => '#0f172a',
                'text' => '#1e293b',
                'accent' => '#334155',
                'light' => '#f8fafc',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1e293b'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#f1f5f9'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#cbd5e1'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.125rem',
                '--radius-md' => '0.2rem',
                '--radius-lg' => '0.3rem',
                '--radius-control' => '0.2rem',
                '--radius-card' => '0.25rem',
                '--radius-media' => '0.2rem',
                '--section-y' => '5.25rem',
                '--section-x' => '1.75rem',
                '--space-block' => '1.5rem',
                '--gap-sm' => '0.75rem',
                '--gap-md' => '1.5rem',
                '--gap-lg' => '2.5rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '8%',
                '--tracking-label' => '0.16em',
                '--tracking-btn' => '0.06em',
                '--heading-tracking' => '-0.03em',
                '--heading-weight' => '600',
                '--label-weight' => '500',
                '--btn-pad-y' => '0.85rem',
                '--btn-pad-x' => '1.4rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => 'none',
                '--shadow-soft' => 'none',
                '--shadow-lift' => 'none',
                '--heading-size' => 'clamp(1.9rem, 3.6vw, 2.9rem)',
                '--content-max' => '68rem',
            ]),
            'css' => self::sharedCss('Minimal: tiszta, hűvös, sok fehér'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function softUi(): array
    {
        return [
            'key' => 'soft-ui',
            'category' => 'clean',
            'label' => 'Soft UI',
            'tagline' => 'Barátságos, modern, puha',
            'shape' => 'Nagy radius, lágy árnyék, kerek gombok',
            'description' => 'Alapértelmezett modern felület: puha sarkok, enyhe mélység, olvasható tipográfia. Bármilyen szolgáltatáshoz vagy brandhez jó kiindulópont.',
            'font_sans' => 'DM Sans',
            'font_display' => 'Manrope',
            'font_size_base' => '16px',
            'line_height' => '1.65',
            'colors' => [
                'primary' => '#1e3a5f',
                'text' => '#1f2937',
                'accent' => '#3b82f6',
                'light' => '#f1f5f9',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#2a4a73'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#e2e8f0'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#94a3b8'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.55rem',
                '--radius-md' => '0.9rem',
                '--radius-lg' => '1.35rem',
                '--radius-control' => '999px',
                '--radius-card' => '1.1rem',
                '--radius-media' => '1rem',
                '--section-y' => '4.5rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.4rem',
                '--gap-sm' => '0.65rem',
                '--gap-md' => '1.15rem',
                '--gap-lg' => '1.85rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '8%',
                '--tracking-label' => '0.08em',
                '--tracking-btn' => '0.04em',
                '--heading-tracking' => '-0.02em',
                '--heading-weight' => '700',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.85rem',
                '--btn-pad-x' => '1.55rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 10px 24px color-mix(in srgb, var(--color-accent) 22%, transparent)',
                '--shadow-soft' => '0 14px 36px color-mix(in srgb, var(--color-text) 8%, transparent)',
                '--shadow-lift' => '0 22px 48px color-mix(in srgb, var(--color-text) 12%, transparent)',
                '--heading-size' => 'clamp(1.95rem, 3.8vw, 3rem)',
                '--content-max' => '72rem',
            ]),
            'css' => self::sharedCss('Soft UI: barátságos, modern, puha felületek'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function editorial(): array
    {
        return [
            'key' => 'editorial',
            'category' => 'content',
            'label' => 'Editorial',
            'tagline' => 'Magazinos, tipográfia-központú',
            'shape' => 'Éles sarkok, nagy címsor, hajszálvonal',
            'description' => 'Erős serif címsorok, széles lélegzet, olvasmányos szövegtest. Bloghoz, magazinhoz, tartalomvezérelt márkához.',
            'font_sans' => 'Source Sans 3',
            'font_display' => 'Newsreader',
            'font_size_base' => '17px',
            'line_height' => '1.75',
            'colors' => [
                'primary' => '#111827',
                'text' => '#1f2937',
                'accent' => '#b45309',
                'light' => '#fafaf9',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#374151'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#f5f5f4'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#a8a29e'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0',
                '--radius-md' => '0',
                '--radius-lg' => '0',
                '--radius-control' => '0',
                '--radius-card' => '0',
                '--radius-media' => '0',
                '--section-y' => '5.75rem',
                '--section-x' => '1.85rem',
                '--space-block' => '1.75rem',
                '--gap-sm' => '0.7rem',
                '--gap-md' => '1.4rem',
                '--gap-lg' => '2.4rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '12%',
                '--tracking-label' => '0.2em',
                '--tracking-btn' => '0.14em',
                '--heading-tracking' => '-0.025em',
                '--heading-weight' => '500',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.95rem',
                '--btn-pad-x' => '1.65rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => 'none',
                '--shadow-soft' => 'none',
                '--shadow-lift' => 'none',
                '--heading-size' => 'clamp(2.35rem, 5vw, 3.75rem)',
                '--content-max' => '64rem',
            ]),
            'css' => self::sharedCss('Editorial: magazinos ritmus, erős tipográfia'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function classic(): array
    {
        return [
            'key' => 'classic',
            'category' => 'content',
            'label' => 'Classic',
            'tagline' => 'Elegáns, időtálló, hotel-tiszta',
            'shape' => 'Éles sarkok, nagy térköz, nincs árnyék',
            'description' => 'Serif címsor, hajszálvékony vonalak, kimért luxus. Professzionális szolgáltatásokhoz, design márkákhoz.',
            'font_sans' => 'Source Sans 3',
            'font_display' => 'Playfair Display',
            'font_size_base' => '16px',
            'line_height' => '1.75',
            'colors' => [
                'primary' => '#121c19',
                'text' => '#161616',
                'accent' => '#a8844f',
                'light' => '#f6f3ee',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1f322c'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#ebe4d8'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#c9bfb0'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0',
                '--radius-md' => '0',
                '--radius-lg' => '0',
                '--radius-control' => '0',
                '--radius-card' => '0',
                '--radius-media' => '0',
                '--section-y' => '6rem',
                '--section-x' => '1.75rem',
                '--space-block' => '1.85rem',
                '--gap-sm' => '0.65rem',
                '--gap-md' => '1.35rem',
                '--gap-lg' => '2.25rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '10%',
                '--tracking-label' => '0.22em',
                '--tracking-btn' => '0.2em',
                '--heading-tracking' => '-0.02em',
                '--heading-weight' => '500',
                '--label-weight' => '500',
                '--btn-pad-y' => '1rem',
                '--btn-pad-x' => '1.75rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => 'none',
                '--shadow-soft' => 'none',
                '--shadow-lift' => 'none',
                '--heading-size' => 'clamp(2rem, 4vw, 3.25rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Classic: elegáns, éles vonalak, nagy lélegzet'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function glass(): array
    {
        return [
            'key' => 'glass',
            'category' => 'surface',
            'label' => 'Glass',
            'tagline' => 'Áttetsző, légies, modern',
            'shape' => 'Nagy radius, üvegszerű kártyák, soft blur',
            'description' => 'Áttetsző felületek, finom blur, világos gradient-érzet. Tech, kreatív és modern brandekhez.',
            'font_sans' => 'Manrope',
            'font_display' => 'Outfit',
            'font_size_base' => '16px',
            'line_height' => '1.65',
            'colors' => [
                'primary' => '#0f2847',
                'text' => '#0f172a',
                'accent' => '#06b6d4',
                'light' => '#e8f4fc',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1a3a5c'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#d6ebf7'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#7aa8c4'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.75rem',
                '--radius-md' => '1.1rem',
                '--radius-lg' => '1.6rem',
                '--radius-control' => '999px',
                '--radius-card' => '1.35rem',
                '--radius-media' => '1.2rem',
                '--section-y' => '4.75rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.45rem',
                '--gap-sm' => '0.7rem',
                '--gap-md' => '1.2rem',
                '--gap-lg' => '2rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '14%',
                '--tracking-label' => '0.1em',
                '--tracking-btn' => '0.05em',
                '--heading-tracking' => '-0.02em',
                '--heading-weight' => '600',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.9rem',
                '--btn-pad-x' => '1.6rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 12px 28px color-mix(in srgb, var(--color-accent) 25%, transparent)',
                '--shadow-soft' => '0 18px 40px color-mix(in srgb, var(--color-primary) 12%, transparent)',
                '--shadow-lift' => '0 28px 56px color-mix(in srgb, var(--color-primary) 18%, transparent)',
                '--heading-size' => 'clamp(2rem, 4vw, 3.1rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Glass: áttetsző, légies felületek')
                ."\n".self::glassExtraCss(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function organic(): array
    {
        return [
            'key' => 'organic',
            'category' => 'natural',
            'label' => 'Organic',
            'tagline' => 'Nyugodt, zöld, regeneráló',
            'shape' => 'Levél-szerű sarkok, pill gombok',
            'description' => 'Organikus zöldek, bőséges tér, aszimmetrikus kártyák. Wellness, természet, fenntartható brandekhez.',
            'font_sans' => 'Nunito Sans',
            'font_display' => 'Fraunces',
            'font_size_base' => '17px',
            'line_height' => '1.75',
            'colors' => [
                'primary' => '#123528',
                'text' => '#10241c',
                'accent' => '#2f6b54',
                'light' => '#d7e8de',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1f4d3a'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#c5dbcf'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#8fb09e'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.65rem',
                '--radius-md' => '1rem',
                '--radius-lg' => '1.5rem',
                '--radius-control' => '999px',
                '--radius-card' => '1.25rem',
                '--radius-media' => '1.1rem',
                '--radius-leaf' => '2.4rem 0.55rem 2.4rem 0.55rem',
                '--section-y' => '5.5rem',
                '--section-x' => '1.6rem',
                '--space-block' => '1.65rem',
                '--gap-sm' => '0.85rem',
                '--gap-md' => '1.35rem',
                '--gap-lg' => '2rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '14%',
                '--tracking-label' => '0.14em',
                '--tracking-btn' => '0.08em',
                '--heading-tracking' => '-0.01em',
                '--heading-weight' => '500',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.9rem',
                '--btn-pad-x' => '1.65rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 10px 24px color-mix(in srgb, var(--color-primary) 18%, transparent)',
                '--shadow-soft' => '0 16px 40px color-mix(in srgb, var(--color-primary) 16%, transparent)',
                '--shadow-lift' => '0 24px 56px color-mix(in srgb, var(--color-primary) 20%, transparent)',
                '--heading-size' => 'clamp(2.1rem, 4vw, 3.2rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Organic: organikus, puha, regeneráló')
                ."\n".self::organicExtraCss(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function warmCraft(): array
    {
        return [
            'key' => 'warm-craft',
            'category' => 'natural',
            'label' => 'Warm craft',
            'tagline' => 'Meleg, kézműves, otthonos',
            'shape' => 'Kerekebb sarkok, lágy árnyék',
            'description' => 'Meleg krém–bronz paletta, barátságos kártyák, ismerős arányok. Kávézókhoz, kézműves márkákhoz, vendéglátáshoz.',
            'font_sans' => 'Karla',
            'font_display' => 'Literata',
            'font_size_base' => '16px',
            'line_height' => '1.6',
            'colors' => SiteColors::defaults(),
            'tokens' => self::tokens([
                '--radius-sm' => '0.4rem',
                '--radius-md' => '0.7rem',
                '--radius-lg' => '1.1rem',
                '--radius-control' => '0.55rem',
                '--radius-card' => '0.9rem',
                '--radius-media' => '0.7rem',
                '--section-y' => '3.75rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.35rem',
                '--gap-sm' => '0.55rem',
                '--gap-md' => '1rem',
                '--gap-lg' => '1.5rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '12%',
                '--tracking-label' => '0.12em',
                '--tracking-btn' => '0.1em',
                '--heading-tracking' => '0',
                '--heading-weight' => '600',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.8rem',
                '--btn-pad-x' => '1.25rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => '0 6px 16px color-mix(in srgb, var(--color-accent) 22%, transparent)',
                '--shadow-soft' => '0 12px 28px color-mix(in srgb, var(--color-text) 9%, transparent)',
                '--shadow-lift' => '0 18px 40px color-mix(in srgb, var(--color-text) 12%, transparent)',
                '--heading-size' => 'clamp(1.85rem, 3.4vw, 2.75rem)',
                '--content-max' => '72rem',
            ]),
            'css' => self::sharedCss('Warm craft: meleg, otthonos, kézműves'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function industrial(): array
    {
        return [
            'key' => 'industrial',
            'category' => 'character',
            'label' => 'Industrial',
            'tagline' => 'Kontrasztos, határozott, loft',
            'shape' => 'Éles formák, erős árnyék, vastagabb border',
            'description' => 'Sötét alap, erős accent, sűrűbb ritmus. Kreatív stúdiókhoz, design márkákhoz, városi brandekhez.',
            'font_sans' => 'IBM Plex Sans',
            'font_display' => 'Barlow',
            'font_size_base' => '15.5px',
            'line_height' => '1.5',
            'colors' => [
                'primary' => '#09090b',
                'text' => '#18181b',
                'accent' => '#e11d48',
                'light' => '#f4f4f5',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#27272a'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#e4e4e7'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#a1a1aa'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.1rem',
                '--radius-md' => '0.2rem',
                '--radius-lg' => '0.35rem',
                '--radius-control' => '0.15rem',
                '--radius-card' => '0.25rem',
                '--radius-media' => '0.15rem',
                '--section-y' => '3.5rem',
                '--section-x' => '1.35rem',
                '--space-block' => '1.15rem',
                '--gap-sm' => '0.45rem',
                '--gap-md' => '0.85rem',
                '--gap-lg' => '1.35rem',
                '--border-width' => '2px',
                '--card-border-width' => '2px',
                '--border-strength' => '16%',
                '--tracking-label' => '0.18em',
                '--tracking-btn' => '0.16em',
                '--heading-tracking' => '-0.025em',
                '--heading-weight' => '700',
                '--label-weight' => '700',
                '--btn-pad-y' => '0.95rem',
                '--btn-pad-x' => '1.5rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => '0 12px 28px color-mix(in srgb, var(--color-accent) 35%, transparent)',
                '--shadow-soft' => '0 16px 36px color-mix(in srgb, var(--color-primary) 20%, transparent)',
                '--shadow-lift' => '0 28px 56px color-mix(in srgb, var(--color-primary) 28%, transparent)',
                '--heading-size' => 'clamp(1.9rem, 3.5vw, 2.85rem)',
                '--content-max' => '74rem',
            ]),
            'css' => self::sharedCss('Industrial: kontrasztos, sűrű, határozott'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function deco(): array
    {
        return [
            'key' => 'deco',
            'category' => 'character',
            'label' => 'Art deco',
            'tagline' => 'Arany, geometria, estélyi luxus',
            'shape' => 'Éles sarkok, arany keretérzet, erős tracking',
            'description' => 'Fekete–pezsgő arany, geometrikus ritmus, glamour tipográfia. Estélyi, luxus, karakteres márkákhoz.',
            'font_sans' => 'Josefin Sans',
            'font_display' => 'Cormorant Garamond',
            'font_size_base' => '15.5px',
            'line_height' => '1.55',
            'colors' => [
                'primary' => '#0c0c0c',
                'text' => '#161616',
                'accent' => '#c6a75e',
                'light' => '#f7f2e8',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#2a2a2a'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#ebe2d2'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#d2c3a5'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0',
                '--radius-md' => '0',
                '--radius-lg' => '0',
                '--radius-control' => '0',
                '--radius-card' => '0',
                '--radius-media' => '0',
                '--section-y' => '5rem',
                '--section-x' => '1.6rem',
                '--space-block' => '1.55rem',
                '--gap-sm' => '0.55rem',
                '--gap-md' => '1.1rem',
                '--gap-lg' => '1.75rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '18%',
                '--tracking-label' => '0.28em',
                '--tracking-btn' => '0.24em',
                '--heading-tracking' => '0.04em',
                '--heading-weight' => '500',
                '--label-weight' => '600',
                '--btn-pad-y' => '1rem',
                '--btn-pad-x' => '1.85rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => '0 0 0 1px color-mix(in srgb, var(--color-accent) 55%, transparent), 0 14px 30px color-mix(in srgb, var(--color-primary) 25%, transparent)',
                '--btn-border' => 'var(--color-accent)',
                '--shadow-soft' => '0 0 0 1px color-mix(in srgb, var(--color-accent) 28%, transparent)',
                '--shadow-lift' => '0 0 0 1px var(--color-accent), 0 22px 48px color-mix(in srgb, var(--color-primary) 30%, transparent)',
                '--heading-size' => 'clamp(2.3rem, 4.6vw, 3.6rem)',
                '--content-max' => '68rem',
            ]),
            'css' => self::sharedCss('Art deco: arany, geometria, estélyi luxus'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function precision(): array
    {
        return [
            'key' => 'precision',
            'category' => 'clean',
            'label' => 'Precision',
            'tagline' => 'Pontos, strukturált, SaaS-tiszta',
            'shape' => 'Közepes radius, vékony border, sűrűbb ritmus',
            'description' => 'Szakmai, rendezett felület: tiszta hierarchia, visszafogott árnyék. Termékoldalhoz, szolgáltatói és B2B brandekhez.',
            'font_sans' => 'Plus Jakarta Sans',
            'font_display' => 'Figtree',
            'font_size_base' => '15.5px',
            'line_height' => '1.6',
            'colors' => [
                'primary' => '#0b1220',
                'text' => '#1e293b',
                'accent' => '#0f766e',
                'light' => '#f8fafc',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1e293b'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#eef2f7'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#94a3b8'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.3rem',
                '--radius-md' => '0.5rem',
                '--radius-lg' => '0.75rem',
                '--radius-control' => '0.45rem',
                '--radius-card' => '0.65rem',
                '--radius-media' => '0.55rem',
                '--section-y' => '4.5rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.25rem',
                '--gap-sm' => '0.55rem',
                '--gap-md' => '1rem',
                '--gap-lg' => '1.65rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '10%',
                '--tracking-label' => '0.1em',
                '--tracking-btn' => '0.04em',
                '--heading-tracking' => '-0.025em',
                '--heading-weight' => '700',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.8rem',
                '--btn-pad-x' => '1.35rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 8px 18px color-mix(in srgb, var(--color-accent) 18%, transparent)',
                '--shadow-soft' => '0 10px 24px color-mix(in srgb, var(--color-text) 6%, transparent)',
                '--shadow-lift' => '0 18px 40px color-mix(in srgb, var(--color-text) 10%, transparent)',
                '--heading-size' => 'clamp(1.85rem, 3.4vw, 2.85rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Precision: strukturált, tiszta, professzionális'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function literary(): array
    {
        return [
            'key' => 'literary',
            'category' => 'content',
            'label' => 'Literary',
            'tagline' => 'Olvasmányos, nyugodt, könyves',
            'shape' => 'Kis radius, széles sormagasság, szűkebb tartalom',
            'description' => 'Hosszú szövegekhez optimalizált ritmus: kényelmes törzs, meleg serif címsor. Bloghoz, kiadóhoz, tudásmárkához.',
            'font_sans' => 'Source Sans 3',
            'font_display' => 'Lora',
            'font_size_base' => '17.5px',
            'line_height' => '1.8',
            'colors' => [
                'primary' => '#2c241b',
                'text' => '#3b3229',
                'accent' => '#8b5e3c',
                'light' => '#f7f3ec',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#4a3f34'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#ebe3d6'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#b5a796'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.2rem',
                '--radius-md' => '0.35rem',
                '--radius-lg' => '0.55rem',
                '--radius-control' => '0.3rem',
                '--radius-card' => '0.45rem',
                '--radius-media' => '0.35rem',
                '--section-y' => '5.25rem',
                '--section-x' => '1.75rem',
                '--space-block' => '1.6rem',
                '--gap-sm' => '0.7rem',
                '--gap-md' => '1.35rem',
                '--gap-lg' => '2.15rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '11%',
                '--tracking-label' => '0.12em',
                '--tracking-btn' => '0.08em',
                '--heading-tracking' => '-0.01em',
                '--heading-weight' => '600',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.85rem',
                '--btn-pad-x' => '1.5rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => 'none',
                '--shadow-soft' => '0 10px 28px color-mix(in srgb, var(--color-text) 7%, transparent)',
                '--shadow-lift' => '0 18px 42px color-mix(in srgb, var(--color-text) 10%, transparent)',
                '--heading-size' => 'clamp(2.1rem, 4.2vw, 3.2rem)',
                '--type-section-lead-size' => '1.12rem',
                '--type-card-title-size' => '1.2rem',
                '--type-card-body-size' => '1rem',
                '--content-max' => '62rem',
            ]),
            'css' => self::sharedCss('Literary: olvasmányos, könyves tipográfia'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function solid(): array
    {
        return [
            'key' => 'solid',
            'category' => 'surface',
            'label' => 'Solid',
            'tagline' => 'Tömör, erős, határozott felületek',
            'shape' => 'Közepes radius, erős árnyék, vastagabb CTA',
            'description' => 'Telített színek, tömör kártyák, egyértelmű CTA. Kampányoldalhoz, modern szolgáltatói brandhez.',
            'font_sans' => 'Work Sans',
            'font_display' => 'Montserrat',
            'font_size_base' => '16px',
            'line_height' => '1.55',
            'colors' => [
                'primary' => '#0f172a',
                'text' => '#0f172a',
                'accent' => '#1d4ed8',
                'light' => '#f1f5f9',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#1e293b'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#e2e8f0'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#64748b'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.45rem',
                '--radius-md' => '0.75rem',
                '--radius-lg' => '1.1rem',
                '--radius-control' => '0.65rem',
                '--radius-card' => '0.95rem',
                '--radius-media' => '0.85rem',
                '--section-y' => '4.25rem',
                '--section-x' => '1.45rem',
                '--space-block' => '1.35rem',
                '--gap-sm' => '0.6rem',
                '--gap-md' => '1.1rem',
                '--gap-lg' => '1.75rem',
                '--border-width' => '1px',
                '--card-border-width' => '0',
                '--border-strength' => '0%',
                '--tracking-label' => '0.08em',
                '--tracking-btn' => '0.05em',
                '--heading-tracking' => '-0.03em',
                '--heading-weight' => '700',
                '--label-weight' => '700',
                '--btn-pad-y' => '0.95rem',
                '--btn-pad-x' => '1.65rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 14px 30px color-mix(in srgb, var(--color-accent) 30%, transparent)',
                '--shadow-soft' => '0 16px 36px color-mix(in srgb, var(--color-primary) 14%, transparent)',
                '--shadow-lift' => '0 26px 52px color-mix(in srgb, var(--color-primary) 20%, transparent)',
                '--heading-size' => 'clamp(2rem, 4vw, 3.15rem)',
                '--btn-fg' => '#ffffff',
                '--link-color' => 'var(--color-accent)',
                '--content-max' => '72rem',
            ]),
            'css' => self::sharedCss('Solid: tömör felületek, erős CTA')
                ."\n".self::solidExtraCss(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function matte(): array
    {
        return [
            'key' => 'matte',
            'category' => 'surface',
            'label' => 'Matte',
            'tagline' => 'Matt, puha, fénymentes',
            'shape' => 'Nagy radius, lágy árnyék, semmi fényes él',
            'description' => 'Lágy, átlátszatlan felületek blur nélkül. Modern, nyugodt UI lifestyle és kreatív brandekhez.',
            'font_sans' => 'Mulish',
            'font_display' => 'Outfit',
            'font_size_base' => '16px',
            'line_height' => '1.65',
            'colors' => [
                'primary' => '#2d2a32',
                'text' => '#2a2730',
                'accent' => '#7c6a9a',
                'light' => '#f3f1f5',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#453f4d'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#e6e2ea'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#b4aabe'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.7rem',
                '--radius-md' => '1.1rem',
                '--radius-lg' => '1.6rem',
                '--radius-control' => '0.9rem',
                '--radius-card' => '1.35rem',
                '--radius-media' => '1.2rem',
                '--section-y' => '4.75rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.45rem',
                '--gap-sm' => '0.7rem',
                '--gap-md' => '1.2rem',
                '--gap-lg' => '1.95rem',
                '--border-width' => '1px',
                '--card-border-width' => '0',
                '--border-strength' => '0%',
                '--tracking-label' => '0.06em',
                '--tracking-btn' => '0.03em',
                '--heading-tracking' => '-0.02em',
                '--heading-weight' => '600',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.9rem',
                '--btn-pad-x' => '1.55rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 12px 28px color-mix(in srgb, var(--color-accent) 18%, transparent)',
                '--shadow-soft' => '0 18px 40px color-mix(in srgb, var(--color-text) 8%, transparent)',
                '--shadow-lift' => '0 26px 52px color-mix(in srgb, var(--color-text) 12%, transparent)',
                '--heading-size' => 'clamp(1.95rem, 3.8vw, 3rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Matte: matt, puha, fénymentes felületek')
                ."\n".self::matteExtraCss(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function coastal(): array
    {
        return [
            'key' => 'coastal',
            'category' => 'natural',
            'label' => 'Coastal',
            'tagline' => 'Napfényes, laza, tengerparti',
            'shape' => 'Közepes radius, meleg árnyék, laza gap',
            'description' => 'Terrakotta–homok, laza ritmus, napfényes kontraszt. Utazás, vendéglátás, életmód márkákhoz.',
            'font_sans' => 'Lato',
            'font_display' => 'Cormorant Garamond',
            'font_size_base' => '16.5px',
            'line_height' => '1.7',
            'colors' => [
                'primary' => '#4a3226',
                'text' => '#2a1f1a',
                'accent' => '#d2693c',
                'light' => '#fbf5ec',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#6b4635'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#f2e4d2'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#dcc4a4'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0.45rem',
                '--radius-md' => '0.85rem',
                '--radius-lg' => '1.25rem',
                '--radius-control' => '0.7rem',
                '--radius-card' => '1rem',
                '--radius-media' => '0.85rem',
                '--section-y' => '4.25rem',
                '--section-x' => '1.5rem',
                '--space-block' => '1.45rem',
                '--gap-sm' => '0.6rem',
                '--gap-md' => '1.15rem',
                '--gap-lg' => '1.85rem',
                '--border-width' => '1px',
                '--card-border-width' => '1px',
                '--border-strength' => '14%',
                '--tracking-label' => '0.1em',
                '--tracking-btn' => '0.06em',
                '--heading-tracking' => '0.01em',
                '--heading-weight' => '500',
                '--label-weight' => '600',
                '--btn-pad-y' => '0.85rem',
                '--btn-pad-x' => '1.45rem',
                '--btn-text-transform' => 'none',
                '--btn-shadow' => '0 10px 22px color-mix(in srgb, var(--color-accent) 28%, transparent)',
                '--shadow-soft' => '0 14px 32px color-mix(in srgb, var(--color-accent) 14%, transparent)',
                '--shadow-lift' => '0 22px 48px color-mix(in srgb, var(--color-accent) 18%, transparent)',
                '--heading-size' => 'clamp(2.15rem, 4.2vw, 3.4rem)',
                '--content-max' => '72rem',
            ]),
            'css' => self::sharedCss('Coastal: napfényes, meleg, laza ritmus'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function brutal(): array
    {
        return [
            'key' => 'brutal',
            'category' => 'character',
            'label' => 'Brutal',
            'tagline' => 'Nyers, vastag, poster-szerű',
            'shape' => 'Nulla radius, vastag border, erős tracking',
            'description' => 'Kemény kontraszt, vastag keretek, plakátos tipográfia. Kreatív stúdióhoz, eseményhez, merész brandhez.',
            'font_sans' => 'IBM Plex Sans',
            'font_display' => 'Barlow',
            'font_size_base' => '16px',
            'line_height' => '1.45',
            'colors' => [
                'primary' => '#000000',
                'text' => '#111111',
                'accent' => '#facc15',
                'light' => '#f5f5f5',
                'extra' => [
                    ['key' => 'primary-mid', 'label' => 'Primary közép', 'value' => '#262626'],
                    ['key' => 'surface', 'label' => 'Felület', 'value' => '#e5e5e5'],
                    ['key' => 'muted', 'label' => 'Muted', 'value' => '#a3a3a3'],
                ],
            ],
            'tokens' => self::tokens([
                '--radius-sm' => '0',
                '--radius-md' => '0',
                '--radius-lg' => '0',
                '--radius-control' => '0',
                '--radius-card' => '0',
                '--radius-media' => '0',
                '--section-y' => '3.75rem',
                '--section-x' => '1.35rem',
                '--space-block' => '1.2rem',
                '--gap-sm' => '0.5rem',
                '--gap-md' => '0.9rem',
                '--gap-lg' => '1.4rem',
                '--border-width' => '2px',
                '--card-border-width' => '2px',
                '--border-strength' => '100%',
                '--tracking-label' => '0.16em',
                '--tracking-btn' => '0.14em',
                '--heading-tracking' => '-0.04em',
                '--heading-weight' => '800',
                '--label-weight' => '700',
                '--btn-pad-y' => '1rem',
                '--btn-pad-x' => '1.6rem',
                '--btn-text-transform' => 'uppercase',
                '--btn-shadow' => '4px 4px 0 var(--color-primary)',
                '--btn-border' => 'var(--color-primary)',
                '--shadow-soft' => '4px 4px 0 color-mix(in srgb, var(--color-primary) 85%, transparent)',
                '--shadow-lift' => '6px 6px 0 var(--color-primary)',
                '--heading-size' => 'clamp(2.2rem, 5vw, 3.6rem)',
                '--content-max' => '70rem',
            ]),
            'css' => self::sharedCss('Brutal: nyers, vastag, poster-szerű'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function tokenVariables(?string $key = null): array
    {
        $preset = self::get($key);
        /** @var array<string, string> $tokens */
        $tokens = $preset['tokens'];

        return $tokens;
    }

    public static function presetCss(?string $key = null): string
    {
        $preset = self::get($key);

        return trim((string) ($preset['css'] ?? ''));
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private static function tokens(array $overrides): array
    {
        return [
            '--radius-sm' => '0.35rem',
            '--radius-md' => '0.65rem',
            '--radius-lg' => '1rem',
            '--radius-control' => '0.5rem',
            '--radius-card' => '0.85rem',
            '--radius-media' => '0.65rem',
            '--radius-leaf' => 'var(--radius-card)',
            '--section-y' => '3.5rem',
            '--section-x' => '1.5rem',
            '--space-block' => '1.25rem',
            '--gap-sm' => '0.5rem',
            '--gap-md' => '1rem',
            '--gap-lg' => '1.5rem',
            '--border-width' => '1px',
            '--card-border-width' => '1px',
            '--border-strength' => '10%',
            '--tracking-label' => '0.12em',
            '--tracking-btn' => '0.1em',
            '--heading-tracking' => '0',
            '--heading-weight' => '600',
            '--label-weight' => '600',
            '--btn-pad-y' => '0.8rem',
            '--btn-pad-x' => '1.25rem',
            '--btn-text-transform' => 'uppercase',
            '--btn-shadow' => 'none',
            '--btn-bg' => 'var(--color-accent)',
            '--btn-fg' => '#ffffff',
            '--btn-hover-bg' => 'color-mix(in srgb, var(--color-accent) 82%, black)',
            '--btn-border' => 'transparent',
            '--btn-ghost-bg' => 'transparent',
            '--btn-ghost-fg' => 'var(--color-text)',
            '--btn-ghost-border' => 'color-mix(in srgb, var(--color-text) 22%, transparent)',
            '--btn-on-dark-ghost-fg' => '#ffffff',
            '--btn-on-dark-ghost-border' => 'color-mix(in srgb, #ffffff 40%, transparent)',
            '--btn-font-size' => '0.75rem',
            '--btn-font-weight' => '600',
            '--link-color' => 'var(--color-accent)',
            '--shadow-soft' => 'none',
            '--shadow-lift' => 'none',
            '--heading-size' => 'clamp(1.85rem, 3.5vw, 2.75rem)',
            /* Szekció h2 (split / features / ikonlista…): a split cím a mérvadó */
            '--type-section-title-size' => 'clamp(1.75rem, 3vw, 2.4rem)',
            '--type-section-lead-size' => '1.05rem',
            '--type-card-title-size' => '1.15rem',
            '--type-card-body-size' => '0.95rem',
            '--content-max' => '72rem',
            ...$overrides,
        ];
    }

    private static function organicExtraCss(): string
    {
        return <<<'CSS'
/* Organic: erősebb kontraszt + levél-szerű aszimmetrikus sarkok */
.ts-faq,
.ts-features,
.ts-icon-list,
.ts-dyn-featured,
.ts-dyn-search {
  background: color-mix(in srgb, var(--color-primary) 10%, var(--color-light));
}

.ts-steps,
.ts-checkin,
.ts-gallery,
.ts-map {
  background: var(--color-surface);
}

.ts-faq__item,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-logo-row__item,
.ts-pricing__table,
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside {
  border-radius: var(--radius-leaf, 2.4rem 0.55rem 2.4rem 0.55rem) !important;
  background: #fff;
  color: var(--color-text);
  border: 1px solid color-mix(in srgb, var(--color-primary) 14%, transparent) !important;
  box-shadow: var(--shadow-soft) !important;
}

/* Fehér kártyán sötét szöveg – NINCS !important, hogy a Style Manager felülírhassa */
.ts-faq__item,
.ts-faq__item *:not(a.ts-btn):not(.ts-btn),
.ts-feature,
.ts-feature *:not(a.ts-btn):not(.ts-btn),
.ts-icon-item,
.ts-icon-item *:not(a.ts-btn):not(.ts-btn),
.ts-steps__grid li,
.ts-steps__grid li *:not(span):not(a.ts-btn):not(.ts-btn),
.ts-checkin article,
.ts-checkin article *:not(a.ts-btn):not(.ts-btn),
.ts-logo-row__item,
.ts-logo-row__item *,
.ts-dyn-card,
.ts-dyn-card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-card__cta),
.ts-dyn-list__row,
.ts-dyn-list__row *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-list__cta),
.ts-dyn-appointment__card,
.ts-dyn-appointment__card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-appointment__cta),
.ts-dyn-search__form,
.ts-dyn-search__form *:not(button),
.ts-dyn-contact-form__form,
.ts-dyn-contact-form__form *:not(button),
.ts-dyn-details aside,
.ts-dyn-details aside *:not(a.ts-btn):not(.ts-btn) {
  color: var(--color-text);
}

.ts-faq__item summary,
.ts-checkin article h3,
.ts-steps__grid h3,
.ts-feature h3,
.ts-icon-item h3,
.ts-stats article h3 {
  font-weight: 700 !important;
}

.ts-faq__item p,
.ts-checkin article p,
.ts-steps__grid p,
.ts-feature p,
.ts-icon-item p,
.ts-stats article p,
.ts-dyn-card__desc {
  opacity: 0.85 !important;
}

.ts-steps__grid span,
.ts-dyn-appointment__cta,
.ts-dyn-card__cta:not(.ts-dyn-card__cta--outline),
.ts-dyn-list__cta {
  color: #fff !important;
}

.ts-faq__lead,
.ts-steps__lead,
.ts-features .ts-features__lead,
.ts-icon-list .ts-icon-list__lead,
.ts-dyn-featured__eyebrow {
  opacity: 0.78 !important;
}

.ts-gallery__item,
.ts-gallery__item img,
.ts-map__frame,
.ts-video__frame,
.ts-split__media,
.ts-split__media img,
.ts-dyn-gallery__grid figure,
.ts-dyn-map__frame,
.ts-dyn-featured__media img,
.ts-dyn-card__media img {
  border-radius: 2rem 0.45rem 2rem 0.45rem !important;
}

.ts-steps__grid span {
  border-radius: 999px !important;
  background: var(--color-primary) !important;
  color: #fff !important;
}

CSS;
    }

    private static function solidExtraCss(): string
    {
        return <<<'CSS'
/* Solid: szekció- és kártyakontraszt – világos = sötét szöveg, sötét = fehér szöveg,
   fehér kártyán mindig sötét szöveg (még sötét szülőben is, pl. quote). */

/* —— Világos szekciók —— */
.ts-features,
.ts-icon-list,
.ts-social,
.ts-logo-row,
.ts-faq,
.ts-gallery,
.ts-map,
.ts-steps,
.ts-how-to-book,
.ts-checkin,
.ts-stats,
.ts-buttons,
.ts-text,
.ts-split,
.ts-contact,
.ts-legal,
.ts-nearby,
.ts-amenities,
.ts-pricing,
.ts-dyn-featured,
.ts-dyn-search,
.ts-dyn-cards,
.ts-dyn-list,
.ts-dyn-gallery,
.ts-dyn-appointment,
.ts-dyn-contact-form,
.ts-dyn-map,
.ts-dyn-details {
  background: var(--color-light);
  color: var(--color-text);
}

.ts-buttons,
.ts-text,
.ts-split,
.ts-contact,
.ts-legal,
.ts-nearby,
.ts-amenities,
.ts-pricing {
  background: #fff;
  color: var(--color-text);
}

.ts-features,
.ts-icon-list,
.ts-faq,
.ts-dyn-featured,
.ts-dyn-search,
.ts-dyn-cards,
.ts-checkin,
.ts-stats {
  background: var(--color-surface);
  color: var(--color-text);
}

/* —— Sötét szekciók —— */
.ts-footer,
.ts-footer-min,
.ts-cta,
.ts-dyn-booking-cta,
.ts-banner:not([data-media-url]),
.ts-video,
.ts-quote,
.ts-hero,
.ts-hero-slider {
  color: #fff !important;
}

.ts-cta,
.ts-dyn-booking-cta,
.ts-banner:not([data-media-url]),
.ts-video,
.ts-quote {
  background: var(--color-primary-mid);
}

.ts-footer,
.ts-footer-min {
  background: var(--color-primary);
}

.ts-footer a,
.ts-footer-min a,
.ts-cta a:not(.ts-btn):not(.ts-dyn-booking-cta__inner a),
.ts-quote a:not(.ts-btn),
.ts-video a:not(.ts-btn),
.ts-banner a:not(.ts-btn),
.ts-hero a:not(.ts-btn),
.ts-hero-slider a:not(.ts-btn) {
  color: #fff;
}

.ts-cta__lead,
.ts-banner p,
.ts-video__lead,
.ts-quote p,
.ts-cta__lead,
.ts-banner p,
.ts-video__lead,
.ts-quote p,
.ts-hero__lead,
.ts-hero-slider__lead {
  color: #fff !important;
  opacity: 1;
}

.ts-footer p,
.ts-footer-min p {
  color: #fff !important;
  opacity: 0.88;
}

.ts-hero__title,
.ts-hero__eyebrow,
.ts-hero-slider__title,
.ts-hero-slider__eyebrow,
.ts-hero-slider__inner {
  color: #fff !important;
  opacity: 1 !important;
}

/* —— Tömör fehér kártyák: mindig sötét szöveg —— */
.ts-faq__item,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-stats article,
.ts-logo-row__item,
.ts-pricing__table,
.ts-quote__card,
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside {
  background: #fff !important;
  color: var(--color-text) !important;
  border: none !important;
  box-shadow: var(--shadow-soft) !important;
}

.ts-faq__item *:not(a.ts-btn):not(.ts-btn),
.ts-feature *:not(a.ts-btn):not(.ts-btn),
.ts-icon-item *:not(a.ts-btn):not(.ts-btn),
.ts-steps__grid li *:not(span):not(a.ts-btn):not(.ts-btn),
.ts-checkin article *:not(a.ts-btn):not(.ts-btn),
.ts-stats article *:not(a.ts-btn):not(.ts-btn),
.ts-logo-row__item *,
.ts-pricing__table *:not(a.ts-btn):not(.ts-btn),
.ts-quote__card *:not(a.ts-btn):not(.ts-btn),
.ts-dyn-card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-card__cta),
.ts-dyn-list__row *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-list__cta),
.ts-dyn-appointment__card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-appointment__cta),
.ts-dyn-search__form *:not(button),
.ts-dyn-contact-form__form *:not(button),
.ts-dyn-details aside *:not(a.ts-btn):not(.ts-btn) {
  color: var(--color-text);
}

/* Kártyán belüli linkek: accent, ne örököljenek fehéret */
.ts-faq__item a:not(.ts-btn),
.ts-feature a:not(.ts-btn),
.ts-icon-item a:not(.ts-btn),
.ts-quote__card a:not(.ts-btn),
.ts-dyn-card a:not(.ts-btn):not(.ts-dyn-card__cta),
.ts-dyn-list__row a:not(.ts-btn):not(.ts-dyn-list__cta),
.ts-dyn-details aside a:not(.ts-btn),
.ts-contact a,
.ts-dyn-contact-form__info a,
.ts-dyn-map__copy a {
  color: var(--color-accent) !important;
}

/* CTA / dyn gombok a kártyán: fehér szöveg a színes gombon */
.ts-steps__grid span,
.ts-dyn-appointment__cta,
.ts-dyn-card__cta:not(.ts-dyn-card__cta--outline),
.ts-dyn-list__cta {
  color: #fff !important;
}

/* Label / eyebrow: muted, de olvasható a light/surface-en */
.ts-features__lead,
.ts-icon-list__lead,
.ts-faq__lead,
.ts-steps__lead,
.ts-dyn-featured__eyebrow,
.ts-dyn-cards__lead,
.ts-dyn-list__lead,
.ts-dyn-search__lead {
  color: var(--color-muted, #64748b);
  opacity: 1;
}

.ts-dyn-search__field label,
.ts-dyn-contact-form__form label,
.field-label {
  color: var(--color-muted, #64748b);
}

CSS;
    }

    private static function matteExtraCss(): string
    {
        return <<<'CSS'
/* Matte: puha, átlátszatlan, fénymentes felületek (nincs blur) */
.ts-faq__item,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-stats article,
.ts-logo-row__item,
.ts-pricing__table,
.ts-quote__card,
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside {
  background: color-mix(in srgb, #ffffff 88%, var(--color-surface)) !important;
  border: none !important;
  box-shadow: var(--shadow-soft) !important;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
}

.ts-features,
.ts-icon-list,
.ts-faq,
.ts-social,
.ts-dyn-featured,
.ts-dyn-search {
  background: var(--color-surface);
}

CSS;
    }

    private static function glassExtraCss(): string
    {
        return <<<'CSS'
/* Glass: üvegkártyák + lágy, nem levágódó szekcióháttér.
   Egyetlen felülről érkező mosás, ami a szekció közepén már átlátszó – nincs alsó blob. */
.ts-features,
.ts-icon-list,
.ts-faq,
.ts-social,
.ts-logo-row,
.ts-gallery,
.ts-map,
.ts-steps,
.ts-how-to-book,
.ts-contact,
.ts-dyn-featured,
.ts-dyn-search,
.ts-dyn-contact-form,
.ts-dyn-cards,
.ts-dyn-list {
  background-color: var(--color-light);
  background-image: radial-gradient(
    ellipse 110% 85% at 50% -15%,
    color-mix(in srgb, var(--color-accent) 12%, transparent),
    transparent 68%
  );
  background-repeat: no-repeat;
  background-size: 100% 100%;
}

.ts-faq__item,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-stats article,
.ts-logo-row__item,
.ts-pricing__table,
.ts-quote__card,
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside {
  background: color-mix(in srgb, #ffffff 62%, transparent) !important;
  backdrop-filter: blur(16px) saturate(1.3);
  -webkit-backdrop-filter: blur(16px) saturate(1.3);
  border-color: color-mix(in srgb, #ffffff 65%, var(--color-accent) 14%) !important;
  box-shadow: var(--shadow-soft), inset 0 1px 0 color-mix(in srgb, #ffffff 70%, transparent) !important;
}

/* Fejléc: színes üveg — csak a régi dinamikus nav, ne a site builder fejléc */
.site-nav:not([data-site-nav]),
.ts-header-simple:not([data-site-nav]),
.ts-header-bar:not([data-site-nav]) {
  background: color-mix(in srgb, var(--color-primary) 68%, transparent) !important;
  backdrop-filter: blur(18px) saturate(1.4);
  -webkit-backdrop-filter: blur(18px) saturate(1.4);
  border-bottom: 1px solid color-mix(in srgb, #ffffff 18%, transparent) !important;
}

@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
  .ts-faq__item,
  .ts-feature,
  .ts-icon-item,
  .ts-steps__grid li,
  .ts-checkin article,
  .ts-stats article,
  .ts-logo-row__item,
  .ts-pricing__table,
  .ts-quote__card,
  .ts-dyn-card,
  .ts-dyn-list__row,
  .ts-dyn-appointment__card,
  .ts-dyn-search__form,
  .ts-dyn-contact-form__form,
  .ts-dyn-details aside {
    background: color-mix(in srgb, #ffffff 92%, var(--color-light)) !important;
  }

  .site-nav:not([data-site-nav]),
  .ts-header-simple:not([data-site-nav]),
  .ts-header-bar:not([data-site-nav]) {
    background: color-mix(in srgb, var(--color-primary) 94%, transparent) !important;
  }
}

CSS;
    }

    private static function sharedCss(string $comment): string
    {
        return <<<CSS
/* {$comment} */

/* —— Gombok / CTA-szerű elemek (forma + szín, blokk CSS felülírható custom CSS-sel) —— */
.ts-btn,
.ts-nav-cta,
.ts-social__links a,
.ts-dyn-appointment__cta,
.ts-dyn-card__cta,
.ts-dyn-featured__actions a.is-primary,
.ts-dyn-list__cta,
.ts-dyn-booking-cta__inner a,
.ts-dyn-details__actions a.is-primary,
.ts-dyn-search__actions button,
.ts-dyn-contact-form__form button,
.btn-primary,
.btn-dark,
.btn-ghost,
.ts-banner .ts-btn,
.ts-bg-section .ts-btn,
.ts-split .ts-btn {
  border-radius: var(--radius-control) !important;
  letter-spacing: var(--tracking-btn) !important;
  padding: var(--btn-pad-y) var(--btn-pad-x) !important;
  font-size: var(--btn-font-size) !important;
  font-weight: var(--btn-font-weight) !important;
  text-transform: var(--btn-text-transform) !important;
  box-shadow: var(--btn-shadow) !important;
  text-decoration: none !important;
  transition: background-color .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease !important;
}

.ts-btn--primary,
.ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse),
.ts-nav-cta,
.ts-social__links a,
.ts-dyn-appointment__cta,
.ts-dyn-card__cta:not(.ts-dyn-card__cta--outline),
.ts-dyn-featured__actions a.is-primary,
.ts-dyn-list__cta,
.ts-dyn-booking-cta__inner a,
.ts-dyn-details__actions a.is-primary,
.ts-dyn-search__actions button,
.ts-dyn-contact-form__form button,
.btn-primary,
.btn-dark,
.ts-banner .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse),
.ts-bg-section .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse),
.ts-split .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse) {
  background: var(--btn-primary-bg, var(--btn-bg)) !important;
  color: var(--btn-primary-fg, var(--btn-fg)) !important;
  border: var(--border-width) solid var(--btn-primary-border, var(--btn-border)) !important;
}

.ts-btn--primary:hover,
.ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse):hover,
.ts-nav-cta:hover,
.ts-social__links a:hover,
.ts-dyn-appointment__cta:hover,
.ts-dyn-card__cta:not(.ts-dyn-card__cta--outline):hover,
.ts-dyn-featured__actions a.is-primary:hover,
.ts-dyn-list__cta:hover,
.ts-dyn-booking-cta__inner a:hover,
.ts-dyn-details__actions a.is-primary:hover,
.ts-dyn-search__actions button:hover,
.ts-dyn-contact-form__form button:hover,
.btn-primary:hover,
.btn-dark:hover,
.ts-banner .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse):hover,
.ts-bg-section .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse):hover,
.ts-split .ts-btn:not(.ts-btn--ghost):not(.ts-btn--secondary):not(.ts-btn--inverse):hover {
  background: var(--btn-primary-hover-bg, var(--btn-hover-bg)) !important;
  color: var(--btn-primary-fg, var(--btn-fg)) !important;
}

.ts-btn--secondary,
.ts-btn--ghost,
.btn-ghost,
.ts-dyn-card__cta--outline {
  background: var(--btn-secondary-bg, var(--btn-ghost-bg)) !important;
  color: var(--btn-secondary-fg, var(--btn-ghost-fg)) !important;
  border: var(--border-width) solid var(--btn-secondary-border, var(--btn-ghost-border)) !important;
  box-shadow: none !important;
}

.ts-btn--secondary:hover,
.ts-btn--ghost:hover,
.btn-ghost:hover,
.ts-dyn-card__cta--outline:hover {
  background: var(--btn-secondary-hover-bg, var(--btn-secondary-bg, transparent)) !important;
  border-color: color-mix(in srgb, var(--btn-secondary-fg, var(--btn-ghost-fg)) 45%, transparent) !important;
}

.ts-btn--inverse {
  background: var(--btn-inverse-bg) !important;
  color: var(--btn-inverse-fg) !important;
  border: var(--border-width) solid var(--btn-inverse-border) !important;
}

.ts-btn--inverse:hover {
  background: var(--btn-inverse-hover-bg) !important;
  color: var(--btn-inverse-fg) !important;
}

/* Sötét szekciókon a másodlagos (outline) gomb: inverse tokenek, ha nincs explicit inverse osztály */
.ts-hero .ts-btn--ghost:not(.ts-btn--inverse),
.ts-hero .ts-btn--secondary:not(.ts-btn--inverse),
.ts-hero-slider .ts-btn--ghost:not(.ts-btn--inverse),
.ts-hero-slider .ts-btn--secondary:not(.ts-btn--inverse),
.ts-cta .ts-btn--ghost:not(.ts-btn--inverse),
.ts-cta .ts-btn--secondary:not(.ts-btn--inverse),
.ts-banner .ts-btn--ghost:not(.ts-btn--inverse),
.ts-banner .ts-btn--secondary:not(.ts-btn--inverse),
.ts-bg-section .ts-btn--ghost:not(.ts-btn--inverse),
.ts-bg-section .ts-btn--secondary:not(.ts-btn--inverse),
.ts-video .ts-btn--ghost:not(.ts-btn--inverse),
.ts-video .ts-btn--secondary:not(.ts-btn--inverse),
.ts-quote .ts-btn--ghost:not(.ts-btn--inverse),
.ts-quote .ts-btn--secondary:not(.ts-btn--inverse),
.ts-dyn-booking-cta .ts-btn--ghost:not(.ts-btn--inverse),
.ts-dyn-booking-cta .ts-btn--secondary:not(.ts-btn--inverse) {
  color: var(--btn-on-dark-ghost-fg, var(--btn-inverse-fg)) !important;
  border-color: var(--btn-on-dark-ghost-border, var(--btn-inverse-border, color-mix(in srgb, #ffffff 40%, transparent))) !important;
  background: transparent !important;
  box-shadow: none !important;
}

.ts-dyn-card__actions a:not(.ts-dyn-card__cta):not(.is-primary),
.ts-dyn-featured__actions a:not(.is-primary),
.ts-dyn-details__actions a:not(.is-primary),
.ts-contact a,
.ts-dyn-contact-form__info a,
.ts-dyn-map__copy a {
  color: var(--link-color) !important;
}

.ts-steps__grid span {
  border-radius: var(--radius-control) !important;
  background: var(--btn-bg) !important;
  color: var(--btn-fg) !important;
}

/* —— Chrome / sötét és világos szekció felületek —— */
/* Szekció háttér/szín: NINCS !important – a Style Manager és az oldal CSS felülírhatja.
   A gombok továbbra is !important-tal jönnek fentebb. */
.ts-footer,
.ts-footer-min {
  background: var(--color-primary);
  color: #fff;
}

.ts-cta,
.ts-dyn-booking-cta,
.ts-banner:not([data-media-url]),
.ts-video,
.ts-quote {
  background: var(--color-primary-mid);
  color: #fff;
}

.ts-features,
.ts-icon-list,
.ts-social,
.ts-logo-row,
.ts-dyn-featured,
.ts-dyn-search,
.ts-faq,
.ts-gallery,
.ts-map,
.ts-steps,
.ts-how-to-book {
  background: var(--color-light);
  color: var(--color-text);
}

.ts-buttons,
.ts-text,
.ts-split,
.ts-contact,
.ts-legal,
.ts-nearby,
.ts-amenities,
.ts-pricing {
  background: #fff;
  color: var(--color-text);
}

.ts-dyn-details aside,
.ts-pricing__head {
  background: var(--color-light);
}

/* —— Kártyák / felületek (dyn + statikus) —— */
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-stats article,
.ts-logo-row__item,
.ts-pricing__table,
.ts-quote__card,
.ts-faq__item {
  border-radius: var(--radius-card) !important;
  border-width: var(--card-border-width) !important;
  border-style: solid !important;
  border-color: color-mix(in srgb, var(--color-text) var(--border-strength), transparent) !important;
  box-shadow: var(--shadow-soft) !important;
}

/* Világos felületű kártyák: alap kontraszt (Style Manager #id felülírhatja – nincs !important) */
.ts-dyn-card,
.ts-dyn-list__row,
.ts-dyn-appointment__card,
.ts-dyn-search__form,
.ts-dyn-contact-form__form,
.ts-dyn-details aside,
.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-logo-row__item,
.ts-pricing__table,
.ts-faq__item,
.ts-checkin article {
  background-color: #fff;
  color: var(--color-text);
}

.ts-dyn-card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-card__cta),
.ts-dyn-list__row *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-list__cta),
.ts-dyn-appointment__card *:not(a.ts-btn):not(.ts-btn):not(.ts-dyn-appointment__cta),
.ts-dyn-search__form *:not(button),
.ts-dyn-contact-form__form *:not(button),
.ts-dyn-details aside *:not(a.ts-btn):not(.ts-btn),
.ts-feature *:not(a.ts-btn):not(.ts-btn),
.ts-icon-item *:not(a.ts-btn):not(.ts-btn),
.ts-steps__grid li *:not(span):not(a.ts-btn):not(.ts-btn),
.ts-logo-row__item *,
.ts-faq__item *:not(a.ts-btn):not(.ts-btn),
.ts-checkin article *:not(a.ts-btn):not(.ts-btn) {
  color: inherit;
}

.ts-steps__grid span,
.ts-dyn-appointment__cta,
.ts-dyn-card__cta:not(.ts-dyn-card__cta--outline),
.ts-dyn-list__cta {
  color: #fff !important;
}

.ts-feature,
.ts-icon-item,
.ts-steps__grid li,
.ts-checkin article,
.ts-stats article,
.ts-logo-row__item,
.ts-quote__card,
.ts-dyn-card__body,
.ts-dyn-appointment__card,
.ts-dyn-details aside,
.ts-dyn-search__form,
.ts-dyn-contact-form__form {
  padding: var(--space-block) !important;
}

/* GYIK: kártyás accordion – egységes belső tér és lista-hézag minden presetnél */
.ts-faq__list {
  display: flex !important;
  flex-direction: column !important;
  gap: var(--gap-sm) !important;
}

.ts-faq__item {
  padding: calc(var(--space-block) * 0.85) var(--space-block) !important;
  margin: 0 !important;
  border-top: none !important;
  border-bottom: none !important;
  background: #fff;
  color: var(--color-text);
}

.ts-faq__item p {
  margin-top: 0.75rem !important;
  margin-bottom: 0 !important;
}

/* —— Média keretek —— */
.ts-dyn-gallery__grid figure,
.ts-dyn-map__frame,
.ts-dyn-featured__media img,
.ts-dyn-card__media img,
.ts-gallery__item,
.ts-gallery__item img,
.ts-map__frame,
.ts-video__frame,
.ts-split__media,
.ts-split__media img {
  border-radius: var(--radius-media) !important;
  overflow: hidden;
}

/* —— Szekció térköz (preset: --section-y, --section-x = oldalsó safe zone / gutter) —— */
.ts-hero,
.ts-cta,
.ts-banner,
.ts-bg-section,
.ts-features,
.ts-icon-list,
.ts-faq,
.ts-gallery,
.ts-map,
.ts-social,
.ts-checkin,
.ts-buttons,
.ts-text,
.ts-split,
.ts-stats,
.ts-quote,
.ts-pricing,
.ts-amenities,
.ts-nearby,
.ts-steps,
.ts-how-to-book,
.ts-legal,
.ts-logo-row,
.ts-video,
.ts-contact,
.ts-dyn-cards,
.ts-dyn-list,
.ts-dyn-featured,
.ts-dyn-booking-cta,
.ts-dyn-search,
.ts-dyn-details,
.ts-dyn-gallery,
.ts-dyn-contact-form,
.ts-dyn-map,
.ts-dyn-appointment,
.section {
  padding-top: var(--section-y) !important;
  padding-bottom: var(--section-y) !important;
  padding-left: max(var(--section-x), 1.5rem) !important;
  padding-right: max(var(--section-x), 1.5rem) !important;
  box-sizing: border-box !important;
}

/*
 * Layout szekciók (1/2/3 oszlop): kisebb alap függőleges padding, mint a tartalom-szekciók.
 * Nincs !important, így a Dimension panelben nullázható vagy növelhető.
 */
.ts-layout {
  padding-top: var(--section-y-layout, 1.25rem);
  padding-bottom: var(--section-y-layout, 1.25rem);
  padding-left: max(var(--section-x), 1.5rem);
  padding-right: max(var(--section-x), 1.5rem);
  box-sizing: border-box;
}

/* Layoutba ágyazott háttér-blokkok: az ID-s Grapes padding:0 se nyerhessen */
.ts-layout__col > .ts-cta,
.ts-layout__col > .ts-gallery,
.ts-layout__col > .ts-steps,
.ts-layout__col > .ts-features,
.ts-layout__col > .ts-icon-list,
.ts-layout__col > .ts-quote,
.ts-layout__col > .ts-banner,
.ts-layout__col > .ts-dyn-booking-cta,
.ts-layout__col > .ts-dyn-cards,
.ts-layout__col > .ts-dyn-gallery,
.ts-layout__col > section {
  padding-left: max(var(--section-x), 1.5rem) !important;
  padding-right: max(var(--section-x), 1.5rem) !important;
}

/* Oszlopba ágyazott szöveg/kép: ne örökölje a teljes szekció térközt */
.ts-layout__col > .ts-text,
.ts-layout__col > .ts-image,
.ts-layout__col > .ts-spacer {
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

.ts-hero {
  min-height: clamp(28rem, 72vh, 46rem);
}

/* Surface overlay: háttérkép/szín fölött, cím és kártyák alatt */
section:not(.ts-hero-slider)[data-overlay],
section:not(.ts-hero-slider):has(> .ts-surface-overlay),
section:not(.ts-hero-slider):has(> [data-ts-bg-overlay]:not(.ts-hero-slider__overlay)) {
  position: relative !important;
  isolation: isolate;
}

section:not(.ts-hero-slider) > .ts-surface-overlay,
section:not(.ts-hero-slider) > [data-ts-bg-overlay]:not(.ts-hero-slider__overlay):not(.ts-hero__overlay) {
  position: absolute !important;
  inset: 0 !important;
  z-index: 1 !important;
  pointer-events: none !important;
  background: var(--ts-overlay-color, var(--color-primary)) !important;
}

section:not(.ts-hero-slider) > .ts-surface-overlay ~ :not(style):not(.ts-hero__media):not(.ts-banner__media):not(.ts-bg-section__media),
section:not(.ts-hero-slider) > [data-ts-bg-overlay]:not(.ts-hero-slider__overlay) ~ :not(style):not(.ts-hero__media):not(.ts-banner__media):not(.ts-bg-section__media) {
  position: relative !important;
  z-index: 2 !important;
}

.ts-hero > .ts-hero__media {
  position: absolute !important;
  inset: 0 !important;
  z-index: 0 !important;
  width: auto !important;
  height: auto !important;
  pointer-events: none;
}

.ts-hero[data-layout="split"] > .ts-hero__media {
  inset: auto !important;
  top: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  left: 50% !important;
  width: 50% !important;
}

/* Hero slider: full-bleed szekció; a Stíluskezelő paddingje → --ts-hero-pad-* (tartalomréteg) */
.ts-hero-slider {
  padding: 0 !important;
}

.ts-hero-slider__slide {
  padding: 0 !important;
  box-sizing: border-box !important;
}

.ts-hero-slider__inner {
  max-width: var(--content-max) !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-top: var(--ts-hero-pad-top, max(var(--section-y), 4.5rem)) !important;
  padding-bottom: var(--ts-hero-pad-bottom, max(var(--section-y), 4.5rem)) !important;
  padding-left: var(--ts-hero-pad-left, max(var(--section-x), 1.5rem)) !important;
  padding-right: var(--ts-hero-pad-right, max(var(--section-x), 1.5rem)) !important;
  box-sizing: border-box !important;
}

/* —— Chrome: fejléc / lábléc —— */
.ts-nav-inner,
.ts-header-simple {
  max-width: var(--content-max) !important;
  gap: var(--gap-md) !important;
  padding-left: var(--section-x) !important;
  padding-right: var(--section-x) !important;
}

.ts-nav-links {
  gap: var(--gap-md) !important;
}

.ts-menu-placeholder {
  border-radius: var(--radius-control) !important;
}

.ts-footer__grid,
.ts-footer-min {
  max-width: var(--content-max) !important;
  gap: var(--gap-lg) !important;
  padding-top: var(--section-y) !important;
  padding-bottom: calc(var(--section-y) * 0.55) !important;
  padding-left: var(--section-x) !important;
  padding-right: var(--section-x) !important;
}

.ts-footer__copy {
  padding-left: var(--section-x) !important;
  padding-right: var(--section-x) !important;
}

/* —— Belső ritmus / gap —— */
.ts-hero__actions,
.ts-buttons__row,
.ts-cta__inner,
.ts-social__links,
.ts-dyn-card__actions,
.ts-dyn-featured__actions,
.ts-dyn-details__actions,
.ts-amenities__row {
  gap: var(--gap-sm) !important;
}

.ts-dyn-cards__grid,
.ts-dyn-appointment__grid,
.ts-dyn-featured__grid,
.ts-dyn-details__grid,
.ts-dyn-contact-form__inner,
.ts-dyn-map__inner,
.ts-dyn-gallery__grid,
.ts-dyn-list__rows,
.ts-features__grid,
.ts-icon-list__grid,
.ts-checkin__grid,
.ts-stats__grid,
.ts-gallery__grid,
.ts-logo-row__grid,
.ts-nearby__grid,
.ts-steps__grid,
.ts-split__inner,
.ts-map__inner {
  gap: var(--gap-md) !important;
}

.ts-dyn-cards__inner,
.ts-dyn-list__inner,
.ts-dyn-featured__inner,
.ts-dyn-booking-cta__inner,
.ts-dyn-search__inner,
.ts-dyn-details__inner,
.ts-dyn-gallery__inner,
.ts-dyn-contact-form__inner,
.ts-dyn-map__inner,
.ts-dyn-appointment__inner,
.ts-hero__inner,
.ts-buttons__inner,
.ts-cta__inner,
.ts-features__inner,
.ts-icon-list__inner,
.ts-gallery__inner,
.ts-map__inner,
.ts-checkin__inner,
.ts-split__inner,
.ts-stats__inner,
.ts-quote__inner,
.ts-pricing__inner,
.ts-amenities__inner,
.ts-nearby__inner,
.ts-steps__inner,
.ts-logo-row__inner,
.ts-video__inner,
.ts-contact__inner,
.ts-banner__inner,
.ts-bg-section__inner,
.ts-social__inner,
.ts-text__inner,
.ts-legal__inner,
.section-narrow {
  max-width: var(--content-max) !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* GYIK szűkebb olvasási sáv – ne nyúljon ki a teljes content-max szélességre */
.ts-faq__inner {
  max-width: min(48rem, var(--content-max)) !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* —— Tipográfia lépték —— */
.ts-hero__eyebrow,
.ts-dyn-card__type,
.ts-dyn-list__type,
.ts-dyn-featured__type,
.ts-dyn-featured__eyebrow,
.ts-dyn-appointment__role,
.ts-dyn-search__field label,
.ts-dyn-contact-form__form label,
.ts-dyn-details__eyebrow,
.ts-dyn-details__type,
.ts-footer__label,
.ts-faq__item summary,
.field-label {
  letter-spacing: var(--tracking-label) !important;
  font-weight: var(--label-weight) !important;
}

.font-display,
h1, h2, h3,
.ts-hero__title,
.ts-dyn-cards__title,
.ts-dyn-list__title,
.ts-dyn-featured__body h2,
.ts-dyn-appointment__title,
.ts-dyn-search__title,
.ts-dyn-details__grid h2,
.ts-dyn-contact-form__info h2,
.ts-dyn-map__copy h2,
.ts-features h2,
.ts-icon-list h2,
.ts-split h2,
.ts-steps h2,
.ts-checkin h2,
.ts-stats h2,
.ts-gallery h2,
.ts-faq h2,
.ts-cta h2,
.ts-banner h2,
.ts-footer__brand,
.ts-nav-brand,
.section-title {
  font-weight: var(--heading-weight) !important;
  letter-spacing: var(--heading-tracking) !important;
}

.ts-hero__title,
.ts-dyn-cards__title,
.ts-dyn-list__title,
.ts-dyn-featured__body h2,
.ts-dyn-appointment__title,
.ts-features h2,
.ts-features__title,
.ts-icon-list h2,
.ts-icon-list__title,
.ts-split h2,
.ts-split__copy h2,
.ts-steps h2,
.ts-cta h2,
.ts-cta__title,
.ts-faq h2,
.ts-gallery h2,
.ts-checkin h2,
.ts-stats h2,
.ts-banner h2,
.ts-map__title,
.ts-pricing h2,
.ts-video h2,
.ts-social h2,
.ts-contact h2,
.ts-text h2,
.ts-nearby h2,
.ts-amenities h2,
.ts-logo-row h2,
.ts-how-to-book h2,
.ts-legal h2,
.ts-dyn-search__title,
.ts-dyn-details__grid h2,
.ts-dyn-contact-form__info h2,
.ts-dyn-map__copy h2,
.ts-dyn-gallery__title,
.ts-dyn-booking-cta__inner h2,
.section-title {
  font-size: var(--type-section-title-size, var(--heading-size)) !important;
  line-height: 1.15 !important;
}

/* Szekció leírás / lead */
.ts-hero__lead,
.ts-features__lead,
.ts-icon-list__lead,
.ts-faq__lead,
.ts-steps__lead,
.ts-pricing__lead,
.ts-video__lead,
.ts-social__lead,
.ts-gallery__lead,
.ts-checkin__lead,
.ts-stats__lead,
.ts-map__lead,
.ts-nearby__lead,
.ts-amenities__lead,
.ts-logo-row__lead,
.ts-how-to-book__lead,
.ts-contact__lead,
.ts-text__lead,
.ts-cta__lead,
.ts-banner__lead,
.ts-dyn-cards__lead,
.ts-dyn-list__lead,
.ts-dyn-featured__lead,
.ts-dyn-search__lead,
.ts-dyn-appointment__lead {
  font-size: var(--type-section-lead-size) !important;
  line-height: 1.55 !important;
}

/* Kártya címsor */
.ts-feature h3,
.ts-feature__title,
.ts-icon-item h3,
.ts-icon-item__title,
.ts-steps__grid h3,
.ts-checkin article h3,
.ts-stats article h3,
.ts-faq__item summary,
.ts-pricing__table h3,
.ts-nearby article h3,
.ts-amenities li strong,
.ts-dyn-card__title,
.ts-dyn-list__row h3,
.ts-dyn-appointment__card h3,
.ts-dyn-details aside h3 {
  font-size: var(--type-card-title-size) !important;
  line-height: 1.3 !important;
}

/* Kártya leírás */
.ts-feature p,
.ts-feature__text,
.ts-icon-item p,
.ts-steps__grid p,
.ts-checkin article p,
.ts-stats article p,
.ts-faq__item p,
.ts-pricing__table p,
.ts-nearby article p,
.ts-dyn-card__desc,
.ts-dyn-list__row p,
.ts-dyn-appointment__card p,
.ts-dyn-details aside p {
  font-size: var(--type-card-body-size) !important;
  line-height: 1.55 !important;
}

/* —— Form mezők —— */
.field,
.ts-dyn-search__field input,
.ts-dyn-search__field select,
.ts-dyn-contact-form__form input:not([type="checkbox"]):not([type="hidden"]):not([type="radio"]),
.ts-dyn-contact-form__form textarea {
  border-radius: var(--radius-control) !important;
  border-width: var(--border-width) !important;
}

/* Többsoros mező: pill radius helyett max 20px */
.field textarea,
.ts-dyn-contact-form__form textarea,
.ts-dyn-search__field textarea,
textarea.field {
  border-radius: min(20px, var(--radius-control)) !important;
}

/* Hero elrendezés-variánsok (builder data-layout) */
.ts-hero[data-layout="center"] {
  align-items: center !important;
  text-align: center !important;
}

.ts-hero[data-layout="center"] .ts-hero__inner {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
}

.ts-hero[data-layout="center"] .ts-hero__lead {
  margin-left: auto !important;
  margin-right: auto !important;
}

.ts-hero[data-layout="center"] .ts-hero__actions {
  justify-content: center !important;
}

.ts-hero[data-layout="split"] {
  align-items: center !important;
  background: var(--ts-overlay-color, var(--color-primary));
  color: #fff;
}

.ts-hero[data-layout="split"] .ts-hero__media {
  inset: auto !important;
  top: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  left: 50% !important;
  width: 50% !important;
}

/* Asztali osztott: nincs overlay a képen – a bal oldal a szekció háttérszíne */
.ts-hero[data-layout="split"] .ts-hero__overlay {
  opacity: 0 !important;
  background: none !important;
}

.ts-hero[data-layout="split"] .ts-hero__inner {
  max-width: var(--content-max) !important;
  width: 100% !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-right: max(1.25rem, 50%) !important;
  box-sizing: border-box !important;
}

@media (max-width: 800px) {
  .ts-hero[data-layout="split"] {
    align-items: flex-end !important;
  }

  .ts-hero[data-layout="split"] .ts-hero__media {
    inset: 0 !important;
    width: auto !important;
    left: 0 !important;
  }

  .ts-hero[data-layout="split"] .ts-hero__overlay {
    opacity: var(--ts-hero-overlay-opacity, 0.85) !important;
    background: linear-gradient(180deg, color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) 28%, transparent) 0%, color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) 78%, transparent) 70%, color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) 94%, transparent) 100%) !important;
  }

  .ts-hero[data-layout="split"] .ts-hero__inner {
    max-width: var(--content-max) !important;
    width: 100% !important;
    padding-right: 0 !important;
  }
}

/* Banner / háttérszekció elrendezések */
.ts-banner[data-layout="center"],
.ts-bg-section[data-layout="center"] {
  text-align: center !important;
}

.ts-banner[data-layout="center"] .ts-banner__inner,
.ts-bg-section[data-layout="center"] .ts-bg-section__inner {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
}

.ts-banner[data-layout="center"] p,
.ts-bg-section[data-layout="center"] .ts-bg-section__body {
  margin-left: auto !important;
  margin-right: auto !important;
}

.ts-banner[data-layout="split"],
.ts-bg-section[data-layout="split"] {
  background: var(--ts-overlay-color, var(--color-primary));
  color: #fff;
}

.ts-banner[data-layout="split"] .ts-banner__media,
.ts-bg-section[data-layout="split"] .ts-bg-section__media {
  inset: auto !important;
  top: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  left: 50% !important;
  width: 50% !important;
}

.ts-banner[data-layout="split"] .ts-banner__overlay,
.ts-bg-section[data-layout="split"] .ts-bg-section__overlay {
  opacity: 0 !important;
  background: none !important;
}

.ts-banner[data-layout="split"] .ts-banner__inner,
.ts-bg-section[data-layout="split"] .ts-bg-section__inner {
  max-width: var(--content-max) !important;
  width: 100% !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-right: max(1.25rem, 50%) !important;
  box-sizing: border-box !important;
}

@media (max-width: 800px) {
  .ts-banner[data-layout="split"] .ts-banner__media,
  .ts-bg-section[data-layout="split"] .ts-bg-section__media {
    inset: 0 !important;
    width: auto !important;
    left: 0 !important;
  }

  .ts-banner[data-layout="split"] .ts-banner__overlay {
    opacity: var(--ts-banner-overlay-opacity, 0.55) !important;
    background: var(--ts-overlay-color, var(--color-primary)) !important;
  }

  .ts-bg-section[data-layout="split"] .ts-bg-section__overlay {
    opacity: var(--ts-bg-section-overlay-opacity, 0.55) !important;
    background: var(--ts-overlay-color, var(--color-primary)) !important;
  }

  .ts-banner[data-layout="split"] .ts-banner__inner,
  .ts-bg-section[data-layout="split"] .ts-bg-section__inner {
    padding-right: 0 !important;
  }
}

/* CTA elrendezések */
.ts-cta[data-layout="center"] .ts-cta__inner {
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  text-align: center !important;
}

.ts-cta[data-layout="stack"] .ts-cta__inner {
  flex-direction: column !important;
  align-items: flex-start !important;
  justify-content: flex-start !important;
  text-align: left !important;
}

/* Features / ikonlista oszlopszám */
.ts-features[data-columns="2"] .ts-features__grid,
.ts-icon-list[data-columns="2"] .ts-icon-list__grid {
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
}

.ts-features[data-columns="3"] .ts-features__grid,
.ts-icon-list[data-columns="3"] .ts-icon-list__grid {
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
}

.ts-features[data-columns="4"] .ts-features__grid,
.ts-icon-list[data-columns="4"] .ts-icon-list__grid {
  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
}

@media (max-width: 900px) {
  .ts-features[data-columns="3"] .ts-features__grid,
  .ts-features[data-columns="4"] .ts-features__grid,
  .ts-icon-list[data-columns="3"] .ts-icon-list__grid,
  .ts-icon-list[data-columns="4"] .ts-icon-list__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  }
}

@media (max-width: 640px) {
  .ts-features[data-columns="2"] .ts-features__grid,
  .ts-features[data-columns="3"] .ts-features__grid,
  .ts-features[data-columns="4"] .ts-features__grid,
  .ts-icon-list[data-columns="2"] .ts-icon-list__grid,
  .ts-icon-list[data-columns="3"] .ts-icon-list__grid,
  .ts-icon-list[data-columns="4"] .ts-icon-list__grid {
    grid-template-columns: 1fr !important;
  }

  .ts-cta[data-layout="row"] .ts-cta__inner,
  .ts-cta:not([data-layout]) .ts-cta__inner {
    flex-direction: column !important;
    align-items: flex-start !important;
  }
}

.cal-day {
  border-radius: var(--radius-sm) !important;
}

.cal-grid {
  gap: var(--gap-sm) !important;
}

/* —— Layout szekció (Elementor-szerű: háttér mindig oldalszélig, tartalom boxed / full) —— */
.ts-layout {
  width: 100% !important;
  box-sizing: border-box !important;
}

.ts-layout__inner {
  width: 100% !important;
  box-sizing: border-box !important;
}

.ts-layout__inner--2col {
  display: grid !important;
  gap: var(--gap-md) !important;
  grid-template-columns: 1fr 1fr !important;
}

.ts-layout__inner--3col {
  display: grid !important;
  gap: var(--gap-md) !important;
  grid-template-columns: repeat(3, 1fr) !important;
}

.ts-layout__col {
  min-height: 2rem !important;
}

.ts-layout__placeholder {
  margin: 0 !important;
  padding: 1rem !important;
  border: 1px dashed color-mix(in srgb, var(--color-text) 22%, transparent) !important;
  text-align: center !important;
  opacity: 0.55 !important;
  font-size: 0.85rem !important;
  border-radius: var(--radius-sm) !important;
}

/* Ha az oszlopban már van tartalom, a placeholder eltűnik (builder) */
.ts-layout__col:has(> :not(.ts-layout__placeholder)) > .ts-layout__placeholder {
  display: none !important;
}

/* Publikus oldal / előnézet: soha ne jelenjen meg a „Húzza ide…” felirat */
body.site-shell .ts-layout__placeholder {
  display: none !important;
}

@media (max-width: 800px) {
  .ts-layout__inner--2col,
  .ts-layout__inner--3col {
    grid-template-columns: 1fr !important;
  }
}

/* Boxed: belső tartalom max-width, középre */
.ts-layout[data-section-width="content"] > .ts-layout__inner {
  max-width: var(--content-max) !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Full: tartalom max-width nélkül; oldalsó gutter a preset --section-x értékéből marad */
.ts-layout[data-section-width="full"] > .ts-layout__inner {
  max-width: none !important;
  width: 100% !important;
}

/*
 * Beágyazott widgetek MEGTARTJÁK a saját --section-x paddingjüket.
 * (Ha nulláznánk, háttérszínű blokkoknál a tartalom a szekció széléhez tapadna —
 * Elementorban is a widget/szekció saját paddingje adja a belső légteret.)
 */

/* Teljes szélességű layout: beágyazott widgetek belső max-width feloldása */
.ts-layout[data-section-width="full"] .ts-hero__inner,
.ts-layout[data-section-width="full"] .ts-features__inner,
.ts-layout[data-section-width="full"] .ts-icon-list__inner,
.ts-layout[data-section-width="full"] .ts-cta__inner,
.ts-layout[data-section-width="full"] .ts-buttons__inner,
.ts-layout[data-section-width="full"] .ts-gallery__inner,
.ts-layout[data-section-width="full"] .ts-map__inner,
.ts-layout[data-section-width="full"] .ts-checkin__inner,
.ts-layout[data-section-width="full"] .ts-split__inner,
.ts-layout[data-section-width="full"] .ts-stats__inner,
.ts-layout[data-section-width="full"] .ts-quote__viewport,
.ts-layout[data-section-width="full"] .ts-pricing__inner,
.ts-layout[data-section-width="full"] .ts-amenities__inner,
.ts-layout[data-section-width="full"] .ts-nearby__inner,
.ts-layout[data-section-width="full"] .ts-steps__inner,
.ts-layout[data-section-width="full"] .ts-logo-row__inner,
.ts-layout[data-section-width="full"] .ts-video__inner,
.ts-layout[data-section-width="full"] .ts-contact__inner,
.ts-layout[data-section-width="full"] .ts-banner__inner,
.ts-layout[data-section-width="full"] .ts-bg-section__inner,
.ts-layout[data-section-width="full"] .ts-social__inner,
.ts-layout[data-section-width="full"] .ts-text__inner,
.ts-layout[data-section-width="full"] .ts-legal__inner,
.ts-layout[data-section-width="full"] .ts-faq__inner,
.ts-layout[data-section-width="full"] .ts-dyn-cards__inner,
.ts-layout[data-section-width="full"] .ts-dyn-list__inner,
.ts-layout[data-section-width="full"] .ts-dyn-featured__inner,
.ts-layout[data-section-width="full"] .ts-dyn-booking-cta__inner,
.ts-layout[data-section-width="full"] .ts-dyn-search__inner,
.ts-layout[data-section-width="full"] .ts-dyn-details__inner,
.ts-layout[data-section-width="full"] .ts-dyn-gallery__inner,
.ts-layout[data-section-width="full"] .ts-dyn-contact-form__inner,
.ts-layout[data-section-width="full"] .ts-dyn-map__inner,
.ts-layout[data-section-width="full"] .ts-dyn-appointment__inner {
  max-width: none !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  box-sizing: border-box !important;
}

/* Régi mentések: blokk-szintű data-section-width (visszafelé kompatibilitás) */
[data-section-width="content"] .ts-hero__inner,
[data-section-width="content"] .ts-features__inner,
[data-section-width="content"] .ts-icon-list__inner,
[data-section-width="content"] .ts-cta__inner,
[data-section-width="content"] .ts-buttons__inner,
[data-section-width="content"] .ts-gallery__inner,
[data-section-width="content"] .ts-map__inner,
[data-section-width="content"] .ts-checkin__inner,
[data-section-width="content"] .ts-split__inner,
[data-section-width="content"] .ts-stats__inner,
[data-section-width="content"] .ts-quote__viewport,
[data-section-width="content"] .ts-pricing__inner,
[data-section-width="content"] .ts-amenities__inner,
[data-section-width="content"] .ts-nearby__inner,
[data-section-width="content"] .ts-steps__inner,
[data-section-width="content"] .ts-logo-row__inner,
[data-section-width="content"] .ts-video__inner,
[data-section-width="content"] .ts-contact__inner,
[data-section-width="content"] .ts-banner__inner,
[data-section-width="content"] .ts-bg-section__inner,
[data-section-width="content"] .ts-social__inner,
[data-section-width="content"] .ts-text__inner,
[data-section-width="content"] .ts-legal__inner,
[data-section-width="content"] .ts-faq__inner,
[data-section-width="content"] .ts-dyn-cards__inner,
[data-section-width="content"] .ts-dyn-list__inner,
[data-section-width="content"] .ts-dyn-featured__inner,
[data-section-width="content"] .ts-dyn-booking-cta__inner,
[data-section-width="content"] .ts-dyn-search__inner,
[data-section-width="content"] .ts-dyn-details__inner,
[data-section-width="content"] .ts-dyn-gallery__inner,
[data-section-width="content"] .ts-dyn-contact-form__inner,
[data-section-width="content"] .ts-dyn-map__inner,
[data-section-width="content"] .ts-dyn-appointment__inner {
  max-width: var(--content-max) !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Teljes szélességű legacy blokkok: gutter – layout szekciók KIVÉTEL (Style Manager dönt) */
[data-section-width="full"]:not(.ts-layout) {
  padding-left: max(var(--section-x), 1.25rem) !important;
  padding-right: max(var(--section-x), 1.25rem) !important;
}

[data-section-width="full"] .ts-hero__inner,
[data-section-width="full"] .ts-features__inner,
[data-section-width="full"] .ts-icon-list__inner,
[data-section-width="full"] .ts-cta__inner,
[data-section-width="full"] .ts-buttons__inner,
[data-section-width="full"] .ts-gallery__inner,
[data-section-width="full"] .ts-map__inner,
[data-section-width="full"] .ts-checkin__inner,
[data-section-width="full"] .ts-split__inner,
[data-section-width="full"] .ts-stats__inner,
[data-section-width="full"] .ts-quote__viewport,
[data-section-width="full"] .ts-pricing__inner,
[data-section-width="full"] .ts-amenities__inner,
[data-section-width="full"] .ts-nearby__inner,
[data-section-width="full"] .ts-steps__inner,
[data-section-width="full"] .ts-logo-row__inner,
[data-section-width="full"] .ts-video__inner,
[data-section-width="full"] .ts-contact__inner,
[data-section-width="full"] .ts-banner__inner,
[data-section-width="full"] .ts-bg-section__inner,
[data-section-width="full"] .ts-social__inner,
[data-section-width="full"] .ts-text__inner,
[data-section-width="full"] .ts-legal__inner,
[data-section-width="full"] .ts-faq__inner,
[data-section-width="full"] .ts-dyn-cards__inner,
[data-section-width="full"] .ts-dyn-list__inner,
[data-section-width="full"] .ts-dyn-featured__inner,
[data-section-width="full"] .ts-dyn-booking-cta__inner,
[data-section-width="full"] .ts-dyn-search__inner,
[data-section-width="full"] .ts-dyn-details__inner,
[data-section-width="full"] .ts-dyn-gallery__inner,
[data-section-width="full"] .ts-dyn-contact-form__inner,
[data-section-width="full"] .ts-dyn-map__inner,
[data-section-width="full"] .ts-dyn-appointment__inner {
  max-width: none !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  box-sizing: border-box !important;
}

[data-section-width="full"].ts-hero[data-layout="split"] .ts-hero__inner {
  max-width: none !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
}
CSS;
    }
}

