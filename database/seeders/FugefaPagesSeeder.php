<?php

namespace Database\Seeders;

use App\Enums\SiteModule;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Support\GrapesJs\SiteBlockCatalog;
use App\Support\GrapesJs\SiteRichAttr;
use App\Support\SiteBuilderCss;
use Illuminate\Database\Seeder;
use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;
use NoteBrainsLab\FilamentMenuManager\Models\MenuLocation;
use RuntimeException;

/**
 * Oldalak összerakása az oldalépítő gyári blokkjaiból (mintha a vizuális szerkesztőben húznánk be őket).
 */
class FugefaPagesSeeder extends Seeder
{
    private const FOOTER_LEAD = 'Innovatív építészeti megoldásokkal, precíz tervezéssel és megbízható szakmai háttérrel támogatjuk ügyfeleinket a koncepciótól a megvalósításig.';
    public function run(): void
    {
        $this->enableAppointmentModule();

        config(['site-builder.website' => 'fugefa']);
        SiteBlockCatalog::flush();

        $home = $this->upsertPage(
            slug: 'kezdo',
            title: 'Főoldal',
            blocks: [
                $this->droppedBlock('ts-hero', [
                    'layout' => 'center',
                    'title' => SiteSetting::current()->site_name ?: 'Fügefa építésziroda',
                    'show_eyebrow' => '0',
                    'show_lead' => '0',
                    'show_primary' => '0',
                    'show_secondary' => '0',
                ]),
            ],
            isHomepage: true,
            sortOrder: 0,
        );

        $works = $this->upsertPage(
            slug: 'munkaink',
            title: 'Munkáink',
            blocks: [
                $this->droppedBlock('ts-ba-gallery', [
                    'title' => 'Munkáink',
                ]),
            ],
            sortOrder: 10,
        );

        $office = $this->upsertPage(
            slug: 'iroda',
            title: 'Iroda',
            blocks: [
                $this->droppedBlock('ts-features', [
                    'title' => 'Munkatársak',
                ]),
            ],
            sortOrder: 20,
        );

        $contact = $this->upsertPage(
            slug: 'kapcsolat',
            title: 'Kapcsolat',
            blocks: [
                $this->droppedBlock('ts-contact-form'),
            ],
            sortOrder: 30,
        );

        $booking = $this->upsertPage(
            slug: 'idopontfoglalas',
            title: 'Időpontfoglalás',
            blocks: [
                $this->droppedBlock('ts-appointment-booking'),
            ],
            sortOrder: 40,
        );

        $this->syncPrimaryMenu([
            ['page' => $home, 'label' => 'Főoldal'],
            ['page' => $works, 'label' => 'Munkáink'],
            ['page' => $office, 'label' => 'Iroda'],
            ['page' => $contact, 'label' => 'Kapcsolat'],
            ['page' => $booking, 'label' => 'Időpontfoglalás'],
        ]);

        $this->seedLegalAndFooter();
    }

    /**
     * Impresszum / adatkezelés / pályázat + lábléc. A főmenübe nem kerülnek be.
     * Élő adatbázison önállóan is hívható, a meglévő tartalmi oldalak felülírása nélkül.
     */
    public function seedLegalAndFooter(): void
    {
        config(['site-builder.website' => 'fugefa']);
        SiteBlockCatalog::flush();

        $this->seedLegalPages();
        $this->syncFooter();
    }

    /**
     * Csak a három jogi oldal; a láblécet és a weboldal beállításokat nem módosítja.
     */
    public function seedLegalPages(): void
    {
        config(['site-builder.website' => 'fugefa']);
        SiteBlockCatalog::flush();

        $this->upsertPage(
            slug: 'impresszum',
            title: 'Impresszum',
            blocks: [
                $this->droppedBlock('ts-legal', [
                    'title' => 'Impresszum',
                    'body' => $this->impresszumBody(),
                ]),
            ],
            sortOrder: 100,
        );

        $this->upsertPage(
            slug: 'adatkezelesi-tajekoztato',
            title: 'Adatkezelési tájékoztató',
            blocks: [
                $this->droppedBlock('ts-legal', [
                    'title' => 'Adatkezelési tájékoztató',
                    'body' => $this->privacyBody(),
                ]),
            ],
            sortOrder: 110,
        );

        $this->seedGrantPage();
    }

    /**
     * Csak a pályázati oldal; a többi jogi oldalt és a láblécet nem módosítja.
     */
    public function seedGrantPage(): void
    {
        config(['site-builder.website' => 'fugefa']);
        SiteBlockCatalog::flush();

        $this->upsertPage(
            slug: 'palyazat',
            title: 'Pályázat',
            blocks: [
                $this->droppedBlock('ts-legal', [
                    'title' => 'Pályázat',
                    'body' => $this->grantBody(),
                ]),
            ],
            sortOrder: 120,
        );
    }

