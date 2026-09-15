<?php

namespace App\Support;

/**
 * Google Maps megosztó / rövid link → iframe embed URL.
 */
final class GoogleMapsEmbed
{
    public static function normalize(?string $url): string
    {
        $url = trim(html_entity_decode((string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '') {
            return '';
        }

        if (self::isEmbedUrl($url)) {
            return $url;
        }

        $wasShort = self::isShortMapsUrl($url);
        if ($wasShort) {
            $resolved = self::resolveRedirect($url);
            if ($resolved !== '') {
                $url = $resolved;
            }
        }

        if (self::isEmbedUrl($url)) {
            return $url;
        }

        if (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)(?:,(\d+(?:\.\d+)?)z)?/', $url, $m)) {
            $lat = $m[1];
            $lng = $m[2];
            $zoom = isset($m[3]) ? (string) max(1, min(21, (int) round((float) $m[3]))) : '15';

            return 'https://maps.google.com/maps?q='.rawurlencode($lat.','.$lng).'&z='.$zoom.'&output=embed';
        }

        // "319m" zoom a rövid linkekben – koordináta a @ után
        if (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $url, $m)) {
            return 'https://maps.google.com/maps?q='.rawurlencode($m[1].','.$m[2]).'&z=16&output=embed';
        }

        if (preg_match('/[?&](?:q|query)=([^&]+)/i', $url, $m)) {
            return 'https://maps.google.com/maps?q='.$m[1].'&output=embed';
        }

        // Feloldatlan rövid linket ne kódoljunk lekérdezésként
        if ($wasShort && self::isShortMapsUrl($url)) {
            return '';
        }

        if (preg_match('#google\.[^/]+/maps#i', $url) || preg_match('#maps\.google\.#i', $url)) {
            $sep = str_contains($url, '?') ? '&' : '?';

            return $url.$sep.'output=embed';
        }

        // Cím / szabad szöveg
        return 'https://maps.google.com/maps?q='.rawurlencode($url).'&z=15&ie=UTF8&output=embed';
    }

    /**
     * data-embed-url attribútumok normalizálása oldal-HTML-ben.
     */
    public static function normalizeInHtml(string $html): string
    {
        if ($html === '' || ! str_contains($html, 'data-embed-url')) {
            return $html;
        }

        return (string) preg_replace_callback(
            '/\bdata-embed-url=(["\'])(.*?)\1/i',
            static function (array $m): string {
                $raw = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalized = self::normalize($raw);
                if ($normalized === '' || $normalized === $raw) {
                    return $m[0];
                }

                return 'data-embed-url='.$m[1].e($normalized).$m[1];
            },
            $html
        );
    }

    public static function isEmbedUrl(string $url): bool
    {
        return str_contains($url, 'output=embed')
            || str_contains($url, '/maps/embed')
            || str_contains($url, 'google.com/maps/embed');
    }

    public static function isShortMapsUrl(string $url): bool
    {
        return (bool) preg_match('#(?:maps\.app\.goo\.gl|goo\.gl/maps|g\.co/maps)#i', $url);
    }

    protected static function resolveRedirect(string $url): string
    {
        if (! function_exists('curl_init')) {
            return '';
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return '';
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml',
                'Accept-Language: hu-HU,hu;q=0.9,en;q=0.8',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_NOBODY => false,
            CURLOPT_HEADER => false,
        ]);

        curl_exec($ch);
        $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redir = (int) curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        curl_close($ch);

        if ($final === '' || ($code >= 400 && $code !== 0)) {
            return '';
        }

        if ($redir < 1 && self::isShortMapsUrl($final)) {
            return '';
        }

        return $final;
    }
}
