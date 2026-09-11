<?php

namespace App\Support\Seo;

use App\Models\SitePage;
use App\Models\SiteSetting;

final class SitePageSeo
{
    /**
     * @return array<string, string>
     */
    public static function empty(): array
    {
        return [
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'robots' => 'index,follow',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
            'og_type' => 'website',
            'og_url' => '',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => '',
            'twitter_description' => '',
            'twitter_image' => '',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $seo
     * @return array<string, string>
     */
    public static function normalize(?array $seo): array
    {
        $base = self::empty();

        if (! is_array($seo)) {
            return $base;
        }

        foreach ($base as $key => $default) {
            if (array_key_exists($key, $seo) && $seo[$key] !== null) {
                $base[$key] = trim((string) $seo[$key]);
            }
        }

        return $base;
    }

    /**
     * Kitölti az üres ismétlődő mezőket a forrásértékekből.
     *
     * @param  array<string, string>  $seo
     * @return array<string, string>
     */
    public static function withAutoFill(array $seo, SitePage $page): array
    {
        $seo = self::normalize($seo);
        $pageUrl = self::pageUrl($page);

        if ($seo['meta_title'] === '') {
            $seo['meta_title'] = $page->title;
        }

        if ($seo['canonical_url'] === '') {
            $seo['canonical_url'] = $pageUrl;
        }

        if ($seo['og_title'] === '') {
            $seo['og_title'] = $seo['meta_title'];
        }

        if ($seo['og_description'] === '') {
            $seo['og_description'] = $seo['meta_description'];
        }

        if ($seo['og_url'] === '') {
            $seo['og_url'] = $seo['canonical_url'] !== '' ? $seo['canonical_url'] : $pageUrl;
        }

        if ($seo['twitter_title'] === '') {
            $seo['twitter_title'] = $seo['og_title'] !== '' ? $seo['og_title'] : $seo['meta_title'];
        }

        if ($seo['twitter_description'] === '') {
            $seo['twitter_description'] = $seo['og_description'] !== '' ? $seo['og_description'] : $seo['meta_description'];
        }

        if ($seo['twitter_image'] === '') {
            $seo['twitter_image'] = $seo['og_image'];
        }

        if ($seo['robots'] === '') {
            $seo['robots'] = 'index,follow';
        }

        if ($seo['og_type'] === '') {
            $seo['og_type'] = 'website';
        }

        if ($seo['twitter_card'] === '') {
            $seo['twitter_card'] = 'summary_large_image';
        }

        return $seo;
    }

    public static function pageUrl(SitePage $page): string
    {
        if ($page->is_homepage) {
            return url('/');
        }

        $slug = trim((string) $page->slug);
        if ($slug === '') {
            return url('/');
        }

        return route('site-pages.show', ['slug' => $slug]);
    }

    /**
     * @return array<string, string>
     */
    public static function forPage(SitePage $page): array
    {
        return self::withAutoFill(self::normalize($page->seo), $page);
    }

    public static function absoluteUrl(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }

    public static function siteName(): string
    {
        return SiteSetting::current()->site_name ?: 'Fügefa építésziroda';
    }
}