    protected function enableAppointmentModule(): void
    {
        $settings = SiteSetting::current();
        $modules = is_array($settings->modules) ? $settings->modules : SiteModule::defaultStates();
        $modules[SiteModule::Appointment->value] = true;

        $settings->update(['modules' => $modules]);
    }

    public function syncFooter(): void
    {
        $settings = SiteSetting::current();
        $brand = trim((string) ($settings->site_name ?: 'Fügefa építésziroda'));
        $brandEsc = e($brand);
        $text = self::FOOTER_LEAD;
        $textEsc = e($text);
        $textAttr = e(SiteRichAttr::encode($text));
        $logo = $this->footerLogoUrl($settings);

        $html = <<<HTML
<footer class="ts-footer" data-gjs-type="ts-footer-full" data-gjs-name="Lábléc" data-layout="cols-2" data-use-site-contact="0" data-brand="{$brandEsc}" data-text="{$textAttr}" data-show-text="1" data-logo-height="40" data-logo-max-width="180" data-menu-breakpoint="phone">
  <div class="ts-footer__grid">
    <div>
      <a class="ts-footer__brand-link" href="/" aria-label="{$brandEsc}">
        <img class="ts-footer__logo" src="{$logo}" alt="{$brandEsc}">
      </a>
      <p class="ts-footer__brand" data-ts-text="brand">{$brandEsc}</p>
      <p class="ts-footer__text" data-ts-text="text">{$textEsc}</p>
    </div>
    <div>
      <p class="ts-footer__label">Oldalak</p>
      <ul class="ts-footer__list">
        <li><a href="/">Főoldal</a></li>
        <li><a href="/oldal/munkaink">Munkáink</a></li>
        <li><a href="/oldal/iroda">Iroda</a></li>
        <li><a href="/oldal/kapcsolat">Kapcsolat</a></li>
        <li><a href="/oldal/idopontfoglalas">Időpontfoglalás</a></li>
      </ul>
    </div>
    <div>
      <p class="ts-footer__label">Jogi</p>
      <ul class="ts-footer__list">
        <li><a href="/oldal/impresszum">Impresszum</a></li>
        <li><a href="/oldal/adatkezelesi-tajekoztato">Adatkezelési tájékoztató</a></li>
        <li><a href="/oldal/palyazat">Pályázat</a></li>
      </ul>
    </div>
  </div>
</footer>
HTML;

        $html .= $this->footerGrantAndCreditsHtml();

        $settings->update([
            'footer_html' => $html,
            'footer_css' => SiteBuilderCss::sanitize($this->footerCss()),
            'footer_grapes_data' => null,
            'footer_text' => $text,
            'font_sans' => 'Nunito Sans',
            'font_display' => 'Jost',
        ]);
    }

    /**
     * Pályázati szöveg + logók + „Készítette” vissza a meglévő lábléc HTML mögé.
     * A lábléc többi tartalmát (márka, menü, jogi) nem írja felül.
     */
    public function restoreFooterGrantAndCredits(): void
    {
        $settings = SiteSetting::current();
        $html = (string) $settings->footer_html;

        if (! str_contains($html, 'dsp-logo-feher-rgb-transparent')) {
            $html = preg_replace('/<\/footer>/i', '</footer>', $html, 1) ?? $html;
            $html .= $this->footerGrantAndCreditsHtml();
        }

        $css = (string) ($settings->footer_css ?? '');
        $grantCss = $this->footerGrantLogoCss();
        if (! str_contains($css, 'footer-grant-logos')) {
            $css = trim($css."\n".$grantCss);
        }

        $settings->update([
            'footer_html' => $html,
            'footer_css' => SiteBuilderCss::sanitize($css),
            'footer_grapes_data' => null,
        ]);
    }

    protected function footerLogoUrl(SiteSetting $settings): string
    {
        $fallback = '/images/site/eszkoz-3-100.jpg';
        $header = (string) ($settings->header_html ?? '');
        if ($header !== '' && preg_match('/\bdata-logo-url=(["\'])([^"\']+)\1/i', $header, $match)) {
            $url = trim((string) $match[2]);
            if ($url !== '') {
                return $url;
            }
        }

        return $fallback;
    }

