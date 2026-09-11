<?php

namespace Database\Seeders;

use App\Models\SitePage;
use App\Support\Seo\SitePageSeo;
use Illuminate\Database\Seeder;

/**
 * Oldalankénti meta title / description (Baja és környéke, építészmérnöki szolgáltatás).
 * Csak a seo mezőt írja; a lap HTML-jét nem módosítja.
 */
class FugefaSeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::seoBySlug() as $slug => $seo) {
            $page = SitePage::query()->where('slug', $slug)->first();
            if (! $page) {
                continue;
            }

            $page->update([
                'seo' => SitePageSeo::normalize($seo),
            ]);
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function seoBySlug(): array
    {
        return [
            'kezdo' => [
                'meta_title' => 'Fügefa építésziroda | Építész Baja és környéke',
                'meta_description' => 'Építészmérnöki tervezés, engedélyezési tervek és szakmai tanácsadás Baja és környékén. A Fügefa építésziroda a koncepciótól a megvalósításig támogatja Önt.',
                'meta_keywords' => 'építész Baja, építészmérnök Baja, építésziroda Baja, építészeti tervezés Baja környéke, engedélyezési terv Baja',
                'og_title' => 'Fügefa építésziroda | Építész Baja és környéke',
                'og_description' => 'Építészmérnöki tervezés és tanácsadás Baja és környékén – megbízható szakmai partner a koncepciótól a megvalósításig.',
            ],
            'munkaink' => [
                'meta_title' => 'Munkáink | Építészeti projektek – Fügefa, Baja',
                'meta_description' => 'Válogatás a Fügefa építésziroda Baja és környékén készült terveiből és megvalósult építészeti projektjeiből.',
                'meta_keywords' => 'építészeti tervek Baja, referencia projektek, Fügefa építésziroda munkái',
                'og_title' => 'Munkáink | Építészeti projektek – Fügefa, Baja',
                'og_description' => 'Építészeti tervek és megvalósult projektek Baja és környékéről – Fügefa építésziroda.',
            ],
            'iroda' => [
                'meta_title' => 'Iroda és csapat | Fügefa építésziroda Baja',
                'meta_description' => 'Ismerje meg a Fügefa építésziroda csapatát Baján. Építészmérnöki szakértelem Baja és a Dél-Alföld környékén.',
                'meta_keywords' => 'építésziroda Baja, építészmérnök csapat, Fügefa iroda',
                'og_title' => 'Iroda és csapat | Fügefa építésziroda Baja',
                'og_description' => 'A Fügefa építésziroda csapata Baján – építészmérnöki szakértelem Baja és környékére.',
            ],
            'kapcsolat' => [
                'meta_title' => 'Kapcsolat | Fügefa építésziroda Baja',
                'meta_description' => 'Vegye fel a kapcsolatot a Fügefa építészirodával Baján. Építészmérnöki tervezés és konzultáció Baja és környéke számára.',
                'meta_keywords' => 'építész kapcsolat Baja, építésziroda elérhetőség, konzultáció Baja',
                'og_title' => 'Kapcsolat | Fügefa építésziroda Baja',
                'og_description' => 'Kapcsolat a Fügefa építészirodával Baján – tervezés és konzultáció Baja és környékére.',
            ],
            'szolgaltatasok' => [
                'meta_title' => 'Szolgáltatások | Építészmérnöki tervezés Baja',
                'meta_description' => 'Építészmérnöki szolgáltatások Baja és környékén: tervezés, engedélyezés, műszaki tanácsadás. Fügefa építésziroda.',
                'meta_keywords' => 'építészmérnöki szolgáltatás Baja, építészeti tervezés, engedélyezési terv Baja',
                'og_title' => 'Szolgáltatások | Építészmérnöki tervezés Baja',
                'og_description' => 'Építészmérnöki szolgáltatások Baja és környékén – Fügefa építésziroda.',
            ],
            'idopontfoglalas' => [
                'meta_title' => 'Konzultáció | Fügefa építésziroda Baja',
                'meta_description' => 'Foglaljon személyes építészmérnöki konzultációt a Fügefa irodában Baján. Tervezési egyeztetés Baja és környéke ügyfeleinek.',
                'meta_keywords' => 'építész konzultáció Baja, tervezési egyeztetés, Fügefa időpont',
                'og_title' => 'Konzultáció | Fügefa építésziroda Baja',
                'og_description' => 'Építészmérnöki konzultáció Baján – időpont a Fügefa építészirodánál Baja és környékére.',
            ],
            'impresszum' => [
                'meta_title' => 'Impresszum | Fügefa építésziroda Baja',
                'meta_description' => 'A Fügefa építésziroda impresszuma – építészmérnöki szolgáltatás Baja és környékén.',
                'og_title' => 'Impresszum | Fügefa építésziroda Baja',
                'og_description' => 'A Fügefa építésziroda impresszuma – Baja és környéke.',
            ],
            'adatkezelesi-tajekoztato' => [
                'meta_title' => 'Adatkezelési tájékoztató | Fügefa építésziroda',
                'meta_description' => 'Adatkezelési tájékoztató a Fügefa építésziroda weboldalához – Baja és környéke.',
                'og_title' => 'Adatkezelési tájékoztató | Fügefa építésziroda',
                'og_description' => 'Adatkezelési tájékoztató – Fügefa építésziroda, Baja.',
            ],
            'palyazat' => [
                'meta_title' => 'Pályázat | Fügefa építésziroda Baja',
                'meta_description' => 'Pályázati információk a Fügefa építésziroda oldalán – építészmérnöki iroda Baja és környékén.',
                'og_title' => 'Pályázat | Fügefa építésziroda Baja',
                'og_description' => 'Pályázati információk – Fügefa építésziroda, Baja.',
            ],
        ];
    }
}
