<?php

namespace App\Support\GrapesJs;

/**
 * Szekció tartalmi elemek megjelenés / elrejtés kapcsolói.
 * Tárolás: root data-show-*="1|0" + CSS (hiányzó attr = látható).
 */
final class SiteBlockVisibility
{
    /**
     * Param kulcs → show_* kapcsoló meta.
     *
     * @var array<string, array{key: string, label: string, group?: string, groupLabel?: string}>
     */
    private const SHOW_FOR = [
        'eyebrow' => [
            'key' => 'show_eyebrow',
            'label' => 'Alcím megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'title' => [
            'key' => 'show_title',
            'label' => 'Cím megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'lead' => [
            'key' => 'show_lead',
            'label' => 'Leírás megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'text' => [
            'key' => 'show_text',
            'label' => 'Szöveg megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'body' => [
            'key' => 'show_body',
            'label' => 'Szöveg megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'note' => [
            'key' => 'show_note',
            'label' => 'Megjegyzés megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'address' => [
            'key' => 'show_address',
            'label' => 'Cím megjelenítése',
            'group' => 'content',
            'groupLabel' => 'Szöveges tartalom',
        ],
        'phone' => [
            'key' => 'show_phone',
            'label' => 'Telefon megjelenítése',
            'group' => 'contact-details',
            'groupLabel' => 'Elérhetőségek',
        ],
        'email' => [
            'key' => 'show_email',
            'label' => 'Email megjelenítése',
            'group' => 'contact-details',
            'groupLabel' => 'Elérhetőségek',
        ],
        'primary_label' => [
            'key' => 'show_primary',
            'label' => 'Gomb megjelenítése',
            'group' => 'btn-primary',
            'groupLabel' => 'Elsődleges gomb',
        ],
        'secondary_label' => [
            'key' => 'show_secondary',
            'label' => 'Gomb megjelenítése',
            'group' => 'btn-secondary',
            'groupLabel' => 'Másodlagos gomb',
        ],
        'button_label' => [
            'key' => 'show_button',
            'label' => 'Gomb megjelenítése',
            'group' => 'btn-main',
            'groupLabel' => 'Gomb',
        ],
        'button' => [
            'key' => 'show_button',
            'label' => 'Gomb megjelenítése',
            'group' => 'btn-main',
            'groupLabel' => 'Gomb',
        ],
        'cta_label' => [
            'key' => 'show_cta',
            'label' => 'CTA gomb megjelenítése',
            'group' => 'btn-cta',
            'groupLabel' => 'CTA gomb',
            'default' => '0',
        ],
        'topbar_phone' => [
            'key' => 'show_topbar_phone',
            'label' => 'Telefon megjelenítése',
            'group' => 'topbar-contact',
            'groupLabel' => 'Felső sáv – elérhetőség',
        ],
        'topbar_email' => [
            'key' => 'show_topbar_email',
            'label' => 'E-mail megjelenítése',
            'group' => 'topbar-contact',
            'groupLabel' => 'Felső sáv – elérhetőség',
        ],
        'topbar_address' => [
            'key' => 'show_topbar_address',
            'label' => 'Cím megjelenítése',
            'group' => 'topbar-contact',
            'groupLabel' => 'Felső sáv – elérhetőség',
        ],
    ];

