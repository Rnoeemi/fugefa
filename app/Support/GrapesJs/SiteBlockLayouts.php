<?php

namespace App\Support\GrapesJs;

/**
 * Blokk elrendezés-választók (hero-szerű layout picker).
 * Ha a blokk JSON-ban már van layout param, azt nem írjuk felül.
 */
final class SiteBlockLayouts
{
    /**
     * @return array<string, array{default: string, options: list<array{id: string, name: string, hint?: string, wire?: string}>}>
     */
    public static function definitions(): array
    {
        return [
            'ts-features' => [
                'default' => 'cards',
                'options' => [
                    ['id' => 'cards', 'name' => 'Kártyarács', 'hint' => '3 oszlopos előnykártyák', 'wire' => 'cards-3'],
                    ['id' => 'list', 'name' => 'Lista', 'hint' => 'Cím + szöveg soronként', 'wire' => 'list-rows'],
                    ['id' => 'center', 'name' => 'Középre zárt kártyák', 'hint' => 'Középre igazított cím és kártyák', 'wire' => 'cards-center'],
                ],
            ],
            'ts-icon-list' => [
                'default' => 'grid',
                'options' => [
                    ['id' => 'grid', 'name' => 'Ikonrács', 'hint' => 'Ikon + szöveg oszlopokban', 'wire' => 'cards-3'],
                    ['id' => 'list', 'name' => 'Ikonlista', 'hint' => 'Ikon balra, szöveg jobbra', 'wire' => 'list-rows'],
                    ['id' => 'center', 'name' => 'Középre zárt', 'hint' => 'Középre igazított elemek', 'wire' => 'cards-center'],
                ],
            ],
            'ts-faq' => [
                'default' => 'stack',
                'options' => [
                    ['id' => 'stack', 'name' => 'Accordion lista', 'hint' => 'Kérdések egymás alatt', 'wire' => 'faq-stack'],
                    ['id' => 'two-col', 'name' => 'Két hasáb FAQ', 'hint' => 'Accordion két oszlopban', 'wire' => 'faq-two-col'],
                    ['id' => 'split', 'name' => 'Cím + lista', 'hint' => 'Bevezető balra, kérdések jobbra', 'wire' => 'faq-split'],
                ],
            ],
            'ts-quote' => [
                'default' => 'marquee',
                'options' => [
                    ['id' => 'marquee', 'name' => 'Gördülő sáv', 'hint' => 'Idézetek folyamatosan mozognak', 'wire' => 'quote-marquee'],
                    ['id' => 'single', 'name' => 'Egy nagy idézet', 'hint' => 'Egy vélemény középen', 'wire' => 'quote-single'],
                    ['id' => 'grid', 'name' => 'Idézet-rács', 'hint' => 'Több idézet egymás mellett', 'wire' => 'quote-grid'],
                ],
            ],
            'ts-text' => [
                'default' => 'left',
                'options' => [
                    ['id' => 'left', 'name' => 'Keskeny, balra', 'hint' => 'Olvasási szélesség, balra zárt', 'wire' => 'text-left'],
                    ['id' => 'center', 'name' => 'Keskeny, középen', 'hint' => 'Középre igazított szöveg', 'wire' => 'text-center'],
                    ['id' => 'wide', 'name' => 'Széles szöveg', 'hint' => 'Teljes tartalmi sáv', 'wire' => 'text-wide'],
                ],
            ],
            'ts-contact' => [
                'default' => 'stack',
                'options' => [
                    ['id' => 'stack', 'name' => 'Adatsorok', 'hint' => 'Cím, telefon, email egymás alatt', 'wire' => 'contact-stack'],
                    ['id' => 'cards', 'name' => 'Adatkártyák', 'hint' => 'Minden adat saját dobozban', 'wire' => 'contact-cards'],
                    ['id' => 'inline', 'name' => 'Egy sorban', 'hint' => 'Elérhetőségek vízszintesen', 'wire' => 'contact-inline'],
                ],
            ],
            'ts-gallery' => [
                'default' => 'grid',
                'options' => [
                    ['id' => 'grid', 'name' => 'Rács', 'hint' => 'Egyforma képarányú rács, object-fit borítás', 'wire' => 'gallery-grid'],
                    ['id' => 'masonry', 'name' => 'Masonry', 'hint' => 'Oszlopokban, természetes képarányokkal', 'wire' => 'gallery-masonry'],
                    ['id' => 'flex', 'name' => 'Sor (flex)', 'hint' => 'Masonry jelleg: sorokba tördelődő képek, természetes arányokkal', 'wire' => 'gallery-flex'],
                    ['id' => 'slide', 'name' => 'Slide', 'hint' => 'Egysoros váltó, egyforma képarány', 'wire' => 'gallery-slide'],
                ],
            ],
            'ts-stats' => [
                'default' => 'center',
                'options' => [
                    ['id' => 'center', 'name' => 'Számok középen', 'hint' => 'Cím + 3 stat középre', 'wire' => 'stats-center'],
                    ['id' => 'left', 'name' => 'Számok balra', 'hint' => 'Balra zárt statisztika', 'wire' => 'stats-left'],
                    ['id' => 'row', 'name' => 'Cím + számok', 'hint' => 'Cím balra, számok jobbra', 'wire' => 'stats-row'],
                ],
            ],
            'ts-buttons' => [
                'default' => 'center',
                'options' => [
                    ['id' => 'center', 'name' => 'Gombok középen', 'hint' => 'Cím és gombok középre', 'wire' => 'buttons-center'],
                    ['id' => 'left', 'name' => 'Gombok balra', 'hint' => 'Balra zárt gombok', 'wire' => 'buttons-left'],
                    ['id' => 'stack', 'name' => 'Gombok egymás alatt', 'hint' => 'Függőleges gomboszlop', 'wire' => 'buttons-stack'],
                ],
            ],
            'ts-video' => [
                'default' => 'boxed',
                'options' => [
                    ['id' => 'boxed', 'name' => 'Videó középen', 'hint' => 'Cím + középre zárt lejátszó', 'wire' => 'video-boxed'],
                    ['id' => 'full', 'name' => 'Széles videó', 'hint' => 'Teljes tartalmi szélesség', 'wire' => 'video-full'],
                    ['id' => 'split', 'name' => 'Szöveg + videó', 'hint' => 'Szöveg balra, lejátszó jobbra', 'wire' => 'video-split'],
                ],
            ],
            'ts-map' => [
                'default' => 'text-left',
                'options' => [
                    ['id' => 'text-left', 'name' => 'Szöveg + térkép', 'hint' => 'Cím balra, térkép jobbra', 'wire' => 'map-side'],
                    ['id' => 'map-top', 'name' => 'Térkép felül', 'hint' => 'Térkép, alatta szöveg', 'wire' => 'map-top'],
                    ['id' => 'map-only', 'name' => 'Csak térkép', 'hint' => 'Teljes szélességű térkép', 'wire' => 'map-only'],
                ],
            ],
            'ts-amenities' => [
                'default' => 'chips',
                'options' => [
                    ['id' => 'chips', 'name' => 'Címkesor', 'hint' => 'Középre zárt címkék', 'wire' => 'chips-row'],
                    ['id' => 'grid', 'name' => 'Címke-rács', 'hint' => 'Egyenletes dobozrács', 'wire' => 'amenities-grid'],
                    ['id' => 'list', 'name' => 'Címkelista', 'hint' => 'Egymás alatti elemek', 'wire' => 'amenities-list'],
                ],
            ],
            'ts-nearby' => [
                'default' => 'grid-2',
                'options' => [
                    ['id' => 'grid-2', 'name' => '2×2 lista', 'hint' => 'Négy hely két oszlopban', 'wire' => 'nearby-2'],
                    ['id' => 'grid-4', 'name' => '4 oszlop', 'hint' => 'Egy sorban négy tétel', 'wire' => 'nearby-4'],
                    ['id' => 'list', 'name' => 'Függőleges lista', 'hint' => 'Egymás alatti helyek', 'wire' => 'nearby-list'],
                ],
            ],
            'ts-checkin' => [
                'default' => 'grid-3',
                'options' => [
                    ['id' => 'grid-3', 'name' => '3 infókártya', 'hint' => 'Érkezés / távozás / egyéb', 'wire' => 'checkin-3'],
                    ['id' => 'stack', 'name' => 'Kártyák egymás alatt', 'hint' => 'Függőleges infósáv', 'wire' => 'checkin-stack'],
                    ['id' => 'row', 'name' => 'Kompakt kártyák', 'hint' => 'Sűrűbb háromoszlopos', 'wire' => 'checkin-row'],
                ],
            ],
            'ts-how-to-book' => [
                'default' => 'grid-3',
                'options' => [
                    ['id' => 'grid-3', 'name' => '3 lépéskártya', 'hint' => 'Lépések egymás mellett', 'wire' => 'steps-3'],
                    ['id' => 'timeline', 'name' => 'Idővonal', 'hint' => 'Számozott függőleges folyamat', 'wire' => 'steps-timeline'],
                    ['id' => 'stack', 'name' => 'Lépések egymás alatt', 'hint' => 'Halmozott lépéskártyák', 'wire' => 'steps-stack'],
                ],
            ],
            'ts-logo-row' => [
                'default' => 'row',
                'options' => [
                    ['id' => 'row', 'name' => 'Logósor', 'hint' => '4 logó egy sorban', 'wire' => 'logos-row'],
                    ['id' => 'grid-2', 'name' => '2×2 logók', 'hint' => 'Két sorban két-két logó', 'wire' => 'logos-2x2'],
                    ['id' => 'left', 'name' => 'Logók balra', 'hint' => 'Balra zárt logósor', 'wire' => 'logos-left'],
                ],
            ],
            'ts-pricing-table' => [
                'default' => 'table',
                'options' => [
                    ['id' => 'table', 'name' => 'Ártáblázat', 'hint' => 'Sorok és oszlopok', 'wire' => 'pricing-table'],
                    ['id' => 'cards', 'name' => 'Árkártyák', 'hint' => 'Minden időszak saját kártya', 'wire' => 'pricing-cards'],
                    ['id' => 'compact', 'name' => 'Kompakt tábla', 'hint' => 'Keskenyebb, sűrűbb tábla', 'wire' => 'pricing-compact'],
                ],
            ],
            'ts-social' => [
                'default' => 'center',
                'options' => [
                    ['id' => 'center', 'name' => 'Közösségi középen', 'hint' => 'Cím + linkgombok középre', 'wire' => 'social-center'],
                    ['id' => 'left', 'name' => 'Közösségi balra', 'hint' => 'Balra zárt linkek', 'wire' => 'social-left'],
                    ['id' => 'row', 'name' => 'Szöveg + linkek', 'hint' => 'Szöveg balra, gombok jobbra', 'wire' => 'social-row'],
                ],
            ],
            'ts-legal' => [
                'default' => 'narrow',
                'options' => [
                    ['id' => 'narrow', 'name' => 'Keskeny jogi szöveg', 'hint' => 'Olvasási szélesség', 'wire' => 'legal-narrow'],
                    ['id' => 'wide', 'name' => 'Széles jogi szöveg', 'hint' => 'Teljes tartalmi sáv', 'wire' => 'legal-wide'],
                    ['id' => 'two-col', 'name' => 'Két hasáb', 'hint' => 'Szöveg két oszlopban', 'wire' => 'legal-two-col'],
                ],
            ],
            'ts-split' => [
                'default' => 'image-right',
                'options' => [
                    ['id' => 'image-right', 'name' => 'Szöveg | kép', 'hint' => 'Szöveg balra, kép jobbra', 'wire' => 'split-image-right'],
                    ['id' => 'image-left', 'name' => 'Kép | szöveg', 'hint' => 'Kép balra, szöveg jobbra', 'wire' => 'split-image-left'],
                    ['id' => 'stacked', 'name' => 'Kép felül', 'hint' => 'Kép, alatta szöveg', 'wire' => 'split-stacked'],
                ],
            ],
            'ts-footer-full' => [
                'default' => 'cols-2',
                'options' => [
                    ['id' => 'cols-2', 'name' => '2 oszlopos lábléc', 'hint' => 'Brand + linklista', 'wire' => 'footer-2'],
                    ['id' => 'cols-3', 'name' => '3 oszlopos lábléc', 'hint' => 'Szélesebb láblécrács', 'wire' => 'footer-3'],
                    ['id' => 'stacked', 'name' => 'Középre zárt lábléc', 'hint' => 'Egymás alatt, középen', 'wire' => 'footer-stacked'],
                ],
            ],
            'ts-footer-minimal' => [
                'default' => 'split',
                'options' => [
                    ['id' => 'split', 'name' => 'Copyright | linkek', 'hint' => 'Balra évszám, jobbra linkek', 'wire' => 'footermin-split'],
                    ['id' => 'center', 'name' => 'Minimal középen', 'hint' => 'Minden középre', 'wire' => 'footermin-center'],
                    ['id' => 'stack', 'name' => 'Minimal egymás alatt', 'hint' => 'Copyright, alatta linkek', 'wire' => 'footermin-stack'],
                ],
            ],
            'ts-header-bar' => [
                'default' => 'standard',
                'options' => [
                    ['id' => 'standard', 'name' => 'Klasszikus', 'hint' => 'Logó balra, menü jobbra', 'wire' => 'header-standard'],
                    ['id' => 'brand-center', 'name' => 'Logó középen', 'hint' => 'Márka középen, menü oldalt', 'wire' => 'header-brand-center'],
                    ['id' => 'minimal', 'name' => 'Minimál', 'hint' => 'Asztali menü rejtve', 'wire' => 'header-minimal'],
                ],
            ],
            'ts-header-simple' => [
                'default' => 'standard',
                'options' => [
                    ['id' => 'standard', 'name' => 'Logó + menü', 'hint' => 'Régi blokk – kompatibilitás', 'wire' => 'header-standard'],
                    ['id' => 'minimal', 'name' => 'Csak logó + menügomb', 'hint' => 'Minimál fejléc', 'wire' => 'header-minimal'],
                ],
            ],
            'ts-layout-1col' => [
                'default' => 'boxed',
                'options' => [
                    ['id' => 'boxed', 'name' => 'Dobozolt oszlop', 'hint' => 'Tartalmi max-szélesség', 'wire' => 'layout-boxed'],
                    ['id' => 'narrow', 'name' => 'Keskeny oszlop', 'hint' => 'Szűkebb tartalmi sáv', 'wire' => 'layout-narrow'],
                    ['id' => 'full', 'name' => 'Teljes szélesség', 'hint' => 'Szélétől szélig', 'wire' => 'layout-full'],
                ],
            ],
            'ts-layout-2col' => [
                'default' => 'equal',
                'options' => [
                    ['id' => 'equal', 'name' => '50 / 50', 'hint' => 'Két egyenlő oszlop', 'wire' => 'layout-equal-2'],
                    ['id' => 'wide-left', 'name' => 'Szélesebb bal', 'hint' => 'Kb. 2/3 + 1/3', 'wire' => 'layout-wide-left'],
                    ['id' => 'wide-right', 'name' => 'Szélesebb jobb', 'hint' => 'Kb. 1/3 + 2/3', 'wire' => 'layout-wide-right'],
                ],
            ],
            'ts-layout-3col' => [
                'default' => 'equal',
                'options' => [
                    ['id' => 'equal', 'name' => '3 egyenlő oszlop', 'hint' => 'Egyforma hasábok', 'wire' => 'layout-equal-3'],
                    ['id' => 'featured-center', 'name' => 'Közép kiemelt', 'hint' => 'Szélesebb középső oszlop', 'wire' => 'layout-featured-center'],
                    ['id' => 'narrow-sides', 'name' => 'Keskeny szélek', 'hint' => 'Közép hangsúlyos', 'wire' => 'layout-featured-center'],
                ],
            ],
            'ts-contact-form' => [
                'default' => 'split',
                'options' => [
                    ['id' => 'split', 'name' => 'Info + űrlap', 'hint' => 'Elérhetőségek balra, mezők jobbra', 'wire' => 'form-split'],
                    ['id' => 'stack', 'name' => 'Info felett, űrlap alatt', 'hint' => 'Egymás alatt', 'wire' => 'form-stack'],
                    ['id' => 'form-only', 'name' => 'Csak űrlap', 'hint' => 'Középre zárt mezők, gombbal', 'wire' => 'form-only'],
                ],
            ],
            'ts-booking-cta' => [
                'default' => 'row',
                'options' => [
                    ['id' => 'row', 'name' => 'Felirat + gomb sorban', 'hint' => 'Szöveg balra, CTA jobbra', 'wire' => 'booking-row'],
                    ['id' => 'center', 'name' => 'CTA középen', 'hint' => 'Szöveg és gomb középre', 'wire' => 'booking-center'],
                    ['id' => 'stack', 'name' => 'CTA egymás alatt', 'hint' => 'Szöveg, alatta gomb', 'wire' => 'booking-stack'],
                ],
            ],
            'ts-availability-search' => [
                'default' => 'panel',
                'options' => [
                    ['id' => 'panel', 'name' => 'Keresősor', 'hint' => 'Dátum / vendég mezők egy sorban', 'wire' => 'search-panel'],
                    ['id' => 'stack', 'name' => 'Keresőmezők egymás alatt', 'hint' => 'Függőleges űrlap', 'wire' => 'search-stack'],
                    ['id' => 'compact', 'name' => 'Kompakt kereső', 'hint' => 'Sűrűbb mezősor', 'wire' => 'search-compact'],
                ],
            ],
            'ts-appointment-booking' => [
                'default' => 'grid',
                'options' => [
                    ['id' => 'grid', 'name' => 'Időpontkártyák', 'hint' => 'Szolgáltatók rácsban', 'wire' => 'appt-grid'],
                    ['id' => 'list', 'name' => 'Időpontlista', 'hint' => 'Egymás alatti sorok', 'wire' => 'appt-list'],
                    ['id' => 'compact', 'name' => 'Kompakt kártyák', 'hint' => 'Sűrűbb rács', 'wire' => 'appt-compact'],
                ],
            ],
            'ts-accommodations-cards' => [
                'default' => 'grid',
                'options' => [
                    ['id' => 'grid', 'name' => 'Szálláskártyák', 'hint' => 'Kép + szöveg rács', 'wire' => 'accom-grid'],
                    ['id' => 'grid-2', 'name' => '2 széles kártya', 'hint' => 'Nagyobb kártyák két oszlopban', 'wire' => 'accom-grid-2'],
                    ['id' => 'list', 'name' => 'Szálláslista', 'hint' => 'Egymás alatti kártyák', 'wire' => 'accom-list'],
                ],
            ],
            'ts-accommodations-list' => [
                'default' => 'rows',
                'options' => [
                    ['id' => 'rows', 'name' => 'Soros lista', 'hint' => 'Név + ár + gomb soronként', 'wire' => 'accom-rows'],
                    ['id' => 'compact', 'name' => 'Kompakt lista', 'hint' => 'Sűrűbb sorok', 'wire' => 'accom-compact'],
                    ['id' => 'cards', 'name' => 'Keretes lista', 'hint' => 'Sorok kártyaként', 'wire' => 'accom-cards'],
                ],
            ],
            'ts-accommodation-featured' => [
                'default' => 'image-left',
                'options' => [
                    ['id' => 'image-left', 'name' => 'Kép | leírás', 'hint' => 'Kiemelt szállás, kép balra', 'wire' => 'featured-image-left'],
                    ['id' => 'image-right', 'name' => 'Leírás | kép', 'hint' => 'Kiemelt szállás, kép jobbra', 'wire' => 'featured-image-right'],
                    ['id' => 'stacked', 'name' => 'Kép felül', 'hint' => 'Kép, alatta szöveg', 'wire' => 'featured-stacked'],
                ],
            ],
            'ts-accommodation-details' => [
                'default' => 'aside-right',
                'options' => [
                    ['id' => 'aside-right', 'name' => 'Tartalom + oldalsáv', 'hint' => 'Leírás balra, infó jobbra', 'wire' => 'details-aside-right'],
                    ['id' => 'aside-left', 'name' => 'Oldalsáv + tartalom', 'hint' => 'Infó balra, leírás jobbra', 'wire' => 'details-aside-left'],
                    ['id' => 'stack', 'name' => 'Részletek egymás alatt', 'hint' => 'Tartalom, alatta infó', 'wire' => 'details-stack'],
                ],
            ],
            'ts-accommodation-gallery' => [
                'default' => 'grid-4',
                'options' => [
                    ['id' => 'grid-4', 'name' => '4 képes rács', 'hint' => 'Egyenletes galéria', 'wire' => 'gallery-grid'],
                    ['id' => 'grid-3', 'name' => '3 képes rács', 'hint' => 'Nagyobb galériaképek', 'wire' => 'dyn-gallery-3'],
                    ['id' => 'featured', 'name' => 'Kiemelt galéria', 'hint' => 'Első kép nagyobb', 'wire' => 'gallery-featured'],
                ],
            ],
            'ts-site-map' => [
                'default' => 'text-left',
                'options' => [
                    ['id' => 'text-left', 'name' => 'Szöveg + térkép', 'hint' => 'Cím balra, térkép jobbra', 'wire' => 'map-side'],
                    ['id' => 'map-top', 'name' => 'Térkép felül', 'hint' => 'Térkép, alatta szöveg', 'wire' => 'map-top'],
                    ['id' => 'map-only', 'name' => 'Csak térkép', 'hint' => 'Teljes szélességű térkép', 'wire' => 'map-only'],
                ],
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    public static function appendLayoutParam(array $params, string $blockId): array
    {
        $def = self::definitions()[$blockId] ?? null;
        if ($def === null) {
            return $params;
        }

        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'layout' || ($param['type'] ?? '') === 'layout') {
                return $params;
            }
        }

        array_unshift($params, [
            'key' => 'layout',
            'attr' => 'data-layout',
            'label' => 'Elrendezés',
            'type' => 'layout',
            'default' => $def['default'],
            'group' => 'layout',
            'groupLabel' => 'Elrendezés',
            'options' => $def['options'],
        ]);

        return $params;
    }

    public static function css(): string
    {
        return <<<'CSS'
/* —— Blokk elrendezés-variánsok (data-layout) —— */

/* Features */
.ts-features[data-layout="center"]{text-align:center}
.ts-features[data-layout="center"] .ts-features__title{margin-left:auto;margin-right:auto}
.ts-features[data-layout="center"] .ts-features__grid{justify-items:center}
.ts-features[data-layout="center"] .ts-feature{text-align:center;width:100%;align-items:center}
.ts-features[data-layout="list"] .ts-features__grid{grid-template-columns:1fr!important;gap:1rem}
.ts-features[data-layout="list"] .ts-feature{display:grid;grid-template-columns:auto minmax(0,1fr);gap:.85rem 1rem;align-items:start}
.ts-features[data-layout="list"] .ts-feature__icon{grid-row:1 / span 2}
.ts-features[data-layout="list"] .ts-feature__title{grid-column:2}
.ts-features[data-layout="list"] .ts-feature__text{grid-column:2}
@media (max-width:700px){.ts-features[data-layout="list"] .ts-feature{grid-template-columns:auto minmax(0,1fr)}}

/* Icon list */
.ts-icon-list[data-layout="center"]{text-align:center}
.ts-icon-list[data-layout="center"] .ts-icon-list__title,
.ts-icon-list[data-layout="center"] .ts-icon-list__lead{margin-left:auto;margin-right:auto}
.ts-icon-list[data-layout="center"] .ts-icon-list__grid{justify-items:center}
.ts-icon-list[data-layout="center"] .ts-icon-item{text-align:center;align-items:center;width:100%}
.ts-icon-list[data-layout="list"] .ts-icon-list__grid{grid-template-columns:1fr!important;gap:1rem}
.ts-icon-list[data-layout="list"] .ts-icon-item{display:grid;grid-template-columns:auto minmax(0,1fr);gap:.85rem 1rem;align-items:start}
.ts-icon-list[data-layout="list"] .ts-icon-item__icon{grid-row:1 / span 2}
.ts-icon-list[data-layout="list"] .ts-icon-item__title{grid-column:2}
.ts-icon-list[data-layout="list"] .ts-icon-item__text{grid-column:2}

/* FAQ */
.ts-faq[data-layout="two-col"] .ts-faq__inner{max-width:var(--content-max,72rem)}
.ts-faq[data-layout="two-col"] .ts-faq__list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem 1.25rem;align-items:start}
.ts-faq[data-layout="split"] .ts-faq__inner{max-width:var(--content-max,72rem);display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:2rem;align-items:start}
.ts-faq[data-layout="split"] h2,.ts-faq[data-layout="split"] .ts-faq__lead{grid-column:1}
.ts-faq[data-layout="split"] .ts-faq__list{grid-column:2;grid-row:1 / span 3;margin:0}
@media (max-width:860px){
  .ts-faq[data-layout="two-col"] .ts-faq__list,.ts-faq[data-layout="split"] .ts-faq__inner{grid-template-columns:1fr}
}

/* Quote */
.ts-quote[data-layout="single"] .ts-quote__viewport{overflow:visible}
.ts-quote[data-layout="single"] .ts-quote__track{animation:none;width:auto;display:block;max-width:40rem;margin:0 auto}
.ts-quote[data-layout="single"] .ts-quote__card{flex:none;width:auto;padding:0}
.ts-quote[data-layout="single"] .ts-quote__card:nth-child(n+2){display:none}
.ts-quote[data-layout="grid"] .ts-quote__viewport{overflow:visible}
.ts-quote[data-layout="grid"] .ts-quote__track{animation:none;width:auto;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.5rem}
.ts-quote[data-layout="grid"] .ts-quote__card{flex:none;width:auto;padding:0;text-align:left}
.ts-quote[data-layout="grid"] .ts-quote__card:nth-child(n+4){display:none}
@media (max-width:900px){.ts-quote[data-layout="grid"] .ts-quote__track{grid-template-columns:1fr}}

/* Text */
.ts-text[data-layout="left"],.ts-text:not([data-layout]){text-align:left}
.ts-text[data-layout="left"] .ts-text__inner,.ts-text:not([data-layout]) .ts-text__inner{max-width:42rem;margin-left:auto;margin-right:auto}
.ts-text[data-layout="center"]{text-align:center}
.ts-text[data-layout="center"] .ts-text__inner{max-width:42rem;margin-left:auto;margin-right:auto}
.ts-text[data-layout="wide"] .ts-text__inner{max-width:var(--content-max,72rem);margin-left:auto;margin-right:auto;text-align:left}

/* Contact */
.ts-contact[data-layout="cards"] .ts-contact__row{padding:1rem 1.15rem;border:1px solid color-mix(in srgb,var(--color-text) 10%,transparent);background:var(--color-light);margin-bottom:.75rem}
.ts-contact[data-layout="inline"] .ts-contact__inner{max-width:var(--content-max,72rem);display:flex;flex-wrap:wrap;gap:1rem 2rem;align-items:baseline}
.ts-contact[data-layout="inline"] .ts-contact__title{flex:0 0 100%;margin-bottom:.25rem}
.ts-contact[data-layout="inline"] .ts-contact__row{margin:0}

/* Gallery – elrendezések */
.ts-gallery[data-layout="grid"] .ts-gallery__grid,.ts-gallery:not([data-layout]) .ts-gallery__grid,.ts-gallery[data-layout="featured"] .ts-gallery__grid{display:grid;gap:.75rem;grid-template-columns:repeat(4,minmax(0,1fr))}
.ts-gallery[data-layout="grid"] .ts-gallery__item,.ts-gallery:not([data-layout]) .ts-gallery__item,.ts-gallery[data-layout="featured"] .ts-gallery__item{aspect-ratio:4/3}
.ts-gallery[data-layout="masonry"] .ts-gallery__grid,.ts-gallery[data-layout="mosaic"] .ts-gallery__grid{display:block;column-count:3;column-gap:.75rem}
.ts-gallery[data-layout="masonry"] .ts-gallery__item,.ts-gallery[data-layout="mosaic"] .ts-gallery__item{display:inline-block;width:100%;margin:0 0 .75rem;break-inside:avoid;aspect-ratio:auto;min-height:0}
.ts-gallery[data-layout="masonry"] .ts-gallery__item img,.ts-gallery[data-layout="mosaic"] .ts-gallery__item img{height:auto;object-fit:unset}
.ts-gallery[data-layout="flex"] .ts-gallery__grid{display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-start;align-content:flex-start}
.ts-gallery[data-layout="flex"] .ts-gallery__item{flex:0 1 auto;width:auto;max-width:100%;aspect-ratio:auto;min-height:0;overflow:hidden}
.ts-gallery[data-layout="flex"] .ts-gallery__trigger{width:auto;height:auto}
.ts-gallery[data-layout="flex"] .ts-gallery__item img{width:auto;height:auto;max-width:min(100%,20rem);max-height:clamp(8.5rem,20vw,15rem);object-fit:contain;display:block}
.ts-gallery[data-layout="slide"]{--ts-gallery-slide-gap:.75rem;--ts-gallery-slide-cols:3}
.ts-gallery[data-layout="slide"] .ts-gallery__stage{position:relative;padding-inline:1.5rem}
.ts-gallery[data-layout="slide"] .ts-gallery__grid{display:flex;flex-wrap:nowrap;gap:var(--ts-gallery-slide-gap);overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.ts-gallery[data-layout="slide"] .ts-gallery__grid::-webkit-scrollbar{display:none}
.ts-gallery[data-layout="slide"] .ts-gallery__item{flex:0 0 calc((100% - (var(--ts-gallery-slide-cols) - 1) * var(--ts-gallery-slide-gap)) / var(--ts-gallery-slide-cols));scroll-snap-align:start;aspect-ratio:4/3;min-height:0;min-width:0}
.ts-gallery[data-layout="slide"] .ts-gallery__nav{display:inline-flex;background:none;box-shadow:none;border:0;border-radius:0;width:auto;height:auto;padding:.15rem;color:inherit;opacity:.5;font-size:clamp(1.65rem,2.8vw,2.1rem);line-height:1;transition:opacity .15s ease}
.ts-gallery[data-layout="slide"] .ts-gallery__nav:hover,.ts-gallery[data-layout="slide"] .ts-gallery__nav:focus-visible{opacity:1;background:none}
.ts-gallery[data-layout="slide"] .ts-gallery__nav--prev{left:0}
.ts-gallery[data-layout="slide"] .ts-gallery__nav--next{right:0}
.ts-gallery-lightbox{position:fixed;inset:0;z-index:10050;display:flex;align-items:center;justify-content:center;padding:max(1rem,3vw);background:rgba(0,0,0,.88);box-sizing:border-box}
.ts-gallery-lightbox[hidden]{display:none!important}
body.ts-gallery-lightbox-open{overflow:hidden}
.ts-gallery-lightbox__figure{margin:0;max-width:min(96vw,1200px);max-height:92vh;display:flex;flex-direction:column;align-items:center;gap:.65rem}
.ts-gallery-lightbox__img{max-width:100%;max-height:calc(92vh - 2.5rem);width:auto;height:auto;object-fit:contain;display:block}
.ts-gallery-lightbox__caption{margin:0;color:#fff;font-size:.95rem;line-height:1.4;text-align:center;opacity:.9;max-width:60ch}
.ts-gallery-lightbox__close,.ts-gallery-lightbox__prev,.ts-gallery-lightbox__next{position:absolute;border:0;background:rgba(255,255,255,.14);color:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.ts-gallery-lightbox__close{top:1rem;right:1rem;width:2.75rem;height:2.75rem;border-radius:999px;font-size:1.6rem;line-height:1}
.ts-gallery-lightbox__prev,.ts-gallery-lightbox__next{top:50%;transform:translateY(-50%);width:2.75rem;height:2.75rem;border-radius:999px;font-size:1.75rem;line-height:1}
.ts-gallery-lightbox__prev{left:max(.75rem,2vw)}
.ts-gallery-lightbox__next{right:max(.75rem,2vw)}
@media (max-width:900px){
  .ts-gallery[data-layout="grid"] .ts-gallery__grid,.ts-gallery:not([data-layout]) .ts-gallery__grid,.ts-gallery[data-layout="featured"] .ts-gallery__grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .ts-gallery[data-layout="masonry"] .ts-gallery__grid,.ts-gallery[data-layout="mosaic"] .ts-gallery__grid{column-count:2}
  .ts-gallery[data-layout="slide"]{--ts-gallery-slide-cols:2}
}
@media (max-width:520px){
  .ts-gallery[data-layout="grid"] .ts-gallery__grid,.ts-gallery:not([data-layout]) .ts-gallery__grid,.ts-gallery[data-layout="featured"] .ts-gallery__grid{grid-template-columns:1fr}
  .ts-gallery[data-layout="masonry"] .ts-gallery__grid,.ts-gallery[data-layout="mosaic"] .ts-gallery__grid{column-count:1}
  .ts-gallery[data-layout="slide"]{--ts-gallery-slide-cols:1}
}

/* Stats */
.ts-stats[data-layout="left"] .ts-stats__inner{text-align:left}
.ts-stats[data-layout="row"] .ts-stats__inner{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:2rem;align-items:center;text-align:left}
.ts-stats[data-layout="row"] h2{margin:0}
@media (max-width:800px){.ts-stats[data-layout="row"] .ts-stats__inner{grid-template-columns:1fr;text-align:center}}

/* Buttons */
.ts-buttons[data-layout="left"]{text-align:left}
.ts-buttons[data-layout="left"] .ts-buttons__inner{margin-left:0;margin-right:auto}
.ts-buttons[data-layout="left"] .ts-buttons__row{justify-content:flex-start}
.ts-buttons[data-layout="stack"] .ts-buttons__row{flex-direction:column;align-items:center}
.ts-buttons[data-layout="stack"] .ts-buttons__row .ts-btn{width:min(100%,18rem);text-align:center}
.ts-buttons[data-layout="left"][data-layout="stack"] .ts-buttons__row{align-items:flex-start}

/* Video */
.ts-video[data-layout="full"] .ts-video__inner{max-width:var(--content-max,72rem)}
.ts-video[data-layout="split"] .ts-video__inner{max-width:var(--content-max,72rem);display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:2rem;align-items:center}
.ts-video[data-layout="split"] .ts-video__frame{margin:0}
.ts-video[data-layout="split"] h2,.ts-video[data-layout="split"] .ts-video__lead{grid-column:1}
.ts-video[data-layout="split"] .ts-video__frame{grid-column:2;grid-row:1 / span 3}
@media (max-width:860px){.ts-video[data-layout="split"] .ts-video__inner{grid-template-columns:1fr}.ts-video[data-layout="split"] .ts-video__frame{grid-column:auto;grid-row:auto}}

/* Map + site-map */
.ts-map[data-layout="map-top"] .ts-map__inner,.ts-dyn-map[data-layout="map-top"] .ts-dyn-map__inner{grid-template-columns:1fr}
.ts-map[data-layout="map-top"] .ts-map__frame{order:-1}
.ts-map[data-layout="map-only"] .ts-map__copy,.ts-dyn-map[data-layout="map-only"] .ts-dyn-map__copy{display:none}
.ts-map[data-layout="map-only"] .ts-map__inner,.ts-dyn-map[data-layout="map-only"] .ts-dyn-map__inner{grid-template-columns:1fr}
.ts-map[data-layout="map-only"] .ts-map__frame{min-height:360px}

/* Amenities */
.ts-amenities[data-layout="grid"] .ts-amenities__row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;justify-items:stretch}
.ts-amenities[data-layout="grid"] .ts-amenities__row span{border:1px solid color-mix(in srgb,var(--color-text) 10%,transparent);padding:.85rem 1rem;border-bottom-width:1px}
.ts-amenities[data-layout="list"] .ts-amenities__inner{text-align:left}
.ts-amenities[data-layout="list"] .ts-amenities__row{flex-direction:column;align-items:stretch;gap:0}
.ts-amenities[data-layout="list"] .ts-amenities__row span{border-bottom:1px solid color-mix(in srgb,var(--color-text) 10%,transparent);padding:.85rem 0}
.ts-amenities__row > span:empty,
.ts-amenities__row > span.is-empty,
.ts-amenities__row > span[hidden]{display:none!important;border:0!important;padding:0!important;margin:0!important;min-height:0!important}
@media (max-width:700px){.ts-amenities[data-layout="grid"] .ts-amenities__row{grid-template-columns:1fr}}

/* Nearby */
.ts-nearby[data-layout="grid-4"] .ts-nearby__grid{grid-template-columns:repeat(4,minmax(0,1fr))}
.ts-nearby[data-layout="list"] .ts-nearby__grid{grid-template-columns:1fr}
@media (max-width:900px){.ts-nearby[data-layout="grid-4"] .ts-nearby__grid{grid-template-columns:repeat(2,1fr)}}

/* Check-in */
.ts-checkin[data-layout="stack"] .ts-checkin__grid{grid-template-columns:1fr}
.ts-checkin[data-layout="row"] .ts-checkin__grid{gap:.75rem}
.ts-checkin[data-layout="row"] article{padding:.9rem 1rem}

/* How to book / steps */
.ts-steps[data-layout="stack"] .ts-steps__grid{grid-template-columns:1fr}
.ts-steps[data-layout="timeline"] .ts-steps__grid{grid-template-columns:1fr;position:relative;gap:0}
.ts-steps[data-layout="timeline"] .ts-steps__grid li{border:0;border-left:2px solid color-mix(in srgb,var(--color-accent) 55%,transparent);border-radius:0;background:transparent;padding:0 0 1.75rem 1.5rem;position:relative}
.ts-steps[data-layout="timeline"] .ts-steps__grid li:last-child{padding-bottom:0}
.ts-steps[data-layout="timeline"] .ts-steps__grid span{position:absolute;left:-1.05rem;top:0}

/* Logo row */
.ts-logo-row[data-layout="grid-2"] .ts-logo-row__grid{grid-template-columns:repeat(2,minmax(0,1fr));max-width:28rem;margin-left:auto;margin-right:auto}
.ts-logo-row[data-layout="left"]{text-align:left}
.ts-logo-row[data-layout="left"] .ts-logo-row__grid{justify-items:start}

/* Pricing */
.ts-pricing[data-layout="cards"] .ts-pricing__table{border:0;display:grid;gap:1rem}
.ts-pricing[data-layout="cards"] .ts-pricing__head{display:none}
.ts-pricing[data-layout="cards"] .ts-pricing__row{grid-template-columns:1fr;border:1px solid color-mix(in srgb,var(--color-text) 10%,transparent);gap:.35rem}
.ts-pricing[data-layout="compact"] .ts-pricing__row{padding:.65rem .85rem;font-size:.92rem}
.ts-pricing[data-layout="compact"] .ts-pricing__inner{max-width:42rem}

/* Social */
.ts-social[data-layout="left"]{text-align:left}
.ts-social[data-layout="left"] .ts-social__inner{margin-left:0;margin-right:auto}
.ts-social[data-layout="left"] .ts-social__links{justify-content:flex-start}
.ts-social[data-layout="row"] .ts-social__inner{max-width:var(--content-max,72rem);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.25rem;text-align:left}
.ts-social[data-layout="row"] .ts-social__lead{margin:0}
.ts-social[data-layout="row"] .ts-social__links{justify-content:flex-end}

/* Legal */
.ts-legal[data-layout="wide"] .ts-legal__inner{max-width:var(--content-max,72rem)}
.ts-legal[data-layout="two-col"] .ts-legal__inner{max-width:var(--content-max,72rem)}
.ts-legal[data-layout="two-col"] .ts-legal__body{column-count:2;column-gap:2rem}
@media (max-width:800px){.ts-legal[data-layout="two-col"] .ts-legal__body{column-count:1}}

/* Split */
.ts-split[data-layout="image-right"] .ts-split__copy,.ts-split:not([data-layout]) .ts-split__copy{order:1}
.ts-split[data-layout="image-right"] .ts-split__media,.ts-split:not([data-layout]) .ts-split__media{order:2}
.ts-split[data-layout="image-left"] .ts-split__copy{order:2}
.ts-split[data-layout="image-left"] .ts-split__media{order:1}
.ts-split[data-layout="stacked"] .ts-split__inner{grid-template-columns:1fr}
.ts-split[data-layout="stacked"] .ts-split__media{order:-1;max-width:40rem}

/* Footer full */
.ts-footer[data-layout="cols-3"] .ts-footer__grid,.ts-footer[data-layout="cols-3"] .ts-footer__inner{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:2rem}
.ts-footer[data-layout="stacked"] .ts-footer__grid,.ts-footer[data-layout="stacked"] .ts-footer__inner{display:flex;flex-direction:column;align-items:center;text-align:center;gap:1.5rem}

/* Footer minimal */
.ts-footer-min[data-layout="center"]{justify-content:center;flex-direction:column;align-items:center;text-align:center}
.ts-footer-min[data-layout="center"] a{margin-left:.75rem;margin-right:.75rem}
.ts-footer-min[data-layout="stack"]{flex-direction:column;align-items:flex-start}
.ts-footer-min[data-layout="stack"] a{margin-left:0;margin-right:1rem}

/* Headers */
.ts-nav-bar{display:contents}
.site-nav[data-brand-align="center"] .ts-nav-inner{display:flex;flex-wrap:nowrap;justify-content:space-between}
.site-nav[data-brand-align="center"] .ts-nav-brand{flex:1;justify-content:center;min-width:0;grid-column:unset;justify-self:unset}
.site-nav[data-brand-align="center"] .ts-nav-links{grid-column:unset;justify-self:unset}
.site-nav[data-brand-align="center"] .ts-nav-actions{grid-column:unset;justify-self:unset}
.site-nav[data-brand-align="center"] .ts-nav-bar{display:flex;align-items:center;gap:.75rem;flex-shrink:0}
@media (min-width:768px){
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-inner,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-inner{flex-direction:column;align-items:center;gap:.65rem}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-brand,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-brand{flex:none;justify-content:center;width:100%}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-bar,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:1rem 1.5rem;width:100%}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-links,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-links{margin-left:0;justify-content:center}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-actions,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-actions{margin-left:0}
}
@media (min-width:1024px){
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-inner{flex-direction:column;align-items:center;gap:.65rem}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-brand{flex:none;justify-content:center;width:100%}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:1rem 1.5rem;width:100%}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-links{margin-left:0;justify-content:center}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-actions{margin-left:0}
}
@media (max-width:767px){
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-inner,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-inner{flex-direction:row;align-items:center}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-brand,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-brand{flex:1;justify-content:flex-start}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="phone"] .ts-nav-bar,
  .site-nav[data-brand-align="center"]:not([data-menu-breakpoint]) .ts-nav-bar{width:auto;margin-left:auto}
}
@media (max-width:1023px){
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-inner{flex-direction:row;align-items:center}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-brand{flex:1;justify-content:flex-start}
  .site-nav[data-brand-align="center"][data-menu-breakpoint="tablet"] .ts-nav-bar{width:auto;margin-left:auto}
}
.site-nav[data-desktop-menu="hidden"] .ts-nav-links,.site-nav[data-layout="minimal"] .ts-nav-links,.ts-header-simple[data-layout="minimal"] .ts-nav-links{display:none!important}
.site-nav[data-desktop-menu="hidden"] .ts-nav-toggle,.site-nav[data-layout="minimal"] .ts-nav-toggle{display:inline-flex!important}
.site-nav[data-menu-align="left"] .ts-nav-links{margin-left:0;order:2}
.site-nav[data-menu-align="left"] .ts-nav-brand{order:1}
.site-nav[data-menu-align="left"] .ts-nav-actions{order:3;margin-left:auto}
.site-nav[data-menu-align="right"] .ts-nav-links,
.site-nav:not([data-menu-align="left"]) .ts-nav-links{margin-left:auto}
.site-nav[data-menu-align="right"] .ts-nav-actions,
.site-nav:not([data-menu-align="left"]) .ts-nav-actions{margin-left:0}
.ts-nav-actions{display:flex;align-items:center;gap:.75rem;margin-left:auto;flex-shrink:0}

/* Layout containers */
.ts-layout[data-layout="narrow"] .ts-layout__inner{max-width:42rem;margin-left:auto;margin-right:auto}
.ts-layout[data-layout="full"] .ts-layout__inner{max-width:none;width:100%}
.ts-layout--2col[data-layout="wide-left"] .ts-layout__inner--2col{grid-template-columns:minmax(0,1.4fr) minmax(0,.8fr)!important}
.ts-layout--2col[data-layout="wide-right"] .ts-layout__inner--2col{grid-template-columns:minmax(0,.8fr) minmax(0,1.4fr)!important}
.ts-layout--3col[data-layout="featured-center"] .ts-layout__inner--3col,
.ts-layout--3col[data-layout="narrow-sides"] .ts-layout__inner--3col{grid-template-columns:minmax(0,.8fr) minmax(0,1.4fr) minmax(0,.8fr)!important}

/* Dynamic: booking CTA */
.ts-dyn-booking-cta[data-layout="center"] .ts-dyn-booking-cta__inner{flex-direction:column;align-items:center;text-align:center}
.ts-dyn-booking-cta[data-layout="stack"] .ts-dyn-booking-cta__inner{flex-direction:column;align-items:flex-start}

/* Dynamic: contact form */
.ts-dyn-contact-form[data-layout="stack"] .ts-dyn-contact-form__inner{grid-template-columns:1fr}
.ts-dyn-contact-form[data-layout="form-only"] .ts-dyn-contact-form__info{display:none}
.ts-dyn-contact-form[data-layout="form-only"] .ts-dyn-contact-form__inner{grid-template-columns:minmax(0,28rem);justify-content:center}

/* Dynamic: search */
.ts-dyn-search[data-layout="stack"] .ts-dyn-search__form{grid-template-columns:1fr}
.ts-dyn-search[data-layout="compact"] .ts-dyn-search__form{padding:.85rem;gap:.75rem}

/* Dynamic: appointment */
.ts-dyn-appointment[data-layout="list"] .ts-dyn-appointment__grid{grid-template-columns:1fr}
.ts-dyn-appointment[data-layout="compact"] .ts-dyn-appointment__grid{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}

/* Dynamic: cards / list */
.ts-dyn-cards[data-layout="grid-2"] .ts-dyn-cards__grid{grid-template-columns:repeat(2,minmax(0,1fr))}
.ts-dyn-cards[data-layout="list"] .ts-dyn-cards__grid{grid-template-columns:1fr}
.ts-dyn-list[data-layout="compact"] .ts-dyn-list__row{padding:.7rem .9rem}
.ts-dyn-list[data-layout="cards"] .ts-dyn-list__row{box-shadow:0 1px 0 color-mix(in srgb,var(--color-text) 6%,transparent)}

/* Dynamic: featured */
.ts-dyn-featured[data-layout="image-right"] .ts-dyn-featured__grid{grid-template-columns:minmax(0,1fr) minmax(0,1.1fr)}
.ts-dyn-featured[data-layout="image-right"] .ts-dyn-featured__media{order:2}
.ts-dyn-featured[data-layout="image-right"] .ts-dyn-featured__body{order:1}
.ts-dyn-featured[data-layout="stacked"] .ts-dyn-featured__grid{grid-template-columns:1fr}

/* Dynamic: details */
.ts-dyn-details[data-layout="aside-left"] .ts-dyn-details__grid{grid-template-columns:minmax(0,1fr) minmax(0,1.4fr)}
.ts-dyn-details[data-layout="aside-left"] .ts-dyn-details__grid > :first-child{order:2}
.ts-dyn-details[data-layout="stack"] .ts-dyn-details__grid{grid-template-columns:1fr}

/* Dynamic: gallery */
.ts-dyn-gallery[data-layout="grid-3"] .ts-dyn-gallery__grid{grid-template-columns:repeat(3,1fr)}
.ts-dyn-gallery[data-layout="featured"] .ts-dyn-gallery__grid{grid-template-columns:2fr 1fr 1fr}
.ts-dyn-gallery[data-layout="featured"] .ts-dyn-gallery__grid figure:first-child{grid-row:span 2;aspect-ratio:auto}

@media (max-width:768px){
  .ts-dyn-cards[data-layout="grid-2"] .ts-dyn-cards__grid,
  .ts-dyn-gallery[data-layout="grid-3"] .ts-dyn-gallery__grid,
  .ts-dyn-gallery[data-layout="featured"] .ts-dyn-gallery__grid,
  .ts-dyn-featured[data-layout="image-right"] .ts-dyn-featured__grid,
  .ts-dyn-details[data-layout="aside-left"] .ts-dyn-details__grid{grid-template-columns:1fr}
}
CSS;
    }
}