    protected function footerGrantAndCreditsHtml(): string
    {
        $grant = 'A weboldal a Demján Sándor Program keretében és támogatásával valósult meg.';
        $credits = 'Készítette: Auri Consulting Tanácsadó KFT., WHATTHEBRAND Studio KFT. és PROMERA MENEDZSMENT KFT.';
        $grantAttr = e(SiteRichAttr::encode($grant));
        $creditsAttr = e(SiteRichAttr::encode($credits));
        $grantEsc = e($grant);
        $creditsEsc = e($credits);
        $dsp = '/images/site/dsp-logo-feher-rgb-transparent.png';
        $neum = '/images/site/neum-logo-feher-rgb-transparent2.png';

        return <<<HTML
<section class="ts-layout ts-layout--3col" data-gjs-type="ts-layout-3col" data-gjs-name="3 oszlop" data-layout="equal" data-section-width="content">
  <div class="ts-layout__inner ts-layout__inner--3col">
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="1. oszlop">
      <section class="ts-text" data-gjs-type="ts-text" data-gjs-name="Szöveg" data-layout="left" data-title="Pályázat" data-show-title="0" data-body="{$grantAttr}" data-show-body="1">
        <div class="ts-text__inner">
          <h2 data-ts-text="title">Pályázat</h2>
          <p data-ts-text="body">{$grantEsc}</p>
        </div>
      </section>
    </div>
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="2. oszlop">
      <figure class="ts-image" data-gjs-type="ts-image" data-gjs-name="Kép" data-media-url="{$dsp}" data-alt="Demján Sándor Program" data-href="/oldal/palyazat">
        <a class="ts-image__link" data-ts-href="href" href="/oldal/palyazat">
          <img data-ts-bg-image src="{$dsp}" alt="Demján Sándor Program" loading="lazy">
        </a>
      </figure>
    </div>
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="3. oszlop">
      <figure class="ts-image" data-gjs-type="ts-image" data-gjs-name="Kép" data-media-url="{$neum}" data-alt="Neumann Nonprofit Közhasznú Kft." data-href="/oldal/palyazat">
        <a class="ts-image__link" data-ts-href="href" href="/oldal/palyazat">
          <img data-ts-bg-image src="{$neum}" alt="Neumann Nonprofit Közhasznú Kft." loading="lazy">
        </a>
      </figure>
    </div>
  </div>
</section>
<section class="ts-layout" data-gjs-type="ts-layout-1col" data-gjs-name="1 oszlop" data-layout="boxed" data-section-width="content">
  <div class="ts-layout__inner">
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="Oszlop">
      <section class="ts-text footer" data-gjs-type="ts-text" data-gjs-name="Szöveg" data-layout="left" data-title="Készítette" data-show-title="0" data-body="{$creditsAttr}" data-show-body="1" data-extra-class="footer">
        <div class="ts-text__inner">
          <h2 data-ts-text="title">Készítette</h2>
          <p data-ts-text="body">{$creditsEsc}</p>
        </div>
      </section>
    </div>
  </div>
</section>
HTML;
    }

    protected function footerCss(): string
    {
        $template = '';
        $path = resource_path('site-builder/templates/footer-full.html');
        if (is_file($path)) {
            $html = (string) file_get_contents($path);
            if (preg_match_all('/<style\b[^>]*>(.*?)<\/style>/si', $html, $matches) !== false) {
                $template = implode("\n", array_map('trim', $matches[1]));
            }
        }

        $extras = <<<'CSS'
.ts-footer__logo {
  display: block;
  height: 2.75rem;
  width: auto;
  max-width: 12rem;
  object-fit: contain;
  margin: 0 0 .85rem;
  background: #fff;
  padding: .4rem .65rem;
  border-radius: var(--radius-sm, .4rem);
}
.ts-footer__brand-link {
  display: inline-block;
  text-decoration: none;
  color: inherit;
}
.ts-footer__brand-link:focus-visible {
  outline: 2px solid #fff;
  outline-offset: 3px;
}
.ts-footer__brand {
  margin: 0 0 .5rem;
}
.ts-footer__list a:hover {
  color: #fff;
  text-decoration: underline;
}
.ts-footer__list a:focus-visible {
  outline: 2px solid #fff;
  outline-offset: 2px;
}
@media (max-width: 1023px) {
  .ts-footer__logo {
    height: 2.4rem;
    max-width: 10rem;
  }
}
@media (max-width: 767px) {
  .ts-footer__logo {
    height: 2.15rem;
    margin-bottom: .65rem;
  }
  .ts-footer__brand {
    font-size: 1.45rem;
  }
}
CSS;

        return trim($template."\n".$extras."\n".$this->footerGrantLogoCss());
    }

