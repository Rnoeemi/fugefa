<?php

namespace App\Support;

use App\Services\ModuleService;
use Illuminate\Support\Facades\Route;

/**
 * Rendszer „végpontok” a menükezelő Modellek paneljéhez (WooCommerce-szerű).
 *
 * @phpstan-type Endpoint array{
 *     key: string,
 *     label: string,
 *     url: string,
 *     icon: string,
 *     description?: string
 * }
 */
final class SiteMenuEndpoints
{
    /**
     * @return list<Endpoint>
     */
    public static function all(): array
    {
        $modules = app(ModuleService::class);
        $endpoints = [];

        if ($modules->accommodationEnabled()) {
            $endpoints[] = [
                'key' => 'accommodations.index',
                'label' => 'Szállások',
                'description' => 'Összes szállás listája',
                'url' => self::path('accommodations.index', '/szallasok'),
                'icon' => 'heroicon-o-building-storefront',
            ];

            $endpoints[] = [
                'key' => 'booking.general',
                'label' => 'Foglalás',
                'description' => 'Általános online foglaló oldal',
                'url' => '/foglalas-panel/foglalas',
                'icon' => 'heroicon-o-calendar-days',
            ];
        }

        if ($modules->appointmentEnabled()) {
            $endpoints[] = [
                'key' => 'appointment.booking',
                'label' => 'Időpontfoglalás',
                'description' => 'Időpontfoglaló felület',
                'url' => '/idopontfoglalas',
                'icon' => 'heroicon-o-clock',
            ];
        }

        $endpoints[] = [
            'key' => 'contact',
            'label' => 'Kapcsolat',
            'description' => 'Kapcsolat űrlap oldal',
            'url' => self::path('contact', '/kapcsolat'),
            'icon' => 'heroicon-o-envelope',
        ];

        return $endpoints;
    }

    /**
     * @return Endpoint|null
     */
    public static function find(string $key): ?array
    {
        foreach (self::all() as $endpoint) {
            if ($endpoint['key'] === $key) {
                return $endpoint;
            }
        }

        return null;
    }

    public static function urlsMatch(?string $a, ?string $b): bool
    {
        return self::normalizeUrl($a) === self::normalizeUrl($b);
    }

    public static function normalizeUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        $path = '/'.ltrim($path, '/');

        return rtrim($path, '/') === '' ? '/' : rtrim($path, '/');
    }

    protected static function path(string $routeName, string $fallback): string
    {
        if (Route::has($routeName)) {
            return self::normalizeUrl(route($routeName, [], false));
        }

        return self::normalizeUrl($fallback);
    }
}
