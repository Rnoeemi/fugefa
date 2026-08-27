<?php

namespace App\Support;

/**
 * Tipográfia szerepek – csak méret (betűtípus továbbra is sans / display).
 */
class SiteTypography
{
    /**
     * @return list<string>
     */
    public static function roleKeys(): array
    {
        return ['section_title', 'section_lead', 'card_title', 'card_body'];
    }

    /**
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            'section_title' => 'Szekció címsor (h2) – pl. split / features / ikonlista',
            'section_lead' => 'Szekció bevezető szöveg a cím alatt',
            'card_title' => 'Feature / lista / dyn kártya címek',
            'card_body' => 'Kártyák törzsszövege',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleHints(): array
    {
        return [
            'section_title' => 'Alap: clamp(1.75rem, 3vw, 2.4rem) – a split szekció címéhez igazítva',
            'section_lead' => 'Szekció bevezető szöveg a cím alatt',
            'card_title' => 'Feature / lista / dyn kártya címek',
            'card_body' => 'Kártyák törzsszövege',
        ];
    }

    /**
     * @return array{
     *     section_title: string,
     *     section_lead: string,
     *     card_title: string,
     *     card_body: string
     * }
     */
    public static function defaults(?string $presetKey = null): array
    {
        $tokens = SiteStylePresets::tokenVariables($presetKey);
        $sectionTitle = trim((string) ($tokens['--type-section-title-size'] ?? ''));
        if ($sectionTitle === '') {
            $sectionTitle = trim((string) ($tokens['--heading-size'] ?? ''));
        }

        return [
            'section_title' => $sectionTitle !== '' ? $sectionTitle : 'clamp(1.75rem, 3vw, 2.4rem)',
            'section_lead' => trim((string) ($tokens['--type-section-lead-size'] ?? '')) ?: '1.05rem',
            'card_title' => trim((string) ($tokens['--type-card-title-size'] ?? '')) ?: '1.15rem',
            'card_body' => trim((string) ($tokens['--type-card-body-size'] ?? '')) ?: '0.95rem',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $typography
     * @return array{
     *     section_title: string,
     *     section_lead: string,
     *     card_title: string,
     *     card_body: string
     * }
     */
    public static function normalize(?array $typography, ?string $presetKey = null): array
    {
        $defaults = self::defaults($presetKey);
        $typography ??= [];
        $normalized = [];

        foreach (self::roleKeys() as $role) {
            $value = self::normalizeSize($typography[$role] ?? null);
            $normalized[$role] = $value ?? $defaults[$role];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $typography
     * @return array<string, string>
     */
    public static function cssVariables(?array $typography = null, ?string $presetKey = null): array
    {
        $normalized = self::normalize($typography, $presetKey);

        return [
            '--type-section-title-size' => $normalized['section_title'],
            '--type-section-lead-size' => $normalized['section_lead'],
            '--type-card-title-size' => $normalized['card_title'],
            '--type-card-body-size' => $normalized['card_body'],
            // Régi token: a sharedCss / blokkok --heading-size-t használnak
            '--heading-size' => $normalized['section_title'],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $typography
     * @return array<string, string>
     */
    public static function toFormState(?array $typography = null, ?string $presetKey = null): array
    {
        $normalized = self::normalize($typography, $presetKey);
        $state = [];

        foreach (self::roleKeys() as $role) {
            $state['type_'.$role] = $normalized[$role];
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array{
     *     section_title: string,
     *     section_lead: string,
     *     card_title: string,
     *     card_body: string
     * }
     */
    public static function fromFormState(array $state, ?string $presetKey = null): array
    {
        $raw = [];

        foreach (self::roleKeys() as $role) {
            $raw[$role] = $state['type_'.$role] ?? null;
        }

        return self::normalize($raw, $presetKey);
    }

    public static function normalizeSize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || strlen($value) > 80) {
            return null;
        }

        // Egyszerű XSS / CSS injection védelem: csak tipikus méret szintaxis
        if (! preg_match('/^[a-zA-Z0-9.%(),\s+\-\/]+$/', $value)) {
            return null;
        }

        return $value;
    }
}
