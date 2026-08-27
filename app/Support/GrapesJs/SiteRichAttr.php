<?php

namespace App\Support\GrapesJs;

/**
 * Rich HTML trait értékek biztonságos tárolása data-* attribútumokban.
 * Nyers HTML attribútumban töri a markupot (idézőjelek / tagek).
 */
final class SiteRichAttr
{
    public const PREFIX = 'html:';

    public static function encode(string $html): string
    {
        if ($html === '') {
            return '';
        }

        if (str_starts_with($html, self::PREFIX)) {
            return $html;
        }

        return self::PREFIX.rawurlencode($html);
    }

    public static function decode(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, self::PREFIX)) {
            $encoded = substr($value, strlen(self::PREFIX));

            return rawurldecode($encoded);
        }

        // Régi mentés: entitás-kódolt vagy nyers HTML
        if (str_contains($value, '&lt;') || str_contains($value, '&gt;') || str_contains($value, '&quot;')) {
            return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $value;
    }

    public static function isRichParam(array $param): bool
    {
        $type = (string) ($param['type'] ?? '');

        return $type === 'rich' || $type === 'textarea';
    }

    /**
     * Sérült ts-features szekciók helyreállítása (data-col attribútumok szétestek).
     */
    public static function repairFeaturesSections(string $html): string
    {
        if ($html === '' || ! str_contains($html, 'ts-features')) {
            return $html;
        }

        // Sérült jel: attribútum közepén új data-col, vagy textként megjelenő data-col2=
        $looksBroken = str_contains($html, 'data-col1=" data-col2=')
            || str_contains($html, 'data-col1="&lt;h3 data-col1=')
            || preg_match('/ts-features[^>]*>[^<]*data-col\d=/i', $html);

        if (! $looksBroken) {
            // Akkor is kódoljuk át a nyers HTML-t tartalmazó data-col* attribútumokat,
            // és ha a kártyák belsejében kódolt html:… szöveg van, dekódoljuk.
            return self::syncFeatureArticleBodies(self::reencodeFeatureAttrs($html));
        }

        return (string) preg_replace_callback(
            '/<section\b(?=[^>]*\bts-features\b)[^>]*>.*?<\/section>/is',
            static function (array $m): string {
                $chunk = $m[0];

                $title = 'Miért nálunk?';
                if (preg_match('/data-title=(["\'])(.*?)\1/i', $chunk, $tm)) {
                    $title = html_entity_decode($tm[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                } elseif (preg_match('/data-ts-text="title"[^>]*>(.*?)<\//is', $chunk, $tm)) {
                    $title = trim(strip_tags($tm[1]));
                }

                $cols = [
                    1 => '<h3>Csendes környezet</h3><p>Természetközeli pihenés, távol a város zajától.</p>',
                    2 => '<h3>Online foglalás</h3><p>Nézze meg a szabad időpontokat, és foglaljon pár kattintással.</p>',
                    3 => '<h3>Vendégközpontú</h3><p>Rugalmas fogadás, személyes odafigyelés minden tartózkodásnál.</p>',
                ];

                foreach ([1, 2, 3] as $i) {
                    if (preg_match('/<article[^>]*data-ts-text="col'.$i.'"[^>]*>(.*?)<\/article>/is', $chunk, $am)) {
                        $inner = trim($am[1]);
                        if ($inner !== '') {
                            $cols[$i] = $inner;
                        }
                    }
                }

                // Ha col1 üres, próbáljuk a sérült tag utáni szöveget
                if (trim(strip_tags($cols[1])) === '' || ! preg_match('/<article[^>]*data-ts-text="col1"[^>]*>\s*\S/is', $chunk)) {
                    if (preg_match('/class="ts-features"[^>]*>(.*?)(?:<div class="ts-features__inner"|data-col2=)/is', $chunk, $leak)) {
                        $leaked = trim($leak[1]);
                        $leaked = preg_replace('/^["\s]+|["\s]+$/u', '', $leaked) ?? $leaked;
                        $leaked = preg_replace('/\s*data-col\d="[^"]*"/i', '', $leaked) ?? $leaked;
                        if ($leaked !== '' && ! str_contains($leaked, 'ts-features__')) {
                            if (! str_contains($leaked, '<')) {
                                $leaked = '<h3>'.e($leaked).'</h3>';
                            }
                            $cols[1] = $leaked;
                        }
                    }
                    if (preg_match('/>(Csendes környezet)<p>(.*?)<\/p>/iu', $chunk, $cm)) {
                        $cols[1] = '<h3>'.$cm[1].'</h3><p>'.$cm[2].'</p>';
                    }
                }

                return self::buildFeaturesSection($title, $cols);
            },
            $html
        ) ?? self::reencodeFeatureAttrs($html);
    }

    /**
     * @param  array{1?: string, 2?: string, 3?: string}  $cols
     */
    public static function buildFeaturesSection(string $title, array $cols): string
    {
        $col1 = $cols[1] ?? '';
        $col2 = $cols[2] ?? '';
        $col3 = $cols[3] ?? '';

        return '<section class="ts-features" data-gjs-type="ts-features" data-gjs-name="Előnyök"'
            .' data-title="'.e($title).'"'
            .' data-col1="'.e(self::encode($col1)).'"'
            .' data-col2="'.e(self::encode($col2)).'"'
            .' data-col3="'.e(self::encode($col3)).'">'
            .'<div class="ts-features__inner">'
            .'<h2 class="ts-features__title" data-ts-text="title">'.$title.'</h2>'
            .'<div class="ts-features__grid">'
            .'<article class="ts-feature" data-ts-text="col1">'.$col1.'</article>'
            .'<article class="ts-feature" data-ts-text="col2">'.$col2.'</article>'
            .'<article class="ts-feature" data-ts-text="col3">'.$col3.'</article>'
            .'</div></div></section>';
    }

    protected static function reencodeFeatureAttrs(string $html): string
    {
        return (string) preg_replace_callback(
            '/<section\b(?=[^>]*\bts-features\b)([^>]*)>/i',
            static function (array $m): string {
                $attrs = (string) preg_replace_callback(
                    '/\s(data-col[123]|data-title)=(["\'])(.*?)\2/is',
                    static function (array $am): string {
                        $name = $am[1];
                        $raw = html_entity_decode($am[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if ($name === 'data-title' && ! str_contains($raw, '<')) {
                            return ' '.$name.'="'.e($raw).'"';
                        }

                        return ' '.$name.'="'.e(self::encode(self::decode($raw))).'"';
                    },
                    $m[1]
                );

                return '<section'.$attrs.'>';
            },
            $html
        );
    }

    /**
     * Ha a kártyák belsejébe a kódolt data-col* érték került szövegként, visszaállítjuk HTML-re.
     */
    protected static function syncFeatureArticleBodies(string $html): string
    {
        return (string) preg_replace_callback(
            '/<section\b(?=[^>]*\bts-features\b)([^>]*)>(.*?)<\/section>/is',
            static function (array $m): string {
                $attrs = $m[1];
                $body = $m[2];

                foreach ([1, 2, 3] as $i) {
                    $fromAttr = null;
                    if (preg_match('/\sdata-col'.$i.'=(["\'])(.*?)\1/is', $attrs, $am)) {
                        $fromAttr = self::decode(html_entity_decode($am[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    }

                    $body = (string) preg_replace_callback(
                        '/(<article\b[^>]*\bdata-ts-text="col'.$i.'"[^>]*>)(.*?)(<\/article>)/is',
                        static function (array $am) use ($fromAttr): string {
                            $rawInner = html_entity_decode(trim($am[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $inner = $rawInner;

                            if (str_starts_with($rawInner, self::PREFIX)) {
                                $inner = $fromAttr ?? self::decode($rawInner);
                            } elseif ($fromAttr !== null && $fromAttr !== '' && ! str_contains($rawInner, '<') && str_contains($fromAttr, '<')) {
                                // Üres / csak szöveges kártya, de az attr-ban van HTML → attr a forrás
                                $inner = $fromAttr;
                            }

                            return $am[1].$inner.$am[3];
                        },
                        $body
                    );
                }

                return '<section'.$attrs.'>'.$body.'</section>';
            },
            $html
        );
    }
}
