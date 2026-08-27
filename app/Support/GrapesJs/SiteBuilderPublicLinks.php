<?php

namespace App\Support\GrapesJs;

use App\Models\Accommodation;
use App\Models\SitePage;
use Illuminate\Support\Facades\Route;

final class SiteBuilderPublicLinks
{
    /**
     * @return list<array{label: string, url: string, group: string}>
     */
    public static function all(): array
    {
        $links = [];
        $seen = [];

        $add = function (string $label, string $url, string $group) use (&$links, &$seen): void {
            $url = self::normalizeUrl($url);
            if ($url === '' || isset($seen[$url])) {
                return;
            }
            $seen[$url] = true;
            $links[] = [
                'label' => $label,
                'url' => $url,
                'group' => $group,
            ];
        };

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if (self::isProtectedRoute($route->gatherMiddleware())) {
                continue;
            }

            $uri = ltrim($route->uri(), '/');
            if (self::shouldSkipUri($uri)) {
                continue;
            }

            // Kötelező paraméteres route-ok külön (oldalak / szállások) kerülnek be
            $uriWithoutOptional = preg_replace('/\/?\{[^}]+\?\}/', '', $uri);
            if (preg_match('/\{[^}?]+\}/', (string) $uriWithoutOptional)) {
                continue;
            }

            $url = $uriWithoutOptional === '' ? '/' : '/'.ltrim((string) $uriWithoutOptional, '/');
            $name = (string) ($route->getName() ?: '');
            $label = self::labelForRoute($name, $url);
            if ($label === '') {
                continue;
            }
            $add($label, $url, 'Oldalak');
        }

        SitePage::query()
            ->published()
            ->orderBy('title')
            ->get(['title', 'slug', 'is_homepage'])
            ->each(function (SitePage $page) use ($add): void {
                if ($page->is_homepage) {
                    $add($page->title.' (főoldal)', '/', 'Weboldal oldalak');

                    return;
                }

                $slug = trim((string) $page->slug);
                if ($slug === '') {
                    return;
                }

                $add($page->title, route('site-pages.show', ['slug' => $slug], false), 'Weboldal oldalak');
            });

        $add('Foglalás', '/foglalas-panel/foglalas', 'Oldalak');

        Accommodation::query()
            ->orderBy('name')
            ->get(['name', 'slug'])
            ->each(function (Accommodation $accommodation) use ($add): void {
                $add(
                    $accommodation->name,
                    route('accommodations.show', ['accommodation' => $accommodation->slug], false),
                    'Szállások'
                );
                $add(
                    $accommodation->name.' – foglalás',
                    '/foglalas-panel/foglalas/'.$accommodation->slug,
                    'Szállások'
                );
            });

        usort($links, function (array $a, array $b): int {
            return [$a['group'], $a['label']] <=> [$b['group'], $b['label']];
        });

        return $links;
    }

    /**
     * @param  list<string|object>  $middleware
     */
    protected static function isProtectedRoute(array $middleware): bool
    {
        foreach ($middleware as $item) {
            $name = is_string($item) ? $item : (string) $item;
            if ($name === 'auth' || str_starts_with($name, 'auth:')) {
                return true;
            }
        }

        return false;
    }

    protected static function shouldSkipUri(string $uri): bool
    {
        if ($uri === '') {
            return false;
        }

        $prefixes = [
            'site-builder',
            'rx-panel',
            'foglalas-panel',
            'recepcio',
            'livewire',
            'filament',
            '_debugbar',
            'sanctum',
            'up',
            'ical',
            'vendor',
            'storage',
        ];

        foreach ($prefixes as $prefix) {
            if ($uri === $prefix || str_starts_with($uri, $prefix.'/')) {
                return true;
            }
        }

        // Livewire asset hash prefix: livewire-ac1f7213/...
        if (preg_match('/^livewire-[a-f0-9]+\//i', $uri)) {
            return true;
        }

        return false;
    }

    protected static function labelForRoute(string $name, string $url): string
    {
        if ($name !== '' && (str_starts_with($name, 'filament.') || str_starts_with($name, 'livewire.'))) {
            return '';
        }

        return match ($name) {
            'home' => 'Főoldal',
            'accommodations.index' => 'Szállások listája',
            'contact' => 'Kapcsolat',
            default => $name !== '' ? $name : $url,
        };
    }

    protected static function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return $url;
        }

        if (! str_starts_with($url, '/')) {
            $url = '/'.$url;
        }

        return $url === '//' ? '/' : $url;
    }
}
