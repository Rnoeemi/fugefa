<?php

namespace App\Support\GrapesJs;

/**
 * HTML ID / horgony (data-anchor-id → id) szinkron mentéskor és megjelenítéskor.
 */
final class SiteAnchorId
{
    public static function sanitize(string $raw): string
    {
        $value = trim($raw);
        if ($value === '') {
            return '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($value, \Normalizer::FORM_D);
            if (is_string($normalized)) {
                $value = $normalized;
            }
        }

        $value = (string) preg_replace('/\p{Mn}/u', '', $value);
        $value = (string) preg_replace('/\s+/u', '-', $value);
        $value = (string) preg_replace('/[^A-Za-z0-9_-]/', '', $value);
        $value = ltrim($value, '-');

        if ($value === '') {
            return '';
        }

        if (! preg_match('/^[A-Za-z]/', $value)) {
            $value = 's-'.$value;
        }

        return substr($value, 0, 80);
    }

    public static function syncInHtml(string $html): string
    {
        if ($html === '' || ! str_contains($html, 'data-anchor-id')) {
            return $html;
        }

        return (string) preg_replace_callback(
            '/<([a-zA-Z][\w:-]*)([^>]*\bdata-anchor-id=(["\'])(.*?)\3[^>]*)>/',
            static function (array $m): string {
                $tag = $m[1];
                $attrs = $m[2];
                $quote = $m[3];
                $raw = html_entity_decode($m[4], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $clean = self::sanitize($raw);

                if ($clean === '') {
                    $attrs = (string) preg_replace('/\s*data-anchor-id=(["\']).*?\1/', '', $attrs);

                    return '<'.$tag.$attrs.'>';
                }

                if (preg_match('/\bid=(["\'])(.*?)\1/', $attrs)) {
                    $attrs = (string) preg_replace(
                        '/\bid=(["\']).*?\1/',
                        'id='.$quote.e($clean).$quote,
                        $attrs,
                        1
                    );
                } else {
                    $attrs .= ' id='.$quote.e($clean).$quote;
                }

                $attrs = (string) preg_replace(
                    '/\bdata-anchor-id=(["\']).*?\1/',
                    'data-anchor-id='.$quote.e($clean).$quote,
                    $attrs,
                    1
                );

                return '<'.$tag.$attrs.'>';
            },
            $html
        );
    }
}