    protected function footerGrantLogoCss(): string
    {
        return <<<'CSS'
/* footer-grant-logos */
body.site-shell .ts-footer ~ .ts-layout .ts-layout__inner--3col {
  align-items: center !important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-image img {
  background: transparent !important;
  padding: 0 !important;
  max-height: 4.5rem !important;
}
body.site-shell .ts-footer ~ .ts-layout .ts-image__link:focus-visible {
  outline: 2px solid #fff;
  outline-offset: 3px;
}
CSS;
    }

    protected function impresszumBody(): string
    {
        return <<<'HTML'
<h3>Szolgáltató</h3>
<p><strong>Név:</strong> Maksai-Szamosi Ildikó</p>
<p><strong>Székhely:</strong> HU 7625 Pécs, Felsőhavi utca 26. 3.</p>
<p><strong>Adószám:</strong> 55637398-1-22</p>
<p><strong>E-mail:</strong> <a href="mailto:maksaiszildiko@gmail.com">maksaiszildiko@gmail.com</a></p>
<p><strong>Telefon:</strong> <a href="tel:+36202503121">+36 20 250 3121</a></p>
<h3>Tárhelyszolgáltató</h3>
<p><strong>Név:</strong> Romix Webműhely Kft.</p>
<p><strong>Telephely:</strong> 7020 Dunaföldvár, Dézsma sor 17.</p>
<p><strong>Adószám:</strong> 25824641-2-17</p>
<p><strong>EU adószám:</strong> HU25824641</p>
<p><strong>E-mail:</strong> <a href="mailto:info@romix.hu">info@romix.hu</a></p>
<p><strong>Telefon:</strong> <a href="tel:+36302158796">+36 30 215-8796</a></p>
HTML;
    }

    protected function privacyBody(): string
    {
        return <<<'HTML'
<h3>Adatkezelő</h3>
<p><strong>Név:</strong> Maksai-Szamosi Ildikó</p>
<p><strong>Székhely:</strong> HU 7625 Pécs, Felsőhavi utca 26. 3.</p>
<p><strong>Adószám:</strong> 55637398-1-22</p>
<p><strong>E-mail:</strong> <a href="mailto:maksaiszildiko@gmail.com">maksaiszildiko@gmail.com</a></p>
<p><strong>Telefon:</strong> <a href="tel:+36202503121">+36 20 250 3121</a></p>
<h3>Milyen adatokat kezelünk</h3>
<p>A weboldalon keresztül megadott kapcsolatfelvételi és időpontfoglalási adatokat (név, e-mail, telefon, üzenet, foglalás részletei) a megkeresés megválaszolásához és az időpont egyeztetéséhez kezeljük.</p>
<h3>Jogalap és megőrzés</h3>
<p>Az adatokat a kapcsolattartáshoz, az időpontfoglaláshoz és a jogszabályban előírt kötelezettségek teljesítéséhez szükséges ideig őrizzük.</p>
<h3>Sütik</h3>
<p>A weboldal a működéshez szükséges sütiket használhat. A böngésző beállításaiban a sütik kezelése korlátozható.</p>
<h3>Érintetti jogok</h3>
<p>Az érintett kérheti adataihoz a hozzáférést, azok helyesbítését, törlését vagy a kezelés korlátozását, valamint tiltakozhat a kezelés ellen a fenti elérhetőségeken. Panasszal a Nemzeti Adatvédelmi és Információszabadság Hatósághoz (NAIH) fordulhat.</p>
<h3>Tárhelyszolgáltató</h3>
<p>A weboldal tárhelyszolgáltatója a Romix Webműhely Kft. (7020 Dunaföldvár, Dézsma sor 17., adószám: 25824641-2-17, e-mail: <a href="mailto:info@romix.hu">info@romix.hu</a>).</p>
HTML;
    }

    protected function grantBody(): string
    {
        return <<<'HTML'
<p>A weboldal a Demján Sándor Program keretében és támogatásával valósult meg. A Neumann Nonprofit Közhasznú Kft. döntése alapján vállalkozásunk támogatást nyert az online jelenlét fejlesztésére.</p>
<p><strong>Kedvezményezett neve:</strong> Maksai-Szamosi Ildikó</p>
<p><strong>Székhely:</strong> HU 7625 Pécs, Felsőhavi utca 26. 3.</p>
<p><strong>Támogatói okirat iktatószáma:</strong> 2025/KKV/6859</p>
<p><strong>Projekt címe:</strong> Minden vállalkozásnak legyen saját honlapja</p>
<p><strong>A szerződött támogatás összege:</strong> 1 500 000 Ft</p>
<p><strong>Intenzitás:</strong> 100%</p>
<p><strong>A projekt tartalma:</strong> Alap csomag szerinti online szolgáltatások igénybevétele.</p>
<p><strong>A projekt tervezett befejezési dátuma:</strong> A támogatott tevékenység a kibocsátástól számított 24. hónap végéig tart.</p>
<p>A program a Nemzetgazdasági Minisztérium felügyeletével valósul meg, elősegítve a hazai mikro- és kisvállalkozások versenyképességét a digitális térben.</p>
HTML;
    }

    /**
     * @param  list<array{html: string, css: string}>  $blocks
     */
    protected function upsertPage(
        string $slug,
        string $title,
        array $blocks,
        int $sortOrder,
        bool $isHomepage = false,
    ): SitePage {
        $html = implode("\n", array_column($blocks, 'html'));
        $css = SiteBuilderCss::sanitize(implode("\n\n", array_filter(array_column($blocks, 'css'))));

        return SitePage::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'html' => $html,
                'css' => $css,
                'grapes_data' => null,
                'is_published' => true,
                'is_homepage' => $isHomepage,
                'sort_order' => $sortOrder,
            ],
        );
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array{html: string, css: string}
     */
    protected function droppedBlock(string $id, array $overrides = []): array
    {
        $block = collect(SiteBlockCatalog::definitionsFor('page', 'fugefa'))
            ->firstWhere('id', $id);

        if (! is_array($block) || blank($block['content'] ?? null)) {
            throw new RuntimeException("Az oldalépítőben nem található a(z) {$id} blokk.");
        }

        $content = (string) $block['content'];
        $cssChunks = [];

        if (preg_match_all('/<style\b[^>]*>(.*?)<\/style>/si', $content, $matches) !== false) {
            foreach ($matches[1] as $chunk) {
                $cssChunks[] = trim((string) $chunk);
            }
        }

        $html = trim((string) preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $content));
        $html = $this->applyOverrides($html, $overrides);

