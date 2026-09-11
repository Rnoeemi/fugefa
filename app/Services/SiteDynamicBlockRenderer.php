<?php

namespace App\Services;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\SiteSetting;
use App\Models\Worker;
use App\Support\GrapesJs\SiteContentIcons;
use App\Support\GrapesJs\SiteRichAttr;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SiteDynamicBlockRenderer
{
    /**
     * Replace dynamic placeholders and sync static media src from data-media-url.
     */
    public function hydrate(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $needsDynamic = str_contains($html, 'data-ts-dynamic');
        $needsMedia = str_contains($html, 'data-media-url')
            || str_contains($html, 'data-background-url')
            || str_contains($html, 'data-ts-src-from')
            || str_contains($html, 'data-embed-url')
            || str_contains($html, 'data-ts-items')
            || str_contains($html, 'ts-gallery')
            || str_contains($html, 'data-items');

        if (! $needsDynamic && ! $needsMedia) {
            return $html;
        }

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $previous = libxml_use_internal_errors(true);
            $wrapped = '<div id="ts-dyn-root">'.$html.'</div>';
            $dom->loadHTML('<?xml encoding="UTF-8">'.$wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            $xpath = new DOMXPath($dom);

            if ($needsMedia) {
                $this->syncStaticMedia($dom, $xpath);
                $this->sanitizeHeroSliders($dom, $xpath);
            }

            if ($needsDynamic) {
                /** @var \DOMNodeList<int, DOMElement>|false $nodes */
                $nodes = $xpath->query('//*[@data-ts-dynamic]');

                if ($nodes !== false) {
                    /** @var list<DOMElement> $elements */
                    $elements = [];
                    foreach ($nodes as $node) {
                        if ($node instanceof DOMElement) {
                            $elements[] = $node;
                        }
                    }

                    foreach ($elements as $element) {
                        $type = $element->getAttribute('data-ts-dynamic');
                        $attrs = $this->attributesFromElement($element);
                        $inner = $this->renderBlock($type, $attrs);

                        while ($element->firstChild) {
                            $element->removeChild($element->firstChild);
                        }

                        $this->appendHtml($dom, $element, $inner);
                    }
                }
            }

            $root = $dom->getElementById('ts-dyn-root');

            if (! $root) {
                return $html;
            }

            $output = '';
            foreach ($root->childNodes as $child) {
                $output .= $dom->saveHTML($child);
            }

            return $output;
        } catch (Throwable) {
            return $html;
        }
    }

    protected function syncStaticMedia(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $sections */
        $sections = $xpath->query('//*[@data-media-url or @data-background-url]');

        if ($sections !== false) {
            foreach ($sections as $section) {
                if (! $section instanceof DOMElement) {
                    continue;
                }

                $mediaUrl = $section->getAttribute('data-media-url');
                if ($mediaUrl === '') {
                    $mediaUrl = $section->getAttribute('data-background-url');
                }

                foreach ($section->getElementsByTagName('img') as $img) {
                    if (! $img instanceof DOMElement) {
                        continue;
                    }
                    if ($img->hasAttribute('data-ts-bg-image') || $img->hasAttribute('data-ts-hero-image')) {
                        $img->setAttribute('src', $mediaUrl);
                    }
                }

                foreach ($section->getElementsByTagName('source') as $source) {
                    if (! $source instanceof DOMElement) {
                        continue;
                    }
                    if ($source->hasAttribute('data-ts-bg-source') || $source->hasAttribute('data-ts-hero-source')) {
                        $source->setAttribute('src', $mediaUrl);
                    }
                }

                $bgNodes = (new DOMXPath($dom))->query('.//*[@data-ts-bg-style and not(@data-ts-src-from)]', $section);
                if ($bgNodes !== false) {
                    foreach ($bgNodes as $node) {
                        if (! $node instanceof DOMElement) {
                            continue;
                        }
                        $style = $node->getAttribute('style');
                        $bg = $mediaUrl !== '' ? 'background-image:url("'.$mediaUrl.'")' : 'background-image:none';
                        $node->setAttribute('style', $this->mergeBackgroundStyle($style, $bg));
                    }
                }
            }
        }

        /** @var \DOMNodeList<int, DOMElement>|false $srcFromNodes */
        $srcFromNodes = $xpath->query('//*[@data-ts-src-from]');
        if ($srcFromNodes !== false) {
            foreach ($srcFromNodes as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                $key = $node->getAttribute('data-ts-src-from');
                if ($key === '') {
                    continue;
                }

                $section = $node;
                while ($section instanceof DOMElement && ! $section->hasAttribute('data-gjs-type')) {
                    $section = $section->parentNode;
                }
                if (! $section instanceof DOMElement) {
                    continue;
                }

                $attrName = 'data-'.str_replace('_', '-', $key);
                $value = $section->getAttribute($attrName);

                if (strtolower($node->tagName) === 'img') {
                    $node->setAttribute('src', $value);
                } else {
                    $bg = $value !== '' ? 'background-image:url("'.$value.'")' : 'background-image:none';
                    $node->setAttribute('style', $this->mergeBackgroundStyle($node->getAttribute('style'), $bg));
                }
            }
        }

        /** @var \DOMNodeList<int, DOMElement>|false $embedSections */
        $embedSections = $xpath->query('//*[@data-embed-url]');
        if ($embedSections !== false) {
            foreach ($embedSections as $section) {
                if (! $section instanceof DOMElement) {
                    continue;
                }
                $embed = $section->getAttribute('data-embed-url');
                foreach ($section->getElementsByTagName('iframe') as $frame) {
                    if ($frame instanceof DOMElement) {
                        $frame->setAttribute('src', $embed);
                    }
                }
            }
        }

        $this->ensureGalleryItemContainers($dom, $xpath);
        $this->ensureBaGalleryLayout($dom, $xpath);
        $this->syncBaGallerySections($dom, $xpath);
        $this->syncItemsContainers($dom, $xpath);
    }

    /**
     * Régi Képsáv: background-image div-ek / data-image1…4 → data-ts-items rács.
     */
    protected function ensureGalleryItemContainers(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $galleries */
        $galleries = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " ts-gallery ")]');
        if ($galleries === false) {
            return;
        }

        foreach ($galleries as $section) {
            if (! $section instanceof DOMElement) {
                continue;
            }

            $existing = null;
            foreach ($xpath->query('.//*[@data-ts-items-kind="gallery" or @data-ts-items="items"]', $section) ?: [] as $node) {
                if ($node instanceof DOMElement) {
                    $existing = $node;
                    break;
                }
            }

            $hasLegacy = $section->hasAttribute('data-image1')
                || $section->hasAttribute('data-image2')
                || ($xpath->query('.//*[@data-ts-src-from]', $section)?->length ?? 0) > 0;

            if ($existing instanceof DOMElement && ! $hasLegacy) {
                continue;
            }

            $items = $this->galleryItemsFromSection($section);
            $itemsJson = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]';
            $section->setAttribute('data-items', $itemsJson);

            $layout = $section->getAttribute('data-layout');
            if ($layout === 'featured') {
                $section->setAttribute('data-layout', 'grid');
            } elseif ($layout === 'mosaic') {
                $section->setAttribute('data-layout', 'masonry');
            }

            foreach (['data-image1', 'data-image2', 'data-image3', 'data-image4'] as $legacyAttr) {
                $section->removeAttribute($legacyAttr);
            }

            if ($existing instanceof DOMElement) {
                while ($existing->firstChild) {
                    $existing->removeChild($existing->firstChild);
                }
                $existing->setAttribute('data-ts-items', 'items');
                $existing->setAttribute('data-ts-items-kind', 'gallery');

                continue;
            }

            $inner = null;
            foreach ($section->childNodes as $child) {
                if ($child instanceof DOMElement && str_contains(' '.$child->getAttribute('class').' ', ' ts-gallery__inner ')) {
                    $inner = $child;
                    break;
                }
            }
            if (! $inner instanceof DOMElement) {
                $inner = $dom->createElement('div');
                $inner->setAttribute('class', 'ts-gallery__inner');
                $toMove = [];
                foreach ($section->childNodes as $child) {
                    if ($child instanceof DOMElement && strtolower($child->tagName) === 'style') {
                        continue;
                    }
                    $toMove[] = $child;
                }
                foreach ($toMove as $child) {
                    $inner->appendChild($child);
                }
                $section->insertBefore($inner, $section->firstChild);
            }

            $grid = $dom->createElement('div');
            $grid->setAttribute('class', 'ts-gallery__grid');
            $grid->setAttribute('data-ts-items', 'items');
            $grid->setAttribute('data-ts-items-kind', 'gallery');

            $remove = [];
            foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ts-gallery__grid ")]', $inner) ?: [] as $oldGrid) {
                if ($oldGrid instanceof DOMElement) {
                    $remove[] = $oldGrid;
                }
            }
            foreach ($remove as $node) {
                $node->parentNode?->removeChild($node);
            }

            $inner->appendChild($grid);
        }
    }

    /**
     * Referencia blokk: egy munka / blokk. Régi slider / multi-work markup → egy főkép + szöveg.
     */
    protected function ensureBaGalleryLayout(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $sections */
        $sections = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery ")]');
        if ($sections === false) {
            return;
        }

        foreach ($sections as $section) {
            if (! $section instanceof DOMElement) {
                continue;
            }

            $this->promoteLegacyBaGalleryAttrs($section);

            $inner = null;
            foreach ($section->childNodes as $child) {
                if ($child instanceof DOMElement && str_contains(' '.$child->getAttribute('class').' ', ' ts-ba-gallery__inner ')) {
                    $inner = $child;
                    break;
                }
            }
            if (! $inner instanceof DOMElement) {
                $inner = $dom->createElement('div');
                $inner->setAttribute('class', 'ts-ba-gallery__inner');
                $toMove = [];
                foreach ($section->childNodes as $child) {
                    if ($child instanceof DOMElement && strtolower($child->tagName) === 'style') {
                        continue;
                    }
                    $toMove[] = $child;
                }
                foreach ($toMove as $child) {
                    $inner->appendChild($child);
                }
                $section->insertBefore($inner, $section->firstChild);
            }

            // Remove old chrome / multi-work lists / titles.
            $remove = [];
            foreach ($xpath->query('.//*[@data-ts-ba-prev or @data-ts-ba-next or @data-ts-ba-dots or @data-ts-items or contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__stage ") or contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__list ") or contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__track ") or contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__title ")]', $inner) ?: [] as $node) {
                if ($node instanceof DOMElement) {
                    $remove[] = $node;
                }
            }
            foreach ($remove as $node) {
                $node->parentNode?->removeChild($node);
            }

            $work = null;
            foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__work ")]', $inner) ?: [] as $node) {
                if ($node instanceof DOMElement) {
                    $work = $node;
                    break;
                }
            }
            if (! $work instanceof DOMElement) {
                $work = $dom->createElement('article');
                $work->setAttribute('class', 'ts-ba-gallery__work');
                $work->setAttribute('data-ts-ba-slide', '');
                $inner->appendChild($work);
            }

            $coverBtn = null;
            foreach ($work->getElementsByTagName('button') as $btn) {
                if ($btn instanceof DOMElement && str_contains(' '.$btn->getAttribute('class').' ', ' ts-ba-gallery__cover ')) {
                    $coverBtn = $btn;
                    break;
                }
            }
            if (! $coverBtn instanceof DOMElement) {
                while ($work->firstChild) {
                    $work->removeChild($work->firstChild);
                }
                $coverBtn = $dom->createElement('button');
                $coverBtn->setAttribute('type', 'button');
                $coverBtn->setAttribute('class', 'ts-ba-gallery__cover');
                $coverBtn->setAttribute('data-ts-ba-zoom', '');
                $coverBtn->setAttribute('aria-label', 'Képek megnyitása');
                $img = $dom->createElement('img');
                $img->setAttribute('class', 'ts-ba-gallery__photo');
                $img->setAttribute('data-ts-src-from', 'cover');
                $img->setAttribute('loading', 'lazy');
                $coverBtn->appendChild($img);
                $work->appendChild($coverBtn);
                $copy = $dom->createElement('div');
                $copy->setAttribute('class', 'ts-ba-gallery__copy');
                $copy->setAttribute('data-ts-ba-copy', '');
                $work->appendChild($copy);
            } else {
                // Strip zoom SVGs that explode in GrapesJS.
                foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__zoom ")]', $work) ?: [] as $zoom) {
                    if ($zoom instanceof DOMElement) {
                        $zoom->parentNode?->removeChild($zoom);
                    }
                }
                $img = null;
                foreach ($coverBtn->getElementsByTagName('img') as $candidate) {
                    if ($candidate instanceof DOMElement) {
                        $img = $candidate;
                        break;
                    }
                }
                if (! $img instanceof DOMElement) {
                    $img = $dom->createElement('img');
                    $coverBtn->appendChild($img);
                }
                $img->setAttribute('class', 'ts-ba-gallery__photo');
                $img->setAttribute('data-ts-src-from', 'cover');
                $img->setAttribute('loading', 'lazy');

                $copy = null;
                foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__copy ")]', $work) ?: [] as $node) {
                    if ($node instanceof DOMElement) {
                        $copy = $node;
                        break;
                    }
                }
                if (! $copy instanceof DOMElement) {
                    $copy = $dom->createElement('div');
                    $copy->setAttribute('class', 'ts-ba-gallery__copy');
                    $copy->setAttribute('data-ts-ba-copy', '');
                    $work->appendChild($copy);
                }
            }
        }
    }

    protected function promoteLegacyBaGalleryAttrs(DOMElement $section): void
    {
        $cover = trim($section->getAttribute('data-cover'));
        $raw = $section->getAttribute('data-items');
        $items = json_decode($raw !== '' ? html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '[]', true);
        if (! is_array($items)) {
            $items = [];
        }

        $looksLikeLegacyWorks = false;
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            if (
                array_key_exists('description', $item)
                || array_key_exists('location', $item)
                || array_key_exists('area', $item)
                || array_key_exists('design', $item)
                || array_key_exists('construction', $item)
                || array_key_exists('image_2', $item)
            ) {
                $looksLikeLegacyWorks = true;
                break;
            }
        }

        if ($cover === '' && $items !== []) {
            $first = null;
            foreach ($items as $item) {
                if (is_array($item)) {
                    $first = $item;
                    break;
                }
            }
            if (is_array($first)) {
                $legacyCover = trim((string) ($first['image'] ?? $first['image_before'] ?? $first['media_url'] ?? ''));
                if ($legacyCover === '') {
                    $legacyCover = trim((string) ($first['image_after'] ?? ''));
                }
                if ($legacyCover !== '') {
                    $section->setAttribute('data-cover', $legacyCover);
                    $cover = $legacyCover;
                }
                foreach (['alt', 'description', 'location', 'area', 'design', 'construction'] as $key) {
                    $attr = 'data-'.$key;
                    if ($section->getAttribute($attr) === '' && trim((string) ($first[$key] ?? '')) !== '') {
                        $section->setAttribute($attr, (string) $first[$key]);
                    }
                }
            }
        }

        if ($looksLikeLegacyWorks) {
            $slides = [];
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $before = trim((string) ($item['image'] ?? $item['image_before'] ?? $item['media_url'] ?? ''));
                $after = trim((string) ($item['image_after'] ?? ''));
                $alt = (string) ($item['alt'] ?? '');
                if ($before !== '' || $after !== '') {
                    $slides[] = [
                        'image' => $before !== '' ? $before : $after,
                        'image_after' => $before !== '' ? $after : '',
                        'alt' => $alt,
                    ];
                }
                foreach (['image_2', 'image_3', 'image_4'] as $extra) {
                    $url = trim((string) ($item[$extra] ?? ''));
                    if ($url === '') {
                        continue;
                    }
                    $slides[] = ['image' => $url, 'image_after' => '', 'alt' => $alt];
                }
            }
            $section->setAttribute(
                'data-items',
                json_encode($slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]'
            );
        }

        if ($cover === '' && $section->getAttribute('data-media-url') !== '') {
            $section->setAttribute('data-cover', $section->getAttribute('data-media-url'));
        }
    }

    protected function syncBaGallerySections(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $sections */
        $sections = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery ")]');
        if ($sections === false) {
            return;
        }

        foreach ($sections as $section) {
            if (! $section instanceof DOMElement) {
                continue;
            }

            $cover = trim($section->getAttribute('data-cover'));
            $alt = (string) $section->getAttribute('data-alt');
            $fields = [
                'description' => (string) $section->getAttribute('data-description'),
                'location' => (string) $section->getAttribute('data-location'),
                'area' => (string) $section->getAttribute('data-area'),
                'design' => (string) $section->getAttribute('data-design'),
                'construction' => (string) $section->getAttribute('data-construction'),
                'alt' => $alt,
            ];

            $raw = $section->getAttribute('data-items');
            $slides = json_decode($raw !== '' ? html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '[]', true);
            if (! is_array($slides)) {
                $slides = [];
            }

            $payloadJson = json_encode($this->baGallerySlidesToLightbox($slides), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]';
            $copyHtml = $this->renderBaGalleryCopy($fields);
            $label = $alt !== '' ? 'Képek megnyitása: '.$alt : 'Képek megnyitása';

            foreach ($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__work ")]', $section) ?: [] as $work) {
                if (! $work instanceof DOMElement) {
                    continue;
                }
                $work->setAttribute('data-lightbox-items', $payloadJson);

                foreach ($work->getElementsByTagName('img') as $img) {
                    if (! $img instanceof DOMElement) {
                        continue;
                    }
                    if ($img->getAttribute('data-ts-src-from') === 'cover' || str_contains(' '.$img->getAttribute('class').' ', ' ts-ba-gallery__photo ')) {
                        $img->setAttribute('src', $cover);
                        $img->setAttribute('alt', $alt);
                    }
                }
                foreach ($work->getElementsByTagName('button') as $btn) {
                    if ($btn instanceof DOMElement && $btn->hasAttribute('data-ts-ba-zoom')) {
                        $btn->setAttribute('aria-label', $label);
                    }
                }

                $copy = null;
                foreach ($xpath->query('.//*[@data-ts-ba-copy or contains(concat(" ", normalize-space(@class), " "), " ts-ba-gallery__copy ")]', $work) ?: [] as $node) {
                    if ($node instanceof DOMElement) {
                        $copy = $node;
                        break;
                    }
                }
                if ($copy instanceof DOMElement) {
                    while ($copy->firstChild) {
                        $copy->removeChild($copy->firstChild);
                    }
                    if ($copyHtml !== '') {
                        if (preg_match('/^<div class="ts-ba-gallery__copy">(.*)<\/div>$/s', $copyHtml, $m)) {
                            $this->appendHtml($dom, $copy, $m[1]);
                        } else {
                            $this->appendHtml($dom, $copy, $copyHtml);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return list<array{image: string, alt: string}>
     */
    protected function galleryItemsFromSection(DOMElement $section): array
    {
        $raw = $section->getAttribute('data-items');
        $items = json_decode($raw !== '' ? html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '[]', true);
        if (is_array($items) && $items !== []) {
            $out = [];
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $url = trim((string) ($item['image'] ?? $item['media_url'] ?? ''));
                $out[] = [
                    'image' => $url,
                    'alt' => (string) ($item['alt'] ?? ''),
                ];
            }

            return $out;
        }

        $legacy = [];
        for ($i = 1; $i <= 4; $i++) {
            $url = trim((string) $section->getAttribute('data-image'.$i));
            if ($url !== '') {
                $legacy[] = ['image' => $url, 'alt' => ''];
            }
        }

        return $legacy;
    }

    /**
     * Hero slider: a Grapes „surface overlay” a teljes szekcióra ül (szöveg/gombok is),
     * pedig az overlay csak a média rétegen kell. Eltávolítjuk + solid overlay szín.
     */
    protected function sanitizeHeroSliders(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $sliders */
        $sliders = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " ts-hero-slider ")]');
        if ($sliders === false) {
            return;
        }

        foreach ($sliders as $slider) {
            if (! $slider instanceof DOMElement) {
                continue;
            }

            $toRemove = [];
            foreach ($slider->childNodes as $child) {
                if (! $child instanceof DOMElement) {
                    continue;
                }
                $class = ' '.$child->getAttribute('class').' ';
                $isSurface = $child->hasAttribute('data-ts-bg-overlay')
                    || str_contains($class, ' ts-surface-overlay ');
                $isSlideOverlay = $child->hasAttribute('data-ts-hero-overlay')
                    || str_contains($class, ' ts-hero-slider__overlay ');
                if ($isSurface && ! $isSlideOverlay) {
                    $toRemove[] = $child;
                }
            }
            foreach ($toRemove as $node) {
                $node->parentNode?->removeChild($node);
            }

            $overlayColor = trim($slider->getAttribute('data-overlay-color'));
            if ($overlayColor !== '' && preg_match(
                '/rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*[\d.]+\s*)?\)/i',
                $overlayColor,
                $m
            )) {
                $solid = 'rgb('.$m[1].', '.$m[2].', '.$m[3].')';
                $slider->setAttribute('data-overlay-color', $solid);
                $style = $slider->getAttribute('style');
                if ($style !== '') {
                    $style = preg_replace(
                        '/--ts-overlay-color\s*:\s*[^;]+;?/i',
                        '--ts-overlay-color: '.$solid.';',
                        $style
                    ) ?? $style;
                    $slider->setAttribute('style', $style);
                }
            }

            // Legacy üres stage eltávolítása (tartalom újra a slide-okban van)
            $toRemoveStages = [];
            foreach ($slider->childNodes as $child) {
                if (! $child instanceof DOMElement) {
                    continue;
                }
                $class = ' '.$child->getAttribute('class').' ';
                if ($child->hasAttribute('data-ts-slider-stage') || str_contains($class, ' ts-hero-slider__stage ')) {
                    $toRemoveStages[] = $child;
                }
            }
            foreach ($toRemoveStages as $node) {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    protected function syncItemsContainers(DOMDocument $dom, DOMXPath $xpath): void
    {
        /** @var \DOMNodeList<int, DOMElement>|false $containers */
        $containers = $xpath->query('//*[@data-ts-items]');
        if ($containers === false) {
            return;
        }

        foreach ($containers as $container) {
            if (! $container instanceof DOMElement) {
                continue;
            }

            $section = $container;
            while ($section instanceof DOMElement && ! $section->hasAttribute('data-gjs-type')) {
                $parent = $section->parentNode;
                $section = $parent instanceof DOMElement ? $parent : null;
            }
            if (! $section instanceof DOMElement) {
                continue;
            }

            $key = $container->getAttribute('data-ts-items') ?: 'items';
            $kind = $container->getAttribute('data-ts-items-kind') ?: $key;
            $attrName = 'data-'.str_replace('_', '-', $key);
            $raw = $section->getAttribute($attrName);
            if ($raw === '' && $section->hasAttribute('data-items')) {
                $raw = $section->getAttribute('data-items');
            }
            $items = json_decode($raw !== '' ? $raw : '[]', true);
            if (! is_array($items)) {
                $items = [];
            }

            // Régi Képsáv: data-image1…4
            if ($kind === 'gallery' && $items === []) {
                for ($i = 1; $i <= 4; $i++) {
                    $url = trim((string) $section->getAttribute('data-image'.$i));
                    if ($url !== '') {
                        $items[] = ['image' => $url, 'alt' => ''];
                    }
                }
            }

            while ($container->firstChild) {
                $container->removeChild($container->firstChild);
            }

            $layout = $section->getAttribute('data-layout') ?: '';
            $html = $this->renderItemsHtml($kind, $items, $layout);
            if ($html === '') {
                continue;
            }

            $this->appendHtml($dom, $container, $html);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function renderItemsHtml(string $kind, array $items, string $layout = ''): string
    {
        if ($kind === 'testimonials') {
            if ($layout === 'single') {
                $items = array_slice(array_values($items), 0, 1);
            }

            $cards = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $quote = e((string) ($item['quote'] ?? ''));
                $cite = e((string) ($item['cite'] ?? ''));
                $cards .= '<blockquote class="ts-quote__card"><p>„'.$quote.'”</p><cite>'.$cite.'</cite></blockquote>';
            }

            if ($layout === 'grid' || $layout === 'single') {
                return $cards;
            }

            return $cards.$cards;
        }

        if ($kind === 'faq') {
            $html = '';
            foreach (array_values($items) as $index => $item) {
                $q = e((string) ($item['q'] ?? ''));
                $a = e((string) ($item['a'] ?? ''));
                $open = $index === 0 ? ' open' : '';
                $html .= '<details class="ts-faq__item"'.$open.'><summary>'.$q.'</summary><p>'.$a.'</p></details>';
            }

            return $html;
        }

        if ($kind === 'features') {
            $html = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $visual = $this->renderItemVisual($item, 'ts-feature__icon');
                $title = e((string) ($item['title'] ?? ''));
                $text = e((string) ($item['text'] ?? ''));
                $html .= '<article class="ts-feature">'.$visual
                    .'<h3 class="ts-feature__title">'.$title.'</h3>'
                    .'<p class="ts-feature__text">'.$text.'</p></article>';
            }

            return $html;
        }

        if ($kind === 'icon-list') {
            $html = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $visual = $this->renderItemVisual($item, 'ts-icon-item__icon');
                $title = e((string) ($item['title'] ?? ''));
                $text = e((string) ($item['text'] ?? ''));
                $html .= '<article class="ts-icon-item">'.$visual
                    .'<h3 class="ts-icon-item__title">'.$title.'</h3>'
                    .'<p class="ts-icon-item__text">'.$text.'</p></article>';
            }

            return $html;
        }

        if ($kind === 'hero-slides') {
            $html = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $mediaUrl = (string) ($item['media_url'] ?? '');
                $mediaType = preg_match('/\.(mp4|webm|ogg)(\?|$)/i', $mediaUrl) ? 'video' : 'image';
                $safeUrl = e($mediaUrl);
                $eyebrow = e((string) ($item['eyebrow'] ?? ''));
                $title = e((string) ($item['title'] ?? ''));
                $lead = e((string) ($item['lead'] ?? ''));
                $primaryLabel = e((string) ($item['primary_label'] ?? ''));
                $primaryHref = e((string) ($item['primary_href'] ?? '#'));
                $secondaryLabel = e((string) ($item['secondary_label'] ?? ''));
                $secondaryHref = e((string) ($item['secondary_href'] ?? '#'));

                $media = '<div class="ts-hero-slider__media" aria-hidden="true">'
                    .'<img class="ts-hero-slider__img" src="'.$safeUrl.'" alt="">'
                    .'<video class="ts-hero-slider__video" autoplay muted loop playsinline>'
                    .'<source src="'.$safeUrl.'" type="video/mp4">'
                    .'</video>'
                    .'<div class="ts-hero-slider__overlay" data-ts-hero-overlay></div>'
                    .'</div>';

                $actions = '';
                if ($primaryLabel !== '') {
                    $actions .= '<a class="ts-btn ts-btn--primary" href="'.$primaryHref.'">'.$primaryLabel.'</a>';
                }
                if ($secondaryLabel !== '') {
                    $actions .= '<a class="ts-btn ts-btn--inverse" href="'.$secondaryHref.'">'.$secondaryLabel.'</a>';
                }

                $inner = '<div class="ts-hero-slider__inner">'
                    .($eyebrow !== '' ? '<p class="ts-hero-slider__eyebrow">'.$eyebrow.'</p>' : '')
                    .'<h1 class="ts-hero-slider__title">'.$title.'</h1>'
                    .($lead !== '' ? '<p class="ts-hero-slider__lead">'.$lead.'</p>' : '')
                    .($actions !== '' ? '<div class="ts-hero-slider__actions">'.$actions.'</div>' : '')
                    .'</div>';

                $html .= '<article class="ts-hero-slider__slide" data-ts-slide data-media-type="'.$mediaType.'">'
                    .$media
                    .$inner
                    .'</article>';
            }

            return $html;
        }

        if ($kind === 'gallery') {
            $html = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $url = trim((string) ($item['image'] ?? $item['media_url'] ?? ''));
                $alt = (string) ($item['alt'] ?? '');
                if ($url === '') {
                    $html .= '<figure class="ts-gallery__item ts-gallery__item--empty" aria-hidden="true"></figure>';
                    continue;
                }
                $html .= $this->renderGalleryItemFigure($url, $alt);
            }

            return $html;
        }

        if ($kind === 'ba-gallery') {
            // Egy blokk = egy munka; a tartalmat syncBaGallerySections tölti.
            return '';
        }

        if ($kind === 'flipcards') {
            $html = '';
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $html .= $this->renderFlipcardItem($item);
            }

            return $html;
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function renderFlipcardItem(array $item): string
    {
        $flip = $this->flipcardEnabled($item) ? '1' : '0';
        $front = $this->renderFlipcardFace('front', $item);
        $back = $flip === '1' ? $this->renderFlipcardFace('back', $item) : '';

        return '<article class="ts-flipcard" data-flip="'.$flip.'">'
            .'<div class="ts-flipcard__scene">'
            .$front
            .$back
            .'</div></article>';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function flipcardEnabled(array $item): bool
    {
        $raw = $item['flip'] ?? true;
        if (is_bool($raw)) {
            return $raw;
        }

        $value = strtolower(trim((string) $raw));

        return ! in_array($value, ['0', 'false', 'no', 'nem', 'off'], true);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function renderFlipcardFace(string $side, array $item): string
    {
        $image = trim((string) ($item[$side.'_image'] ?? ''));
        $title = trim((string) ($item[$side.'_title'] ?? ''));
        $text = trim((string) ($item[$side.'_text'] ?? ''));

        $html = '<div class="ts-flipcard__face ts-flipcard__face--'.e($side).'">';
        if ($image !== '') {
            $html .= '<img class="ts-flipcard__media" src="'.e($image).'" alt="" loading="lazy">';
        }

        if ($title !== '' || $text !== '') {
            $html .= '<div class="ts-flipcard__copy">';
            if ($title !== '') {
                $html .= '<h3 class="ts-flipcard__title">'.e($title).'</h3>';
            }
            if ($text !== '') {
                $html .= '<p class="ts-flipcard__text">'.e($text).'</p>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderGalleryItemFigure(string $url, string $alt = ''): string
    {
        $safeUrl = e($url);
        $safeAlt = e($alt);
        $label = $alt !== '' ? e('Kép megnyitása: '.$alt) : 'Kép megnyitása';

        return '<figure class="ts-gallery__item">'
            .'<button type="button" class="ts-gallery__trigger" data-ts-gallery-src="'.$safeUrl.'" aria-label="'.$label.'">'
            .'<img src="'.$safeUrl.'" alt="'.$safeAlt.'" loading="lazy">'
            .'</button></figure>';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function baGalleryUrl(array $item, string $key): string
    {
        return trim((string) ($item[$key] ?? ''));
    }

    /**
     * @param  list<array<string, mixed>>  $slides
     * @return list<array<string, string>>
     */
    protected function baGallerySlidesToLightbox(array $slides): array
    {
        $items = [];
        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                continue;
            }
            $before = trim((string) ($slide['image'] ?? $slide['image_before'] ?? $slide['media_url'] ?? ''));
            $after = $this->baGalleryUrl($slide, 'image_after');
            $alt = (string) ($slide['alt'] ?? '');
            if ($before !== '' && $after !== '') {
                $items[] = [
                    'type' => 'compare',
                    'before' => $before,
                    'after' => $after,
                    'alt' => $alt,
                ];
            } else {
                $url = $before !== '' ? $before : $after;
                if ($url === '') {
                    continue;
                }
                $items[] = [
                    'type' => 'image',
                    'src' => $url,
                    'alt' => $alt,
                ];
            }
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function renderBaGalleryCopy(array $item): string
    {
        $rows = [];
        $description = trim((string) ($item['description'] ?? $item['text'] ?? $item['body'] ?? ''));
        if ($description !== '') {
            $rows[] = '<p class="ts-ba-gallery__lead">'.nl2br(e($description), false).'</p>';
        }

        $meta = [
            'location' => 'Helyszín',
            'area' => 'Hasznos alapterület összesen',
            'design' => 'Tervezés ideje',
            'construction' => 'Kivitelezés',
        ];
        foreach ($meta as $key => $label) {
            $value = trim((string) ($item[$key] ?? ''));
            if ($value === '') {
                continue;
            }
            $rows[] = '<p class="ts-ba-gallery__meta"><span class="ts-ba-gallery__meta-label">'.e($label).':</span> '.e($value).'</p>';
        }

        if ($rows === []) {
            return '';
        }

        return '<div class="ts-ba-gallery__copy">'.implode('', $rows).'</div>';
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function renderItemVisual(array $item, string $class): string
    {
        $image = trim((string) ($item['image'] ?? ''));
        if ($image !== '') {
            return '<span class="'.e($class).' '.e($class).'--media" aria-hidden="true">'
                .'<img src="'.e($image).'" alt="">'
                .'</span>';
        }

        return SiteContentIcons::svg((string) ($item['icon'] ?? ''), $class);
    }

    protected function mergeBackgroundStyle(string $style, string $backgroundDeclaration): string
    {
        $parts = array_values(array_filter(array_map('trim', explode(';', $style))));
        $parts = array_values(array_filter(
            $parts,
            fn (string $part): bool => $part !== '' && ! str_starts_with(strtolower($part), 'background-image:')
        ));
        $parts[] = $backgroundDeclaration;

        return implode(';', $parts).';';
    }

    /**
     * data-show-*="0" → elrejtés (hiányzó / egyéb = látható).
     *
     * @param  array<string, mixed>  $attrs
     */
    protected function isShown(array $attrs, string $field): bool
    {
        $keys = ['show_'.$field, 'data-show-'.str_replace('_', '-', $field)];
        if ($field === 'button') {
            $keys[] = 'show_button';
            $keys[] = 'data-show-button';
        }

        foreach ($keys as $key) {
            if (! array_key_exists($key, $attrs)) {
                continue;
            }
            $raw = $attrs[$key];
            if ($raw === null || $raw === '') {
                continue;
            }

            return ! in_array(strtolower(trim((string) $raw)), ['0', 'false', 'off', 'no'], true);
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $attrs
     * @return array{showTitle: bool, showText: bool, showButton: bool}
     */
    protected function visibilityFlags(array $attrs): array
    {
        return [
            'showTitle' => $this->isShown($attrs, 'title'),
            'showText' => $this->isShown($attrs, 'text'),
            'showButton' => $this->isShown($attrs, 'button'),
        ];
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function renderBlock(string $type, array $attrs = []): string
    {
        $attrs = $this->decodeContentAttrs($attrs);
        $settings = SiteSetting::current();
        $visibility = $this->visibilityFlags($attrs);
        $accommodationBlocks = [
            'accommodations-cards',
            'accommodations-list',
            'accommodation-featured',
            'booking-cta',
            'availability-search',
            'accommodation-details',
            'accommodation-gallery',
        ];

        if (in_array($type, $accommodationBlocks, true) && ! app(ModuleService::class)->accommodationEnabled()) {
            return '';
        }

        if ($type === 'appointment-booking' && ! app(ModuleService::class)->appointmentEnabled()) {
            return '';
        }

        return match ($type) {
            'accommodations-cards' => view('site.dynamic.accommodations-cards', [
                'title' => (string) ($attrs['title'] ?? 'Szállásaink'),
                'limit' => max(1, (int) ($attrs['limit'] ?? 12)),
                'accommodations' => $this->accommodations(
                    limit: max(1, (int) ($attrs['limit'] ?? 12)),
                    type: $attrs['type'] ?? null,
                ),
                ...$visibility,
            ])->render(),
            'accommodations-list' => view('site.dynamic.accommodations-list', [
                'title' => (string) ($attrs['title'] ?? 'Szálláslehetőségek'),
                'limit' => max(1, (int) ($attrs['limit'] ?? 12)),
                'accommodations' => $this->accommodations(
                    limit: max(1, (int) ($attrs['limit'] ?? 12)),
                    type: $attrs['type'] ?? null,
                ),
                ...$visibility,
            ])->render(),
            'accommodation-featured' => view('site.dynamic.accommodation-featured', [
                'accommodation' => $this->findAccommodation($attrs['slug'] ?? null),
                'title' => (string) ($attrs['title'] ?? 'Kiemelt szállás'),
                ...$visibility,
            ])->render(),
            'booking-cta' => view('site.dynamic.booking-cta', [
                'title' => (string) ($attrs['title'] ?? 'Foglaljon online'),
                'text' => (string) ($attrs['text'] ?? 'Nézze meg a szabad időpontokat, és indítsa el a foglalást.'),
                'button' => (string) ($attrs['button'] ?? 'Szállások'),
                ...$visibility,
            ])->render(),
            'availability-search' => view('site.dynamic.availability-search', [
                'title' => (string) ($attrs['title'] ?? 'Szabad helyek'),
                'text' => (string) ($attrs['text'] ?? 'Adja meg az időpontot, és indítsa el a foglalást.'),
                'button' => (string) ($attrs['button'] ?? 'Keresés'),
                'selected' => $this->findAccommodation($attrs['slug'] ?? null),
                'accommodations' => $this->accommodations(limit: 50),
                'checkIn' => (string) ($attrs['check_in'] ?? Carbon::today()->toDateString()),
                'checkOut' => (string) ($attrs['check_out'] ?? Carbon::today()->addDays(2)->toDateString()),
                'guests' => max(1, (int) ($attrs['guests'] ?? 2)),
                ...$visibility,
            ])->render(),
            'appointment-booking' => view('site.dynamic.appointment-booking', [
                'title' => (string) ($attrs['title'] ?? 'Időpontfoglalás'),
                'text' => (string) ($attrs['text'] ?? 'Válasszon munkatársat, és foglaljon időpontot online.'),
                'button' => (string) ($attrs['button'] ?? 'Időpont foglalása'),
                'workers' => $this->workers(limit: max(1, (int) ($attrs['limit'] ?? 12))),
                ...$visibility,
            ])->render(),
            'accommodation-details' => view('site.dynamic.accommodation-details', [
                'title' => (string) ($attrs['title'] ?? 'Részletek'),
                'accommodation' => $this->findAccommodation($attrs['slug'] ?? null),
                ...$visibility,
            ])->render(),
            'accommodation-gallery' => view('site.dynamic.accommodation-gallery', [
                'title' => (string) ($attrs['title'] ?? 'Galéria'),
                'accommodation' => $accommodation = $this->findAccommodation($attrs['slug'] ?? null),
                'images' => $accommodation
                    ? $accommodation->galleryUrls(max(1, (int) ($attrs['limit'] ?? 8)))
                    : [],
                ...$visibility,
            ])->render(),
            'contact-form' => view('site.dynamic.contact-form', [
                'title' => (string) ($attrs['title'] ?? 'Írjon nekünk'),
                'text' => (string) ($attrs['text'] ?? 'Kérdés esetén keressen bizalommal.'),
                'button' => (string) ($attrs['button'] ?? 'Küldés'),
                'privacyHref' => (string) ($attrs['privacy_href'] ?? '/oldal/adatkezelesi-tajekoztato'),
                'settings' => $settings,
                ...$visibility,
            ])->render(),
            'site-map' => view('site.dynamic.site-map', [
                'title' => (string) ($attrs['title'] ?? 'Helyszín'),
                'text' => (string) ($attrs['text'] ?? ''),
                'embedUrl' => (string) ($attrs['embed_url'] ?? 'https://maps.google.com/maps?q=Budapest&t=&z=13&ie=UTF8&iwloc=&output=embed'),
                'settings' => $settings,
                ...$visibility,
            ])->render(),
            default => '<p style="padding:1rem;opacity:.7;">Ismeretlen dinamikus blokk: '.e($type).'</p>',
        };
    }

    /**
     * @return Collection<int, Accommodation>
     */
    public function accommodations(?int $limit = 12, mixed $type = null): Collection
    {
        $query = Accommodation::query()
            ->bookableOnline()
            ->orderBy('sort_order')
            ->orderBy('name');

        if (filled($type) && $type !== 'all') {
            $enum = AccommodationType::tryFrom((string) $type);
            if ($enum) {
                $query->ofType($enum);
            }
        }

        return $query->limit($limit ?? 12)->get();
    }

    /**
     * @return list<array{slug: string, name: string, type: string}>
     */
    public function accommodationOptions(): array
    {
        return Accommodation::query()
            ->bookableOnline()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'slug', 'type'])
            ->map(fn (Accommodation $item): array => [
                'slug' => $item->slug,
                'name' => $item->name,
                'type' => $item->type->value,
            ])
            ->all();
    }

    /**
     * @return Collection<int, Worker>
     */
    public function workers(?int $limit = 12): Collection
    {
        return Worker::query()
            ->bookable()
            ->limit($limit ?? 12)
            ->get();
    }

    protected function findAccommodation(mixed $slug): ?Accommodation
    {
        if (filled($slug)) {
            $found = Accommodation::query()->bookableOnline()->where('slug', $slug)->first();
            if ($found) {
                return $found;
            }
        }

        return Accommodation::query()->bookableOnline()->orderBy('sort_order')->orderBy('name')->first();
    }

    /**
     * @param  array<string, mixed>  $attrs
     * @return array<string, mixed>
     */
    protected function decodeContentAttrs(array $attrs): array
    {
        foreach (['title', 'text', 'button'] as $key) {
            if (! isset($attrs[$key]) || ! is_string($attrs[$key])) {
                continue;
            }

            $attrs[$key] = SiteRichAttr::decode($attrs[$key]);
        }

        return $attrs;
    }

    /**
     * @return array<string, string>
     */
    protected function attributesFromElement(DOMElement $element): array
    {
        return [
            'title' => $element->getAttribute('data-title'),
            'limit' => $element->getAttribute('data-limit') ?: '12',
            'type' => $element->getAttribute('data-type') ?: 'all',
            'slug' => $element->getAttribute('data-slug'),
            'text' => $element->getAttribute('data-text'),
            'button' => $element->getAttribute('data-button'),
            'privacy_href' => $element->getAttribute('data-privacy-href') ?: '/oldal/adatkezelesi-tajekoztato',
            'embed_url' => $element->getAttribute('data-embed-url'),
            'check_in' => $element->getAttribute('data-check-in'),
            'check_out' => $element->getAttribute('data-check-out'),
            'guests' => $element->getAttribute('data-guests') ?: '2',
            'show_title' => $element->hasAttribute('data-show-title') ? $element->getAttribute('data-show-title') : '1',
            'show_text' => $element->hasAttribute('data-show-text') ? $element->getAttribute('data-show-text') : '1',
            'show_button' => $element->hasAttribute('data-show-button') ? $element->getAttribute('data-show-button') : '1',
        ];
    }

    protected function appendHtml(DOMDocument $dom, DOMElement $parent, string $html): void
    {
        if ($html === '') {
            return;
        }

        $tmp = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $tmp->loadHTML('<?xml encoding="UTF-8"><div id="frag">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $fragRoot = $tmp->getElementById('frag');
        if (! $fragRoot) {
            return;
        }

        foreach ($fragRoot->childNodes as $child) {
            $parent->appendChild($dom->importNode($child, true));
        }
    }
}
