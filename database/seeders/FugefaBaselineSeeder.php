<?php

namespace Database\Seeders;

use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Support\GrapesJs\SiteLayoutDefaults;
use Illuminate\Database\Seeder;

/**
 * Üres kiinduló állapot: alap fejléc/lábléc sablon + minimális kezdőlap.
 * Az oldaltartalmat az admin Webhely szerkesztőben kell felépíteni.
 */
class FugefaBaselineSeeder extends Seeder
{
    public function run(): void
    {
        $brand = 'Fügefa építésziroda';
        $settings = SiteSetting::current();

        $settings->update([
            'header_html' => SiteLayoutDefaults::headerHtml($brand),
            'footer_html' => SiteLayoutDefaults::footerHtml(
                brandName: $brand,
                footerText: 'Innovatív építészeti megoldásokkal, precíz tervezéssel és megbízható szakmai háttérrel támogatjuk ügyfeleinket a koncepciótól a megvalósításig.',
            ),
            'header_css' => SiteLayoutDefaults::headerCss(),
            'footer_css' => SiteLayoutDefaults::footerCss(),
        ]);

        SitePage::query()->updateOrCreate(
            ['slug' => 'kezdo'],
            [
                'title' => 'Kezdőlap',
                'html' => <<<'HTML'
<section data-gjs-type="ts-text" data-layout="center" data-title="Üdvözöljük" data-show-title="1" data-body="html:Szerkeszd%20a%20kezdőlapot%20a%20Filament%20Webhely%20szerkesztőben.%20Húzd%20be%20a%20blokkokat%2C%20állítsd%20be%20a%20fejlécet%20és%20a%20láblécet." class="ts-text ts-text--center">
  <div class="ts-text__inner">
    <h1 data-ts-text="title">Üdvözöljük</h1>
    <div class="ts-text__body" data-ts-text="body"><p>Szerkeszd a kezdőlapot a Filament Webhely szerkesztőben. Húzd be a blokkokat, állítsd be a fejlécet és a láblécet.</p></div>
  </div>
</section>
HTML,
                'css' => '',
                'is_published' => true,
                'is_homepage' => true,
                'sort_order' => 0,
            ],
        );
    }
}
