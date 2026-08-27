<?php

namespace App\Support\GrapesJs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use RuntimeException;

final class SiteDynamicBlockRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $blocks = [];

        foreach (File::files(resource_path('site-builder/blocks')) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $block = $this->decode($file->getPathname());

            if (($block['type'] ?? '') !== 'dynamic') {
                continue;
            }

            $id = (string) ($block['id'] ?? $file->getFilenameWithoutExtension());
            $block['id'] = $id;
            $block['__key'] = $id;
            $block['view'] = 'site.dynamic.'.($block['dynamicKey'] ?? '');
            $block['view_exists'] = View::exists($block['view']);
            $block['websites'] = $this->websitesForBlock($id);

            $blocks[] = $block;
        }

        usort($blocks, fn (array $a, array $b): int => strcmp((string) $a['label'], (string) $b['label']));

        return $blocks;
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        $path = $this->pathFor($id);

        if (! is_file($path)) {
            throw new RuntimeException("Dynamic block not found: {$id}");
        }

        $block = $this->decode($path);

        if (($block['type'] ?? '') !== 'dynamic') {
            throw new RuntimeException("Block is not dynamic: {$id}");
        }

        $block['id'] = $id;
        $block['__key'] = $id;
        $block['view'] = 'site.dynamic.'.($block['dynamicKey'] ?? '');
        $block['view_exists'] = View::exists($block['view']);
        $block['websites'] = $this->websitesForBlock($id);

        return $block;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): void
    {
        $existing = $this->find($id);

        $block = [
            'id' => $id,
            'label' => (string) ($data['label'] ?? $existing['label']),
            'category' => (string) ($data['category'] ?? $existing['category'] ?? 'accommodations'),
            'icon' => (string) ($data['icon'] ?? $existing['icon'] ?? 'square'),
            'keywords' => $this->normalizeKeywords($data['keywords'] ?? $existing['keywords'] ?? []),
            'scopes' => array_values($data['scopes'] ?? $existing['scopes'] ?? ['page']),
            'type' => 'dynamic',
            'dynamicKey' => (string) ($existing['dynamicKey'] ?? $id),
            'gjsType' => (string) ($existing['gjsType'] ?? $id),
            'className' => (string) ($data['className'] ?? $existing['className'] ?? 'ts-dyn-block'),
            'params' => $this->normalizeParams($data['params'] ?? $existing['params'] ?? []),
        ];

        $this->writeBlock($id, $block);
        $this->syncWebsiteMembership($id, array_values($data['websites'] ?? []));
        SiteBlockCatalog::flush();
    }

    /**
     * @return array<string, string>
     */
    public function categoryOptions(): array
    {
        $path = resource_path('site-builder/catalog.json');
        $catalog = $this->decode($path);
        $options = [];

        foreach ($catalog['categories'] ?? [] as $key => $meta) {
            $options[$key] = (string) ($meta['label'] ?? $key);
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function iconOptions(): array
    {
        $options = [];

        foreach (array_keys(BlockIcons::paths()) as $key) {
            $options[$key] = $key;
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function websiteOptions(): array
    {
        return SiteBlockCatalog::availableWebsites();
    }

    /**
     * @return array<string, string>
     */
    public function optionSetKeys(): array
    {
        $path = resource_path('site-builder/catalog.json');
        $catalog = $this->decode($path);
        $keys = array_keys($catalog['optionSets'] ?? []);
        $options = [
            'accommodations' => 'accommodations (élő szálláslista)',
        ];

        foreach ($keys as $key) {
            $options[$key] = $key;
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    protected function websitesForBlock(string $blockId): array
    {
        $matched = [];

        foreach (array_keys(SiteBlockCatalog::availableWebsites()) as $key) {
            if (in_array($blockId, SiteBlockCatalog::enabledBlockIds($key), true)) {
                $matched[] = $key;
            }
        }

        return $matched;
    }

    /**
     * @param  list<string>  $websiteKeys
     */
    protected function syncWebsiteMembership(string $blockId, array $websiteKeys): void
    {
        $wanted = array_fill_keys($websiteKeys, true);

        foreach (array_keys(SiteBlockCatalog::availableWebsites()) as $website) {
            $path = resource_path('site-builder/websites/'.$website.'.json');
            $preset = $this->decode($path);
            $blocks = array_values(array_unique(array_map('strval', $preset['blocks'] ?? [])));
            $exclude = array_values(array_unique(array_map('strval', $preset['exclude'] ?? [])));
            $inPack = $this->blockInPacks($blockId, $preset['packs'] ?? []);
            $currentlyEnabled = ! in_array($blockId, $exclude, true)
                && ($inPack || in_array($blockId, $blocks, true));
            $shouldEnable = isset($wanted[$website]);

            if ($currentlyEnabled === $shouldEnable) {
                continue;
            }

            if ($shouldEnable) {
                $exclude = array_values(array_filter($exclude, fn (string $id): bool => $id !== $blockId));
                if (! $inPack && ! in_array($blockId, $blocks, true)) {
                    $blocks[] = $blockId;
                }
            } else {
                $blocks = array_values(array_filter($blocks, fn (string $id): bool => $id !== $blockId));
                if (! in_array($blockId, $exclude, true)) {
                    $exclude[] = $blockId;
                }
            }

            $preset['blocks'] = $blocks;
            $preset['exclude'] = $exclude;
            $this->writeJson($path, $preset);
        }
    }

    /**
     * @param  list<mixed>  $packs
     */
    protected function blockInPacks(string $blockId, array $packs): bool
    {
        foreach ($packs as $pack) {
            $pack = (string) $pack;
            if ($pack === '*') {
                return true;
            }

            $path = resource_path('site-builder/packs/'.$pack.'.json');
            if (! is_file($path)) {
                continue;
            }

            $data = $this->decode($path);
            if (in_array($blockId, array_map('strval', $data['blocks'] ?? []), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    protected function writeBlock(string $id, array $block): void
    {
        $this->writeJson($this->pathFor($id), $block);
    }

    protected function pathFor(string $id): string
    {
        return resource_path('site-builder/blocks/'.$id.'.json');
    }

    /**
     * @return array<string, mixed>
     */
    protected function decode(string $path): array
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function writeJson(string $path, array $data): void
    {
        File::put(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
        );
    }

    /**
     * @param  mixed  $keywords
     * @return list<string>
     */
    protected function normalizeKeywords(mixed $keywords): array
    {
        if (is_string($keywords)) {
            $keywords = preg_split('/[\s,]+/', $keywords) ?: [];
        }

        if (! is_array($keywords)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($item) => trim((string) $item),
            $keywords
        )));
    }

    /**
     * @param  mixed  $params
     * @return list<array<string, mixed>>
     */
    protected function normalizeParams(mixed $params): array
    {
        if (! is_array($params)) {
            return [];
        }

        $normalized = [];

        foreach ($params as $param) {
            if (! is_array($param) || blank($param['key'] ?? null)) {
                continue;
            }

            $item = [
                'key' => (string) $param['key'],
                'attr' => (string) (filled($param['attr'] ?? null) ? $param['attr'] : ('data-'.$param['key'])),
                'label' => (string) ($param['label'] ?? $param['key']),
                'type' => (string) ($param['type'] ?? 'text'),
                'default' => (string) ($param['default'] ?? ''),
            ];

            if (($param['type'] ?? '') === 'number') {
                if (isset($param['min']) && $param['min'] !== '' && $param['min'] !== null) {
                    $item['min'] = (int) $param['min'];
                }
                if (isset($param['max']) && $param['max'] !== '' && $param['max'] !== null) {
                    $item['max'] = (int) $param['max'];
                }
            }

            if (($param['type'] ?? '') === 'select' && filled($param['options'] ?? null)) {
                $item['options'] = (string) $param['options'];
            }

            $normalized[] = $item;
        }

        return $normalized;
    }
}
