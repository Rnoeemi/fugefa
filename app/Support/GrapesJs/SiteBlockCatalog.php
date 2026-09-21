<?php

namespace App\Support\GrapesJs;

use App\Enums\SiteModule;
use App\Services\ModuleService;
use App\Services\SiteDynamicBlockRenderer;
use Illuminate\Support\Facades\File;
use RuntimeException;

final class SiteBlockCatalog
{
    private static ?array $meta = null;

    /** @var list<array<string, mixed>>|null */
    private static ?array $allBlocks = null;

    /** @var array<string, list<string>> */
    private static array $enabledIdsByWebsite = [];

    public static function flush(): void
    {
        self::$meta = null;
        self::$allBlocks = null;
        self::$enabledIdsByWebsite = [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function definitionsFor(string $scope = 'page', ?string $website = null): array
    {
        $meta = self::meta();
        $categories = $meta['categories'] ?? [];
        $optionSets = $meta['optionSets'] ?? [];
        $accommodationOptions = self::accommodationSelectOptions();
        $enabled = array_flip(self::enabledBlockIds($website));

        $blocks = [];

        foreach (self::allBlocks() as $block) {
            $id = (string) ($block['id'] ?? '');
            if ($id === '' || ! isset($enabled[$id])) {
                continue;
            }

            $moduleKey = $block['module'] ?? null;
            if (is_string($moduleKey) && $moduleKey !== '') {
                $siteModule = SiteModule::tryFrom($moduleKey);
                if ($siteModule && ! app(ModuleService::class)->isEnabled($siteModule)) {
                    continue;
                }
            }

            $scopes = $block['scopes'] ?? ['page'];
            if (! in_array($scope, $scopes, true)) {
                continue;
            }

            $categoryKey = (string) ($block['category'] ?? 'content');
            $categoryLabel = $categories[$categoryKey]['label'] ?? $categoryKey;
            $params = is_array($block['params'] ?? null) ? $block['params'] : [];
            $params = self::appendSectionWidthParam($params, $id, $block);
            $params = SiteBlockLayouts::appendLayoutParam($params, $id);
            $params = self::appendOverlayParams($params, $id);
            $params = SiteBlockVisibility::appendShowParams($params);
            $params = self::appendRevealChildrenParam($params);
            $params = self::appendExtraClassParam($params);
            $params = self::appendAnchorIdParam($params);
            $defaults = self::paramDefaults($params);

            $type = $block['type'] ?? 'static';
            $gjsType = (string) ($block['gjsType'] ?? $id);

            if ($type === 'dynamic') {
                $content = self::dynamicPlaceholder($block, $defaults);
            } else {
                $content = self::renderTemplate((string) ($block['template'] ?? ''), $defaults);
                if ($params !== []) {
                    $content = self::injectRootTraitAttributes(
                        $content,
                        $gjsType,
                        (string) ($block['label'] ?? $id),
                        $params,
                        $defaults,
                    );
                }
            }

            $keywords = $block['keywords'] ?? [];
            if (is_array($keywords)) {
                $keywords = implode(' ', $keywords);
            }

            $normalizedParams = self::normalizeParams($params, $optionSets, $accommodationOptions);

            $blocks[] = [
                'id' => $id,
                'label' => $block['label'],
                'category' => $categoryLabel,
                'categoryKey' => $categoryKey,
                'icon' => $block['icon'] ?? 'square',
                'media' => BlockIcons::media($block['icon'] ?? 'square'),
                'keywords' => trim($keywords.' '.$block['label'].' '.$categoryLabel),
                'content' => $content,
                'type' => $type,
                'params' => $normalizedParams,
                'dynamicKey' => $block['dynamicKey'] ?? null,
                'gjsType' => $gjsType,
                'interactive' => (bool) ($block['interactive'] ?? false) || ($type === 'static' && $normalizedParams !== []),
                'container' => (bool) ($block['container'] ?? false),
                'className' => $block['className'] ?? 'ts-dyn-block',
                'order' => (int) ($categories[$categoryKey]['order'] ?? 100),
            ];
        }

        usort($blocks, function (array $a, array $b): int {
            return [$a['order'], $a['label']] <=> [$b['order'], $b['label']];
        });

        return $blocks;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function dynamicDefinitionsFor(string $scope = 'page', ?string $website = null): array
    {
        return array_values(array_filter(
            self::definitionsFor($scope, $website),
            fn (array $block): bool => ($block['type'] ?? '') === 'dynamic'
        ));
    }

    /**
     * Static blocks with params → GrapesJS traits (Tulajdonságok).
     *
     * @return list<array<string, mixed>>
     */
    public static function interactiveDefinitionsFor(string $scope = 'page', ?string $website = null): array
    {
        return array_values(array_filter(
            self::definitionsFor($scope, $website),
            fn (array $block): bool => ($block['type'] ?? '') === 'static'
                && (
                    ! empty($block['params'])
                    || ! empty($block['container'])
                    || ! empty($block['interactive'])
                )
        ));
    }

    /**
     * Available website presets (filename key => label).
     *
     * @return array<string, string>
     */
    public static function availableWebsites(): array
    {
        $dir = resource_path('site-builder/websites');
        if (! is_dir($dir)) {
            return [];
        }

        $websites = [];

        foreach (File::files($dir) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $key = $file->getFilenameWithoutExtension();
            $data = self::decodeJsonFile($file->getPathname());
            $websites[$key] = (string) ($data['label'] ?? $key);
        }

        ksort($websites);

        return $websites;
    }

    /**
     * Resolved block IDs for a website (packs ∪ blocks − exclude).
     *
     * @return list<string>
     */
    public static function enabledBlockIds(?string $website = null): array
    {
        $website ??= self::activeWebsiteKey();

        if (isset(self::$enabledIdsByWebsite[$website])) {
            return self::$enabledIdsByWebsite[$website];
        }

        $path = resource_path('site-builder/websites/'.$website.'.json');

        if (! is_file($path)) {
            throw new RuntimeException("Missing site-builder website preset: {$path}");
        }

        $preset = self::decodeJsonFile($path);
        $ids = [];

        foreach ($preset['packs'] ?? [] as $pack) {
            $pack = (string) $pack;
            if ($pack === '*') {
                $ids = array_merge($ids, array_column(self::allBlocks(), 'id'));
                continue;
            }
            $ids = array_merge($ids, self::packBlockIds($pack));
        }

        foreach ($preset['blocks'] ?? [] as $blockId) {
            $ids[] = (string) $blockId;
        }

        $exclude = array_map('strval', $preset['exclude'] ?? []);
        $ids = array_values(array_unique(array_diff($ids, $exclude)));

        return self::$enabledIdsByWebsite[$website] = $ids;
    }

    public static function activeWebsiteKey(): string
    {
        return (string) config('site-builder.website', 'tusiszallas');
    }

    /**
     * @return array<string, mixed>
     */
    protected static function meta(): array
    {
        if (self::$meta !== null) {
            return self::$meta;
        }

        $path = resource_path('site-builder/catalog.json');

        if (! is_file($path)) {
            throw new RuntimeException("Missing site-builder catalog: {$path}");
        }

        return self::$meta = self::decodeJsonFile($path);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected static function allBlocks(): array
    {
        if (self::$allBlocks !== null) {
            return self::$allBlocks;
        }

        $dir = resource_path('site-builder/blocks');

        if (! is_dir($dir)) {
            throw new RuntimeException("Missing site-builder blocks directory: {$dir}");
        }

        $blocks = [];

        foreach (File::files($dir) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $block = self::decodeJsonFile($file->getPathname());
            if (! isset($block['id'])) {
                $block['id'] = $file->getFilenameWithoutExtension();
            }

            $blocks[] = $block;
        }

        return self::$allBlocks = $blocks;
    }

    /**
     * @return list<string>
     */
    protected static function packBlockIds(string $pack): array
    {
        $path = resource_path('site-builder/packs/'.$pack.'.json');

        if (! is_file($path)) {
            throw new RuntimeException("Missing site-builder pack: {$path}");
        }

        $data = self::decodeJsonFile($path);

        return array_map('strval', $data['blocks'] ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function decodeJsonFile(string $path): array
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * @param  array<string, string>  $params
     */
    protected static function renderTemplate(string $template, array $params): string
    {
        if ($template === '') {
            return '';
        }

        $path = resource_path('site-builder/templates/'.$template);

        if (! is_file($path)) {
            throw new RuntimeException("Missing block template: {$path}");
        }

        $html = (string) File::get($path);

        foreach (array_keys($params) as $key) {
            $quoted = preg_quote($key, '/');

            // Text-only tags: <h2>{{title}}</h2> → data-ts-text binding
            $html = (string) preg_replace_callback(
                '/(<[a-zA-Z][\w:-]*(?:\s[^>]*)?)(>)\s*\{\{'.$quoted.'\}\}\s*(<\/[a-zA-Z][\w:-]*>)/',
                function (array $m) use ($key): string {
                    if (str_contains($m[1], 'data-ts-text=')) {
                        return $m[0];
                    }

                    return $m[1].' data-ts-text="'.$key.'"'.$m[2].'{{'.$key.'}}'.$m[3];
                },
                $html
            );

            // href="{{key}}" → data-ts-href on same tag
            $html = (string) preg_replace_callback(
                '/(<[a-zA-Z][\w:-]*)(\s[^>]*?\s)?href="\{\{'.$quoted.'\}\}"/',
                function (array $m) use ($key): string {
                    $start = $m[1];
                    $middle = $m[2] ?? ' ';
                    if (str_contains($start.$middle, 'data-ts-href=')) {
                        return $m[0];
                    }

                    return $start.$middle.'data-ts-href="'.$key.'" href="{{'.$key.'}}"';
                },
                $html
            );
        }

        foreach ($params as $key => $value) {
            // Attribútum-helyettesítés: HTML tartalom kódolva + escape (ne törje a data-* értékeket)
            $html = (string) preg_replace_callback(
                '/(\s[\w:-]+=")\{\{'.preg_quote((string) $key, '/').'\}\}(")/',
                static function (array $m) use ($value): string {
                    $raw = (string) $value;
                    if (str_contains($raw, '<')) {
                        $raw = SiteRichAttr::encode($raw);
                    }

                    return $m[1].e($raw).$m[2];
                },
                $html
            );

            // Tartalmi helyettesítés (címkék között): nyers HTML
            $html = str_replace('{{'.$key.'}}', (string) $value, $html);
        }

        return (string) preg_replace('/\{\{[a-z0-9_]+\}\}/i', '', $html);
    }

    /**
     * Ensure the root element carries data-gjs-type + param attributes for traits.
     *
     * @param  list<array<string, mixed>>  $params
     * @param  array<string, string>  $defaults
     */
    protected static function injectRootTraitAttributes(
        string $html,
        string $gjsType,
        string $label,
        array $params,
        array $defaults,
    ): string {
        $attributes = [
            'data-gjs-type' => $gjsType,
            'data-gjs-name' => $label,
        ];

        foreach ($params as $param) {
            $key = (string) ($param['key'] ?? '');
            if ($key === '') {
                continue;
            }
            $attr = (string) ($param['attr'] ?? ('data-'.str_replace('_', '-', $key)));
            $raw = $defaults[$key] ?? $param['default'] ?? '';
            if (is_array($raw)) {
                $raw = json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $raw = (string) $raw;
            if (SiteRichAttr::isRichParam($param) || str_contains($raw, '<')) {
                $raw = SiteRichAttr::encode($raw);
            }
            $attributes[$attr] = $raw;
        }

        return (string) preg_replace_callback(
            '/^(\s*<[a-zA-Z][\w:-]*)(\s[^>]*)?(>)/',
            function (array $m) use ($attributes): string {
                $start = $m[1];
                $existing = $m[2] ?? '';
                $end = $m[3];

                foreach ($attributes as $name => $value) {
                    $pattern = '/\s'.preg_quote($name, '/').'\s*=\s*(["\'])(.*?)\\1/';
                    if (preg_match($pattern, $existing)) {
                        $existing = (string) preg_replace(
                            $pattern,
                            ' '.$name.'="'.e((string) $value).'"',
                            $existing,
                            1
                        );
                    } else {
                        $existing .= sprintf(' %s="%s"', $name, e((string) $value));
                    }
                }

                return $start.$existing.$end;
            },
            ltrim($html),
            1
        );
    }

    /**
     * @param  array<string, string>  $defaults
     */
    protected static function dynamicPlaceholder(array $block, array $defaults): string
    {
        $attrs = [
            'class' => $block['className'] ?? 'ts-dyn-block',
            'data-gjs-type' => $block['gjsType'] ?? $block['id'],
            'data-ts-dynamic' => $block['dynamicKey'] ?? '',
            'data-gjs-name' => $block['label'] ?? $block['id'],
        ];

        foreach ($block['params'] ?? [] as $param) {
            $key = (string) ($param['key'] ?? '');
            $attr = (string) ($param['attr'] ?? ('data-'.str_replace('_', '-', $key)));
            $attrs[$attr] = $defaults[$key] ?? (string) ($param['default'] ?? '');
        }

        $attrString = collect($attrs)
            ->map(fn ($value, $name) => sprintf('%s="%s"', $name, e((string) $value)))
            ->implode(' ');

        return '<section '.$attrString.'></section>';
    }

    /**
     * @param  list<array<string, mixed>>  $params
     * @return array<string, string>
     */
    protected static function paramDefaults(array $params): array
    {
        $defaults = [];

        foreach ($params as $param) {
            $key = (string) ($param['key'] ?? '');
            if ($key === '') {
                continue;
            }

            $default = $param['default'] ?? '';
            if (is_array($default)) {
                $defaults[$key] = json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $defaults[$key] = (string) $default;
            }
        }

        return $defaults;
    }

    /**
     * @param  list<array<string, mixed>>  $params
     * @param  array<string, mixed>  $optionSets
     * @param  list<array{id: string, name: string}>  $accommodationOptions
     * @return list<array<string, mixed>>
     */
    protected static function normalizeParams(array $params, array $optionSets, array $accommodationOptions): array
    {
        $normalized = [];

        foreach ($params as $param) {
            $item = $param;
            $key = (string) ($param['key'] ?? '');

            if ($key !== '' && blank($item['attr'] ?? null)) {
                $item['attr'] = 'data-'.str_replace('_', '-', $key);
            }

            $optionsKey = $param['options'] ?? null;

            if (is_string($optionsKey)) {
                $item['options'] = match ($optionsKey) {
                    'accommodations' => $accommodationOptions,
                    default => $optionSets[$optionsKey] ?? [],
                };
            }

            $normalized[] = $item;
        }

        return $normalized;
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    protected static function accommodationSelectOptions(): array
    {
        $options = [
            ['id' => '', 'name' => 'Automatikus (első szállás)'],
        ];

        foreach (app(SiteDynamicBlockRenderer::class)->accommodationOptions() as $item) {
            $options[] = [
                'id' => $item['slug'],
                'name' => $item['name'],
            ];
        }

        return $options;
    }

    /** Layout szekciók – Elementor-szerű tartalom / teljes szélesség beállítás. */
    private const LAYOUT_BLOCK_IDS = [
        'ts-layout-1col',
        'ts-layout-2col',
        'ts-layout-3col',
    ];

    /**
     * Szekciók, ahol nincs beépített media-overlay, de Style Manager háttérképhez kellhet overlay.
     * (hero/banner/bg-section/stats már saját overlay paramokkal jönnek)
     */
    private const OVERLAY_SURFACE_BLOCK_IDS = [
        'ts-cta',
        'ts-quote',
        'ts-social',
        'ts-buttons',
        'ts-text',
        'ts-features',
        'ts-icon-list',
        'ts-faq',
        'ts-split',
        'ts-contact',
        'ts-nearby',
        'ts-amenities',
        'ts-how-to-book',
        'ts-checkin',
        'ts-legal',
        'ts-pricing-table',
        'ts-video',
        'ts-logo-row',
        'ts-gallery',
        'ts-map',
        'ts-booking-cta',
    ];

    /**
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    protected static function appendSectionWidthParam(array $params, string $blockId, array $block): array
    {
        if (! in_array($blockId, self::LAYOUT_BLOCK_IDS, true)) {
            return $params;
        }

        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'section_width') {
                return $params;
            }
        }

        array_unshift($params, [
            'key' => 'section_width',
            'attr' => 'data-section-width',
            'label' => 'Tartalom szélesség',
            'type' => 'select',
            'default' => 'content',
            'options' => 'section_widths',
            'group' => 'layout',
            'groupLabel' => 'Elrendezés',
        ]);

        return $params;
    }

    /**
     * Overlay erősség + szín azokra a szekciókra, ahol csak Style Manageres háttérkép van.
     *
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    protected static function appendOverlayParams(array $params, string $blockId): array
    {
        if (! in_array($blockId, self::OVERLAY_SURFACE_BLOCK_IDS, true)) {
            return $params;
        }

        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'overlay') {
                return $params;
            }
        }

        array_unshift($params, [
            'key' => 'overlay_color',
            'attr' => 'data-overlay-color',
            'label' => 'Overlay szín',
            'type' => 'color',
            'default' => '',
            'group' => 'background',
            'groupLabel' => 'Háttér és overlay',
        ]);
        array_unshift($params, [
            'key' => 'overlay',
            'attr' => 'data-overlay',
            'label' => 'Overlay erősség',
            'type' => 'number',
            'min' => 0,
            'max' => 1,
            'step' => 0.05,
            'default' => '0',
            'group' => 'background',
            'groupLabel' => 'Háttér és overlay',
        ]);

        return $params;
    }

    /**
     * Szekción belül: címek, szövegek, kártyák egymás után jelennek meg görgetéskor.
     *
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    protected static function appendRevealChildrenParam(array $params): array
    {
        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'reveal_children') {
                return $params;
            }
        }

        $params[] = [
            'key' => 'reveal_children',
            'attr' => 'data-reveal-children',
            'label' => 'Belépési animáció (gyerek elemek)',
            'type' => 'checkbox',
            'default' => '0',
            'group' => 'animation',
            'groupLabel' => 'Animáció',
            'hint' => 'A szekción belüli címek, szövegek és kártyák egymás után jelennek meg görgetéskor.',
        ];

        return $params;
    }

    /**
     * Minden blokk root elemére: egyedi CSS class (szóközzel több is).
     *
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    protected static function appendExtraClassParam(array $params): array
    {
        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'extra_class') {
                return $params;
            }
        }

        $params[] = [
            'key' => 'extra_class',
            'attr' => 'data-extra-class',
            'label' => 'Egyedi CSS class',
            'type' => 'text',
            'default' => '',
            'group' => 'advanced',
            'groupLabel' => 'Haladó',
        ];

        return $params;
    }

    /**
     * Minden blokk root elemére: egyedi HTML id (horgony / #scroll cél).
     *
     * @param  list<array<string, mixed>>  $params
     * @return list<array<string, mixed>>
     */
    protected static function appendAnchorIdParam(array $params): array
    {
        foreach ($params as $param) {
            if (($param['key'] ?? '') === 'anchor_id') {
                return $params;
            }
        }

        $params[] = [
            'key' => 'anchor_id',
            'attr' => 'data-anchor-id',
            'label' => 'HTML ID (horgony)',
            'type' => 'text',
            'default' => '',
            'group' => 'advanced',
            'groupLabel' => 'Haladó',
            'hint' => 'Pl. pecs – linknél használd: #pecs. Betűvel kezdődjön, szóköz nélkül.',
        ];

        return $params;
    }
}