        return [
            'html' => $html,
            'css' => implode("\n\n", array_filter($cssChunks)),
        ];
    }

    /**
     * @param  array<string, string>  $overrides
     */
    protected function applyOverrides(string $html, array $overrides): string
    {
        if ($overrides === []) {
            return $html;
        }

        $html = (string) preg_replace_callback(
            '/^(<[^>]+)>/s',
            function (array $match) use ($overrides): string {
                $tag = $match[1];

                foreach ($overrides as $key => $value) {
                    $attr = 'data-'.str_replace('_', '-', $key);
                    $attrValue = str_contains($value, '<') ? SiteRichAttr::encode($value) : $value;
                    $pattern = '/\s'.preg_quote($attr, '/').'\s*=\s*(["\'])(.*?)\\1/';

                    if (preg_match($pattern, $tag)) {
                        $tag = (string) preg_replace($pattern, ' '.$attr.'="'.e($attrValue).'"', $tag, 1);
                    } else {
                        $tag .= ' '.$attr.'="'.e($attrValue).'"';
                    }
                }

                return $tag.'>';
            },
            ltrim($html),
            1,
        );

        if (isset($overrides['title'])) {
            $title = e($overrides['title']);
            $html = (string) preg_replace_callback(
                '/(<(h1|h2)[^>]*data-ts-text="title"[^>]*>)(.*?)(<\/\2>)/is',
                static fn (array $match): string => $match[1].$title.$match[4],
                $html,
                1,
            );
        }

        if (isset($overrides['body'])) {
            $html = (string) preg_replace(
                '/(<div\b[^>]*data-ts-text="body"[^>]*>).*?(<\/div>)/is',
                '$1'.$overrides['body'].'$2',
                $html,
                1,
            );
        }

        return $html;
    }

    /**
     * @param  list<array{page: SitePage, label: string}>  $items
     */
    protected function syncPrimaryMenu(array $items): void
    {
        $location = MenuLocation::query()->firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Webhely'],
        );

        $menu = Menu::query()->firstOrCreate(
            ['menu_location_id' => $location->id],
            [
                'name' => 'Főmenü',
                'is_active' => true,
            ],
        );

        $menu->update(['is_active' => true]);

        MenuItem::query()->where('menu_id', $menu->id)->delete();

        foreach ($items as $index => $item) {
            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'parent_id' => null,
                'title' => $item['label'],
                'url' => $item['page']->getMenuUrl(),
                'target' => '_self',
                'icon' => $item['page']->getMenuIcon(),
                'type' => 'model',
                'linkable_type' => SitePage::class,
                'linkable_id' => $item['page']->id,
                'order' => $index,
                'enabled' => true,
            ]);
        }
    }
}
