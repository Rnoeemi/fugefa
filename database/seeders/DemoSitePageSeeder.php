<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

class DemoSitePageSeeder extends Seeder
{
    public function run(): void
    {
        SitePage::query()->updateOrCreate(
            ['slug' => 'kezdo'],
            [
                'title' => 'Kezdőlap',
                'html' => <<<'HTML'
<section style="padding:7rem 1.5rem 4rem;background:#0f2920;color:#fff;font-family:Karla,sans-serif;">
  <div style="max-width:72rem;margin:0 auto;">
    <p style="letter-spacing:.18em;text-transform:uppercase;opacity:.55;font-size:.75rem;">Tüsiszállás</p>
    <h1 style="font-family:Literata,Georgia,serif;font-size:clamp(2.5rem,5vw,4rem);margin:1rem 0;">Pihenés a természet közelében</h1>
    <p style="max-width:36rem;opacity:.8;line-height:1.6;">Szerkeszd szabadon a webhelyet a Filament Webhely szerkesztőben – drag & drop, szöveg, képek, layout.</p>
    <a href="/szallasok" style="display:inline-block;margin-top:1.75rem;background:#8d6b3e;color:#fff;padding:.85rem 1.4rem;text-decoration:none;font-size:.75rem;letter-spacing:.14em;text-transform:uppercase;font-weight:600;">Szállások</a>
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
