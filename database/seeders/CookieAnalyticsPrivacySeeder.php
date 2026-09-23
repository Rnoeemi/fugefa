<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

/**
 * Frissíti az adatkezelési tájékoztató süti / analitika szakaszát.
 * Nem fut automatikusan a DatabaseSeederből – külön hívd:
 *
 *   php artisan db:seed --class=CookieAnalyticsPrivacySeeder --force
 */
class CookieAnalyticsPrivacySeeder extends Seeder
{
    private const MARKER = '<!-- cookie-analytics-privacy:v1 -->';

    public function run(): void
    {
        $page = SitePage::query()->where('slug', 'adatkezelesi-tajekoztato')->first();

        if (! $page) {
            $this->command?->warn('Nincs adatkezelesi-tajekoztato oldal – seeder kihagyva.');

            return;
        }

        $html = (string) ($page->html ?? '');

        if (str_contains($html, self::MARKER)) {
            $this->command?->info('Süti/analitika szakasz már frissítve – semmi teendő.');

            return;
        }

        $section = $this->cookieSectionHtml();

        // Látható HTML: meglévő „Sütik” szakasz cseréje, különben hozzáfűzés.
        if (preg_match('/<h3>\s*Sütik\s*<\/h3>.*?(?=<h3>|$)/is', $html)) {
            $html = preg_replace(
                '/<h3>\s*Sütik\s*<\/h3>.*?(?=<h3>|$)/is',
                $section,
                $html,
                1
            ) ?? ($html."\n".$section);
        } else {
            $html = rtrim($html)."\n".$section;
        }

        $page->html = $html;
        $page->save();

        $this->command?->info('Adatkezelési tájékoztató süti/analitika szakasza frissítve.');
    }

    protected function cookieSectionHtml(): string
    {
        return <<<'HTML'
<!-- cookie-analytics-privacy:v1 -->
<h3>Sütik és webanalitika</h3>
<p>A weboldal a működéshez <strong>szükséges</strong> sütiket használ (pl. munkamenet, biztonság, CSRF-védelem). Ezekhez nem kérünk külön hozzájárulást.</p>
<p>A <strong>statisztikai</strong> sütiket (Google Analytics 4 / Google Tag Manager) csak akkor töltjük be, ha a süti bannerben Ön az <em>Elfogadom</em> gombra kattint. Elutasítás esetén analitikai script nem fut, mérési süti nem kerül beállításra.</p>
<p>A hozzájárulását bármikor módosíthatja a „Süti beállítások” gombbal. A döntést a böngésző localStorage-jában tároljuk.</p>
<p><strong>Kapcsolati űrlap:</strong> a spam elleni védelemhez Google reCAPTCHA v2 használható; a szolgáltató a Google Ireland Limited / Google LLC.</p>
<p>Panasz esetén a <a href="https://www.naih.hu" rel="noopener noreferrer" target="_blank">Nemzeti Adatvédelmi és Információszabadság Hatósághoz (NAIH)</a> fordulhat.</p>
HTML;
    }
}
