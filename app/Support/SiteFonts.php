<?php

namespace App\Support;

class SiteFonts
{
    /**
     * Magyar diacritic-teszt (ő, ű is).
     */
    public const PANGRAM = 'Árvíztűrő tükörfúrógép';

    /**
     * Elérhető webfontok (Bunny Fonts / Google Fonts tükör).
     * Csak latin-ext / magyar ékezetes karaktereket jól kezelő családok.
     *
     * @return array<string, string> megjelenített név => bunny family slug + súlyok
     */
    public static function options(): array
    {
        $grouped = self::groupedOptions();
        $flat = [];

        foreach ($grouped as $fonts) {
            foreach ($fonts as $label => $slug) {
                $flat[$label] = $slug;
            }
        }

        return $flat;
    }

    /**
     * @return array<string, array<string, string>> csoport => [név => bunny slug]
     */
    public static function groupedOptions(): array
    {
        return [
            'Sans – modern / tiszta' => [
                'Inter' => 'inter:400,500,600,700',
                'DM Sans' => 'dm-sans:400,500,600,700',
                'Manrope' => 'manrope:400,500,600,700',
                'Outfit' => 'outfit:400,500,600,700',
                'Plus Jakarta Sans' => 'plus-jakarta-sans:400,500,600,700',
                'Figtree' => 'figtree:400,500,600,700',
                'Work Sans' => 'work-sans:400,500,600,700',
            ],
            'Sans – barátságos / szállásos' => [
                'Karla' => 'karla:400,500,600,700',
                'Nunito Sans' => 'nunito-sans:400,500,600,700',
                'Source Sans 3' => 'source-sans-3:400,500,600,700',
                'Lato' => 'lato:400,700',
                'Open Sans' => 'open-sans:400,500,600,700',
                'Mulish' => 'mulish:400,500,600,700',
                'Rubik' => 'rubik:400,500,600,700',
                'Quicksand' => 'quicksand:400,500,600,700',
            ],
            'Sans – karakteres / loft' => [
                'IBM Plex Sans' => 'ibm-plex-sans:400,500,600,700',
                'Barlow' => 'barlow:400,500,600,700',
                'Montserrat' => 'montserrat:400,500,600,700',
                'Poppins' => 'poppins:400,500,600,700',
                'Josefin Sans' => 'josefin-sans:400,500,600,700',
            ],
            'Serif – elegáns címsor' => [
                'Playfair Display' => 'playfair-display:500,600,700',
                'Cormorant Garamond' => 'cormorant-garamond:400,500,600,700',
                'Fraunces' => 'fraunces:400,500,600,700',
                'DM Serif Display' => 'dm-serif-display:400',
                'Libre Baskerville' => 'libre-baskerville:400,700',
            ],
            'Serif – olvasható / klasszikus' => [
                'Literata' => 'literata:500,600,700',
                'Source Serif 4' => 'source-serif-4:400,600,700',
                'Lora' => 'lora:400,500,600,700',
                'Merriweather' => 'merriweather:400,700',
                'IBM Plex Serif' => 'ibm-plex-serif:400,500,600,700',
                'Spectral' => 'spectral:400,500,600,700',
                'Crimson Pro' => 'crimson-pro:400,500,600,700',
                'Newsreader' => 'newsreader:400,500,600,700',
                'Bitter' => 'bitter:400,500,600,700',
                'EB Garamond' => 'eb-garamond:400,500,600,700',
            ],
        ];
    }

    public static function stylesheet(?string ...$families): ?string
    {
        $options = self::options();
        $parts = [];

        foreach ($families as $family) {
            if (blank($family) || ! isset($options[$family])) {
                continue;
            }

            $parts[] = $options[$family];
        }

        $parts = array_values(array_unique($parts));

        if ($parts === []) {
            return null;
        }

        return 'https://fonts.bunny.net/css?family='.implode('|', $parts).'&display=swap';
    }

    /**
     * Filament select: csoportosított, érték = családnév.
     *
     * @return array<string, array<string, string>|string>
     */
    public static function selectOptions(): array
    {
        $grouped = [];

        foreach (self::groupedOptions() as $group => $fonts) {
            $grouped[$group] = array_combine(array_keys($fonts), array_keys($fonts));
        }

        return $grouped;
    }

    /**
     * @return list<string>
     */
    public static function labels(): array
    {
        return array_keys(self::options());
    }

    public static function isSerif(string $family): bool
    {
        foreach (self::groupedOptions() as $group => $fonts) {
            if (str_starts_with($group, 'Serif') && isset($fonts[$family])) {
                return true;
            }
        }

        return false;
    }

    /**
     * CSS font-family érték a megadott családhoz.
     */
    public static function cssValue(string $family): string
    {
        $fallback = self::isSerif($family)
            ? 'ui-serif, Georgia, serif'
            : 'ui-sans-serif, system-ui, sans-serif';

        return "'{$family}', {$fallback}";
    }

    /**
     * GrapesJS Style Manager font-family opciók (téma változók + katalógus).
     *
     * @return list<array{id: string, label: string}>
     */
    public static function builderOptions(): array
    {
        $options = [
            [
                'id' => 'var(--font-sans)',
                'label' => 'Téma – törzsszöveg',
            ],
            [
                'id' => 'var(--font-display)',
                'label' => 'Téma – címsor',
            ],
        ];

        foreach (self::labels() as $family) {
            $options[] = [
                'id' => self::cssValue($family),
                'label' => $family,
            ];
        }

        return $options;
    }

    /**
     * Teljes katalógus stylesheet (szerkesztő canvas előnézethez).
     */
    public static function catalogStylesheet(): ?string
    {
        return self::stylesheet(...self::labels());
    }
}
