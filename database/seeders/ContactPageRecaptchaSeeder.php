<?php

namespace Database\Seeders;

use App\Models\SitePage;
use App\Support\GrapesJs\SiteBlockCatalog;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Biztosítja, hogy a Kapcsolat oldal élő (dinamikus) kapcsolati űrlapot tartalmazzon,
 * amiben megjelenik a reCAPTCHA.
 *
 *   php artisan db:seed --class=ContactPageRecaptchaSeeder --force
 */
class ContactPageRecaptchaSeeder extends Seeder
{
    public function run(): void
    {
        $page = SitePage::query()->where('slug', 'kapcsolat')->first();

        if (! $page) {
            $this->command?->warn('Nincs kapcsolat oldal (slug=kapcsolat).');

            return;
        }

        $html = (string) ($page->html ?? '');

        if (str_contains($html, 'data-ts-dynamic="contact-form"')
            || str_contains($html, "data-ts-dynamic='contact-form'")) {
            $this->command?->info('A kapcsolat oldal már tartalmaz dinamikus űrlapot – semmi teendő.');

            return;
        }

        $block = collect(SiteBlockCatalog::definitionsFor('page', 'fugefa'))
            ->firstWhere('id', 'ts-contact-form');

        if (! is_array($block) || blank($block['content'] ?? null)) {
            throw new RuntimeException('Az oldalépítőben nem található a ts-contact-form blokk.');
        }

        $content = (string) $block['content'];
        $blockHtml = trim((string) preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $content));

        // Meglévő tartalom mögé tesszük az élő űrlapot (ne töröljük a szöveget).
        $page->html = rtrim($html)."\n".$blockHtml;
        $page->save();

        $this->command?->info('Dinamikus kapcsolati űrlap hozzáadva a kapcsolat oldalhoz.');
    }
}
