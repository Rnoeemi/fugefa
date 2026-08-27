<?php

namespace App\Support\GrapesJs;

/**
 * Tartalmi ikonok (kártyák, ikonlista) – közös SVG készlet.
 */
final class SiteContentIcons
{
    /**
     * @return array<string, string> key => svg inner paths
     */
    public static function paths(): array
    {
        return array_merge(BlockIcons::paths(), [
            'wifi' => '<path d="M5 12.5a11 11 0 0114 0"/><path d="M8.5 16a6 6 0 017 0"/><circle cx="12" cy="20" r="1.2" fill="currentColor" stroke="none"/>',
            'parking' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 17V7h4.5a3.5 3.5 0 010 7H9"/>',
            'coffee' => '<path d="M6 9h10v5a4 4 0 01-4 4H10a4 4 0 01-4-4V9z"/><path d="M16 10h2.5a2.5 2.5 0 010 5H16"/><path d="M7 4v2M11 4v2M15 4v2M8 20h8"/>',
            'leaf' => '<path d="M5 19c8 0 14-6 14-14-8 0-14 6-14 14z"/><path d="M5 19c3-3 6-6 9-9"/>',
            'heart' => '<path d="M12 20s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 10c0 5.6-7 10-7 10z"/>',
            'users' => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 19a6 6 0 0112 0"/><path d="M14 19a5 5 0 017 0"/>',
            'key' => '<circle cx="8" cy="14" r="4"/><path d="M11 12l9-9M17 4l3 3"/>',
            'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
            'bed' => '<path d="M3 18V9a2 2 0 012-2h6a3 3 0 013 3v1h5a2 2 0 012 2v5"/><path d="M3 14h18M7 18v-2M17 18v-2"/>',
            'tree' => '<path d="M12 22v-6"/><path d="M7 16c0-4 2.5-7 5-9 2.5 2 5 5 5 9H7z"/><path d="M9 11c0-3 1.5-5 3-6.5 1.5 1.5 3 3.5 3 6.5"/>',
            'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
            'droplet' => '<path d="M12 3s6 7 6 11a6 6 0 11-12 0c0-4 6-11 6-11z"/>',
            'flame' => '<path d="M12 3c2 3 5 5 5 9a5 5 0 11-10 0c0-2 1-4 3-6 0 2 1 3 2 3 0-2 0-4 0-6z"/>',
            'paw' => '<circle cx="7" cy="9" r="2"/><circle cx="12" cy="7" r="2"/><circle cx="17" cy="9" r="2"/><path d="M8 15c0-2 1.5-3.5 4-3.5S16 13 16 15c0 2-1.5 4-4 4s-4-2-4-4z"/>',
            'car' => '<path d="M4 14l2-5a2 2 0 012-1.3h8A2 2 0 0118 9l2 5"/><path d="M3 14h18v4a1 1 0 01-1 1h-1a2 2 0 01-4 0H9a2 2 0 01-4 0H4a1 1 0 01-1-1v-4z"/>',
            'utensils' => '<path d="M7 3v8a2 2 0 002 2v8"/><path d="M5 3v5M9 3v5"/><path d="M16 3v7a2 2 0 002 2v9"/><path d="M16 3c2 0 3 1.5 3 3.5S18 10 16 10"/>',
            'bath' => '<path d="M6 11V6a2 2 0 012-2h1"/><path d="M4 14h16l-1 4a2 2 0 01-2 2H7a2 2 0 01-2-2l-1-4z"/><path d="M4 14V12a2 2 0 012-2h12a2 2 0 012 2v2"/>',
            'mountain' => '<path d="M3 19l6.5-10L13 15l2.5-4L21 19H3z"/><path d="M13 15l2-3 2 3"/>',
            'sparkle' => '<path d="M12 3l1.5 5L19 9.5 13.5 11 12 16l-1.5-5L5 9.5 10.5 8 12 3z"/>',
            'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/>',
            'none' => '',
        ]);
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public static function options(): array
    {
        $labels = [
            'none' => 'Nincs ikon',
            'check' => 'Pipás',
            'check-badge' => 'Jelvény',
            'star' => 'Csillag',
            'heart' => 'Szív',
            'home' => 'Ház',
            'bed' => 'Ágy',
            'key' => 'Kulcs',
            'wifi' => 'Wi‑Fi',
            'parking' => 'Parkoló',
            'car' => 'Autó',
            'coffee' => 'Kávé',
            'utensils' => 'Étkezés',
            'bath' => 'Fürdő',
            'leaf' => 'Levél',
            'tree' => 'Fa',
            'sun' => 'Nap',
            'droplet' => 'Víz',
            'flame' => 'Tűz',
            'mountain' => 'Hegy',
            'paw' => 'Állatbarát',
            'users' => 'Vendégek',
            'shield' => 'Biztonság',
            'clock' => 'Óra',
            'map-pin' => 'Helyszín',
            'phone' => 'Telefon',
            'envelope' => 'Email',
            'sparkle' => 'Csillogás',
            'sparkles' => 'Csillagok',
            'photo' => 'Kép',
            'calendar' => 'Naptár',
        ];

        $options = [];
        foreach ($labels as $id => $name) {
            if (! array_key_exists($id, self::paths()) && $id !== 'none') {
                continue;
            }
            $options[] = ['id' => $id, 'name' => $name];
        }

        return $options;
    }

    public static function svg(?string $icon, string $class = 'ts-icon'): string
    {
        $key = trim((string) $icon);
        if ($key === '' || $key === 'none') {
            return '';
        }

        $paths = self::paths();
        $inner = $paths[$key] ?? '';
        if ($inner === '') {
            return '';
        }

        return '<span class="'.e($class).'" aria-hidden="true">'
            .'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">'
            .$inner
            .'</svg></span>';
    }

    /**
     * @return list<array{id: string, name: string, svg: string}>
     */
    public static function optionsForBuilder(): array
    {
        $out = [];
        foreach (self::options() as $opt) {
            $out[] = [
                'id' => $opt['id'],
                'name' => $opt['name'],
                'svg' => self::svg($opt['id'] === 'none' ? '' : $opt['id'], 'ts-content-icon'),
            ];
        }

        return $out;
    }
}