    /**
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    public static function appendShowParams(array $params): array
    {
        $existing = [];
        foreach ($params as $param) {
            $key = (string) ($param['key'] ?? '');
            if ($key !== '') {
                $existing[$key] = true;
            }
        }

        $result = [];
        foreach ($params as $param) {
            $result[] = $param;
            $key = (string) ($param['key'] ?? '');
            $meta = self::SHOW_FOR[$key] ?? null;
            if ($meta === null) {
                continue;
            }
            if (isset($existing[$meta['key']])) {
                continue;
            }

            $group = (string) ($param['group'] ?? $meta['group'] ?? 'content');
            $groupLabel = (string) ($param['groupLabel'] ?? $meta['groupLabel'] ?? 'Szöveges tartalom');

            // Gomb csoportok: a JSON groupLabel-je (pl. „1. gomb”) élvez elsőbbséget
            if (str_starts_with($meta['key'], 'show_primary') || str_starts_with($meta['key'], 'show_secondary') || $meta['key'] === 'show_button') {
                $group = (string) ($param['group'] ?? $meta['group']);
                $groupLabel = (string) ($param['groupLabel'] ?? $meta['groupLabel']);
            }

            $showParam = [
                'key' => $meta['key'],
                'attr' => 'data-'.str_replace('_', '-', $meta['key']),
                'label' => $meta['label'],
                'type' => 'checkbox',
                'default' => (string) ($meta['default'] ?? '1'),
                'group' => $group,
                'groupLabel' => $groupLabel,
            ];
            $result[] = $showParam;
            $existing[$meta['key']] = true;
        }

        return $result;
    }

    /**
     * Központi elrejtő szabályok (hiányzó data-show-* = látható).
     */
    public static function css(): string
    {
        return <<<'CSS'
/* —— Tartalmi elemek megjelenés / elrejtés (data-show-*="0") —— */
[data-show-eyebrow="0"] [data-ts-text="eyebrow"],
[data-show-title="0"] [data-ts-text="title"],
[data-show-lead="0"] [data-ts-text="lead"],
[data-show-text="0"] [data-ts-text="text"],
[data-show-body="0"] [data-ts-text="body"],
[data-show-note="0"] [data-ts-text="note"],
[data-show-address="0"] [data-ts-text="address"],
[data-show-primary="0"] [data-ts-btn="primary_style"],
[data-show-primary="0"] [data-ts-text="primary_label"],
[data-show-primary="0"] [data-ts-href="primary_href"],
[data-show-secondary="0"] [data-ts-btn="secondary_style"],
[data-show-secondary="0"] [data-ts-text="secondary_label"],
[data-show-secondary="0"] [data-ts-href="secondary_href"],
[data-show-button="0"] [data-ts-btn="button_style"],
[data-show-button="0"] [data-ts-text="button_label"],
[data-show-button="0"] [data-ts-text="button"],
[data-show-button="0"] [data-ts-href="button_href"],
[data-show-button="0"] .ts-dyn-search__actions,
[data-show-button="0"] .ts-dyn-booking-cta__inner > a,
[data-show-button="0"] .ts-dyn-appointment__cta,
[data-show-button="0"] .ts-dyn-contact-form__form button[type="submit"],
[data-show-phone="0"] [data-ts-show="phone"],
[data-show-email="0"] [data-ts-show="email"],
[data-show-address="0"] [data-ts-show="address"],
[data-show-address="0"] [data-ts-text="address"],
[data-show-cta="0"] [data-ts-text="cta_label"],
[data-show-cta="0"] [data-ts-href="cta_href"],
[data-show-topbar="0"] [data-ts-topbar],
[data-show-topbar-phone="0"] [data-ts-show="topbar_phone"],
[data-show-topbar-email="0"] [data-ts-show="topbar_email"],
[data-show-topbar-address="0"] [data-ts-show="topbar_address"] {
  display: none !important;
}

/* Térkép: szöveges oszlop részben / teljesen elrejtve → full frame */
.ts-map[data-show-title="0"] .ts-map__title,
.ts-map[data-show-address="0"] .ts-map__address,
.ts-map[data-show-note="0"] .ts-map__note {
  display: none !important;
}

.ts-map[data-show-title="0"][data-show-address="0"][data-show-note="0"] .ts-map__copy {
  display: none !important;
}

.ts-map[data-show-title="0"][data-show-address="0"][data-show-note="0"] .ts-map__inner {
  grid-template-columns: 1fr !important;
  max-width: none !important;
}

.ts-map[data-show-title="0"][data-show-address="0"][data-show-note="0"] .ts-map__frame {
  min-height: min(70vh, 32rem) !important;
}

.ts-map[data-show-title="0"][data-show-address="0"][data-show-note="0"] .ts-map__frame iframe {
  min-height: min(70vh, 32rem) !important;
}

/* CTA: ha nincs szöveg, a gomb középre / önállóan */
.ts-cta[data-show-title="0"][data-show-text="0"] .ts-cta__inner {
  justify-content: center !important;
}
CSS;
    }
}
