<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Support\GrapesJs\SiteLayoutDefaults;
use App\Support\Seo\SitePageSeo;
use Illuminate\Database\Seeder;

class TusiHomepageBuilderSeeder extends Seeder
{
    public const LOGO = '/images/site/tusi-fologo-web.png';

    public const PHOTO_HERO = '/images/site/hero.jpg';

    public const PHOTO_FAMILY = '/images/site/vendeghaz.jpg';

    public const PHOTO_WORKERS = '/images/site/munkasszallas.jpg';

    public function run(): void
    {
        $this->applyHeaderAndFooter();

        $page = SitePage::query()->where('is_homepage', true)->first()
            ?? SitePage::query()->where('slug', 'fooldal')->first();

        $payload = [
            'title' => 'Főoldal',
            'slug' => $page?->slug ?: 'fooldal',
            'html' => $this->html(),
            'css' => $this->css(),
            'grapes_data' => null,
            'is_published' => true,
            'is_homepage' => true,
            'sort_order' => 0,
            'seo' => SitePageSeo::normalize([
                'meta_title' => 'Tüsiszállás | Családi apartman és munkásszállás Tüsifalun',
                'meta_description' => 'Tüsi Vendégház Tüsifalun: teljes ház családnak, konyhával, kerttel és parkolóval, online foglalás. Munkásszállás hét éjszakától, közös konyhával. Írj vagy foglalj.',
                'meta_keywords' => 'Tüsi Vendégház, családi apartman Tüsifalu, munkásszállás Tüsifalu, szállás Tüsifalu, vendégház online foglalás, Tüsiszállás',
                'robots' => 'index,follow',
                'og_title' => 'Tüsiszállás | Családi apartman és munkásszállás Tüsifalun',
                'og_description' => 'Vendégház akár 8 főnek, konyhával és kerttel – online foglalható. Munkásszállás hét éjszakától, egyeztetéssel. Tüsifalu, parkolás a telken.',
                'og_type' => 'website',
                'og_image' => self::PHOTO_FAMILY,
                'twitter_card' => 'summary_large_image',
                'twitter_image' => self::PHOTO_FAMILY,
            ]),
        ];

        if ($page) {
            $page->forceFill($payload)->save();
        } else {
            SitePage::query()->create($payload);
        }
    }

    protected function applyHeaderAndFooter(): void
    {
        $settings = SiteSetting::current();
        $brand = (string) ($settings->site_name ?: 'Tüsi Szállás');

        $settings->forceFill([
            'header_html' => $this->headerHtml($brand),
            'header_css' => $this->headerCss(),
            'header_grapes_data' => null,
            'footer_html' => $this->footerHtml($settings, $brand),
            'footer_css' => $this->footerCss(),
            'footer_grapes_data' => null,
        ])->save();
    }

    protected function headerHtml(string $brand): string
    {
        $logo = self::LOGO;
        $brandEsc = e($brand);
        $menu = SiteLayoutDefaults::menuPlaceholderHtml();
        $mobileMenu = SiteLayoutDefaults::menuMobilePlaceholderHtml();

        $html = <<<HTML
<header class="site-nav is-solid has-logo" data-site-nav data-gjs-type="ts-header-bar" data-gjs-name="Fejléc" data-layout="standard" data-brand-align="left" data-desktop-menu="visible" data-brand="{$brandEsc}" data-logo-url="{$logo}" data-logo-height="56" data-logo-max-width="200" data-menu-align="right" data-menu-breakpoint="phone" data-mobile-menu-style="dropdown" data-show-topbar="0" data-use-site-contact="1" data-cta-label="Foglalás" data-cta-href="/foglalas-panel" data-show-cta="1" style="--ts-logo-height:56px;--ts-logo-max-width:200px">
  <div class="ts-nav-topbar" data-ts-topbar>
    <div class="ts-nav-topbar__inner">
      <div class="ts-nav-topbar__contact">
        <a class="ts-nav-topbar__item ts-nav-topbar__phone" data-ts-show="topbar_phone" data-ts-text="topbar_phone" data-ts-href="topbar_phone" href="#" data-ts-href-scheme="tel">Telefon</a>
        <a class="ts-nav-topbar__item ts-nav-topbar__email" data-ts-show="topbar_email" data-ts-text="topbar_email" data-ts-href="topbar_email" href="#" data-ts-href-scheme="mailto">E-mail</a>
        <span class="ts-nav-topbar__item ts-nav-topbar__address" data-ts-show="topbar_address" data-ts-text="topbar_address">Cím</span>
      </div>
      <div class="ts-nav-topbar__social"></div>
    </div>
  </div>
  <div class="ts-nav-inner">
    <a class="ts-nav-brand" href="/">
      <img class="ts-nav-logo" data-ts-logo data-ts-src-from="logo_url" src="{$logo}" alt="{$brandEsc}">
      <span class="ts-nav-brand-text" data-ts-text="brand" data-ts-brand-text>{$brandEsc}</span>
    </a>
    <div class="ts-nav-bar">
      <nav class="ts-nav-links" data-ts-menu-slot="primary">
        {$menu}
      </nav>
      <div class="ts-nav-actions">
        <a class="ts-nav-cta ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a>
        <button type="button" class="ts-nav-toggle" data-nav-toggle aria-label="Menü megnyitása" aria-expanded="false">
          <span class="ts-nav-toggle__icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>
  </div>
  <div class="ts-nav-overlay" data-nav-overlay hidden aria-hidden="true"></div>
  <div class="ts-nav-panel" data-nav-panel aria-hidden="true">
    <div class="ts-nav-panel__head">
      <p class="ts-nav-panel__title" data-ts-text="brand">{$brandEsc}</p>
      <button type="button" class="ts-nav-panel__close" data-nav-close aria-label="Menü bezárása">
        <span class="ts-nav-panel__close-icon" aria-hidden="true"></span>
      </button>
    </div>
    <div class="ts-nav-panel__body">
      <nav class="ts-nav-panel__menu" data-ts-menu-slot="primary-mobile">
        {$mobileMenu}
      </nav>
      <div class="ts-nav-panel__foot">
        <a class="ts-nav-cta ts-nav-cta--panel ts-btn ts-btn--primary" data-ts-text="cta_label" data-ts-href="cta_href" href="/foglalas-panel">Foglalás</a>
      </div>
    </div>
  </div>
</header>
HTML;

        return SiteLayoutDefaults::ensureHeaderChrome($html);
    }

    protected function headerCss(): string
    {
        $base = SiteLayoutDefaults::headerCss();
        $template = $this->styleFromTemplate('header-bar.html');

        $tusi = <<<'CSS'
.site-nav.has-logo,
.ts-header-simple.has-logo {
  background: #ffffff;
  color: #3d3d3d;
  border-bottom: 1px solid #ececec;
  position: relative;
  z-index: 40;
}
.site-nav.has-logo .ts-nav-links a,
.site-nav.has-logo .ts-nav-links .nav-link,
.ts-header-simple.has-logo .ts-nav-links a,
.ts-header-simple.has-logo .ts-nav-links .nav-link {
  color: #3d3d3d;
}
.site-nav.has-logo .ts-nav-cta {
  background: #e3007f;
  color: #fff !important;
  text-decoration: none;
  white-space: nowrap;
}
.site-nav.has-logo[data-mobile-menu-style="dropdown"] .ts-nav-panel,
.site-nav.has-logo:not([data-mobile-menu-style]) .ts-nav-panel {
  background: #ffffff;
  color: #3d3d3d;
}
.site-nav.has-logo[data-mobile-menu-style="dropdown"] .ts-nav-panel__menu a,
.site-nav.has-logo[data-mobile-menu-style="dropdown"] .ts-nav-panel__link,
.site-nav.has-logo:not([data-mobile-menu-style]) .ts-nav-panel__menu a,
.site-nav.has-logo:not([data-mobile-menu-style]) .ts-nav-panel__link {
  color: #3d3d3d;
  border-bottom-color: #ececec;
}
.site-nav.has-logo .ts-nav-toggle {
  border-color: color-mix(in srgb, #3d3d3d 35%, transparent);
  color: #3d3d3d;
}
CSS;

        return trim($base."\n".$template."\n".$tusi);
    }

    protected function footerHtml(SiteSetting $settings, string $brand): string
    {
        $brandEsc = e($brand);
        $logo = self::LOGO;
        $year = date('Y');
        $text = trim(strip_tags((string) ($settings->footer_text ?: 'Vendégház és munkásszállás Tüsifalun. A Tüsi Vendégház online foglalható, a munkásszállást érdeklődés után egyeztetjük.')));
        $textEsc = e($text);
        $address = trim((string) ($settings->address ?? ''));
        $phone = trim((string) ($settings->phone ?? ''));
        $email = trim((string) ($settings->email ?? ''));

        $contact = '';
        if ($address !== '') {
            $contact .= '<li>'.e($address).'</li>';
        }
        if ($phone !== '') {
            $tel = preg_replace('/\s+/', '', $phone) ?: $phone;
            $contact .= '<li>Telefon: <a href="tel:'.e($tel).'">'.e($phone).'</a></li>';
        }
        if ($email !== '') {
            $contact .= '<li>E-mail: <a href="mailto:'.e($email).'">'.e($email).'</a></li>';
        }
        if ($contact === '') {
            $contact = '<li><a href="/kapcsolat">Kapcsolat</a></li>';
        }

        return <<<HTML
<footer class="ts-footer" data-gjs-type="ts-footer-full" data-gjs-name="Lábléc">
  <div class="ts-footer__grid">
    <div>
      <a class="ts-footer__brand-link" href="/">
        <img class="ts-footer__logo" src="{$logo}" alt="{$brandEsc}">
      </a>
      <p class="ts-footer__brand" data-ts-text="brand">{$brandEsc}</p>
      <p class="ts-footer__text" data-ts-text="text">{$textEsc}</p>
    </div>
    <div>
      <p class="ts-footer__label">Szállások</p>
      <ul class="ts-footer__list">
        <li><a href="/#csaladi-apartman">Családi apartman</a></li>
        <li><a href="/#munkasszallas">Munkásszállás</a></li>
        <li><a href="/szallasok">Összes szállás</a></li>
        <li><a href="/foglalas-panel">Foglalás</a></li>
      </ul>
    </div>
    <div>
      <p class="ts-footer__label">Elérhetőség</p>
      <ul class="ts-footer__list">
        {$contact}
        <li><a href="/kapcsolat">Kapcsolatűrlap</a></li>
      </ul>
    </div>
  </div>
  <div class="ts-footer__copy">© {$year} {$brandEsc} · Minden jog fenntartva</div>
</footer>
HTML;
    }

    protected function footerCss(): string
    {
        $base = SiteLayoutDefaults::footerCss();
        $template = $this->styleFromTemplate('footer-full.html');

        $tusi = <<<'CSS'
.ts-footer {
  background: #2a2a2a;
  color: #fff;
  margin-top: 0;
}
.ts-footer__logo {
  display: block;
  height: 2.75rem;
  width: auto;
  max-width: 12rem;
  object-fit: contain;
  margin: 0 0 .85rem;
  background: #fff;
  padding: .4rem .65rem;
  border-radius: .4rem;
}
.ts-footer__brand-link {
  display: inline-block;
  text-decoration: none;
}
.ts-footer__brand {
  margin: 0 0 .5rem;
}
.ts-footer__list a:hover {
  color: #fff;
  text-decoration: underline;
}
.ts-footer__copy {
  border-top-color: rgba(255,255,255,.12);
}
CSS;

        return trim($base."\n".$template."\n".$tusi);
    }

    protected function styleFromTemplate(string $file): string
    {
        $path = resource_path('site-builder/templates/'.$file);
        if (! is_file($path)) {
            return '';
        }

        $html = (string) file_get_contents($path);
        if (preg_match_all('/<style>(.*?)<\/style>/si', $html, $matches) === false) {
            return '';
        }

        return implode("\n", array_map('trim', $matches[1]));
    }

    protected function html(): string
    {
        $hero = self::PHOTO_HERO;
        $familyPhoto = self::PHOTO_FAMILY;
        $workersPhoto = self::PHOTO_WORKERS;
        $settings = SiteSetting::current();
        $guesthouse = Accommodation::query()->where('slug', 'tusi-vendeghaz')->first();
        $workersAcc = Accommodation::query()->where('slug', 'munkasszallas-a-szarny')->first();

        $ghName = (string) ($guesthouse?->name ?: 'Tüsi Vendégház');
        $ghCap = (int) ($guesthouse?->capacity ?: 8);
        $ghMin = (int) ($guesthouse?->min_nights ?: 2);
        $ghPrice = $this->huf($guesthouse?->price_from ?? $guesthouse?->base_price ?? 45000);
        $wName = (string) ($workersAcc?->name ?: 'Munkásszállás – A szárny');
        $wCap = (int) ($workersAcc?->capacity ?: 12);
        $wMin = (int) ($workersAcc?->min_nights ?: 7);
        $wPrice = $this->huf($workersAcc?->price_from ?? $workersAcc?->base_price ?? 8000);
        $address = trim((string) ($settings->address ?? ''));
        $mapQuery = $address !== '' ? $address : 'Tüsifalu, Magyarország';

        $familyItems = $this->json([
            ['icon' => 'users', 'title' => 'Akár '.$ghCap.' vendég', 'text' => 'Nappali, felszerelt konyha és kert – családnak, több generációnak vagy baráti társaságnak.'],
            ['icon' => 'wifi', 'title' => 'Wifi, klíma, parkoló', 'text' => 'A kocsi a telken marad. A házban van internet és hűtés – nyáron is kényelmes.'],
            ['icon' => 'calendar', 'title' => 'Online foglalás', 'text' => 'Dátum a keresőben, visszaigazolás e-mailben. Minimum '.$ghMin.' éjszaka, IFA-val.'],
        ]);
        $workerItems = $this->json([
            ['icon' => 'bed', 'title' => $wMin.' éjszakától', 'text' => 'Nem turista-hétvégére, hanem munkahétre és hosszabb kiküldetésre. Ágy, rend, közös konyha.'],
            ['icon' => 'shield', 'title' => 'Nyugodt szárny', 'text' => 'Házirend, wifi, műszakhoz igazított beköltözés. Akár '.$wCap.' fő.'],
            ['icon' => 'envelope', 'title' => 'Egyeztetéses foglalás', 'text' => 'Nincs nyilvános naptár: írd meg a létszámot és az időszakot, mi ajánlatot küldünk.'],
        ]);
        $faq = $this->json([
            [
                'q' => 'Miben különbözik a Tüsi Vendégház a munkásszállástól?',
                'a' => 'A '.$ghName.' a teljes ház: nappali, konyha, kert, online foglalható. A '.$wName.' hosszabb távú elhelyezés közös konyhával: érdeklődés után egyeztetünk, a bentlakást az admin rögzíti.',
            ],
            [
                'q' => 'Hányan férnek el a Tüsi Vendégházban?',
                'a' => 'A vendégház akár '.$ghCap.' vendégnek alkalmas. Pároknak a Napfényes szoba is elérhető a szállások között.',
            ],
            [
                'q' => 'Hogyan foglalhatok apartmant Tüsifalun?',
                'a' => 'A vendégháznál add meg az érkezést, a távozást és a vendégszámot a keresőben, majd fejezd be a foglalást a panelen. Visszaigazoló e-mailt küldünk.',
            ],
            [
                'q' => 'Van parkoló a vendégháznál?',
                'a' => 'Igen, a telken, kapun belül parkolhatsz. A kapukódot a foglalás megerősítése után küldjük.',
            ],
            [
                'q' => 'Munkásszállásra lehet online fizetni?',
                'a' => 'Nem. Írj vagy hívj: létszám, időszak, a szerződést nálunk rögzítjük. Irányár '.$wPrice.'-tól / éj, minimum '.$wMin.' éjszaka.',
            ],
            [
                'q' => 'Mennyi a minimum tartózkodás?',
                'a' => 'A '.$ghName.' minimum '.$ghMin.' éjszaka. A munkásszállás minimum '.$wMin.' éjszaka.',
            ],
            [
                'q' => 'Mikor lehet bejelentkezni?',
                'a' => 'Vendégház: érkezés 15:00-tól, távozás 10:00-ig. Munkásszállásnál a beköltözést a műszakhoz egyeztetjük.',
            ],
        ]);
        $mapEmbed = 'https://maps.google.com/maps?q='.rawurlencode($mapQuery).'&t=&z=13&ie=UTF8&iwloc=&output=embed';

        $heroTitle = 'Családi apartman és munkásszállás Tüsifalun';
        $heroLead = 'A '.$ghName.' a teljes házat adja: nappali, konyha, kert, parkoló a telken. A munkásszállás '.$wMin.' éjszakától, közös konyhával – érdeklődés után egyeztetünk.';
        $familyBody = 'A teljes vendégház a tiéd: közösségi nappali, felszerelt konyha és kert. Akár '.$ghCap.' vendég, wifi, klíma, parkolás a kapun belül. Online foglalható, IFA-val; minimum '.$ghMin.' éjszaka, '.$ghPrice.'-tól / éj. Érkezés 15:00, távozás 10:00.';
        $workersBody = 'Ágy, rend, közös konyha – műszakhoz igazított beköltözéssel. Akár '.$wCap.' fő, minimum '.$wMin.' éjszaka, '.$wPrice.'-tól / éj. Nincs nyilvános naptár: írsz, mi visszamegyünk, a bentlakást az admin rögzíti.';
        $mapText = ($address !== '' ? $address.'. ' : 'Tüsifalu. ').'Parkolás a telken; a kapukódot a foglalás megerősítése után küldjük.';

        return <<<HTML
<section class="ts-hero" data-gjs-type="ts-hero" data-gjs-name="Hero" data-layout="center" data-media-type="image" data-media-url="{$hero}" data-overlay="0.68" data-overlay-color="#2a2a2a" style="--ts-overlay-color:#2a2a2a;--ts-hero-overlay-opacity:0.68" data-eyebrow="Tüsiszállás · Tüsifalu" data-title="{$heroTitle}" data-lead="{$heroLead}" data-primary-label="Tüsi Vendégház" data-primary-href="#csaladi-apartman" data-primary-style="primary" data-secondary-label="Munkásszállás" data-secondary-href="#munkasszallas" data-secondary-style="inverse">
  <div class="ts-hero__media" aria-hidden="true">
    <img class="ts-hero__img" data-ts-bg-image data-ts-hero-image src="{$hero}" alt="Tüsi Vendégház kertje Tüsifalun">
    <video class="ts-hero__video" data-ts-bg-video data-ts-hero-video autoplay muted loop playsinline poster=""><source data-ts-bg-source data-ts-hero-source src="{$hero}" type="video/mp4"></video>
  </div>
  <div class="ts-hero__overlay" data-ts-bg-overlay data-ts-hero-overlay style="opacity:0.68"></div>
  <div class="ts-hero__inner">
    <p class="ts-hero__eyebrow" data-ts-text="eyebrow">Tüsiszállás · Tüsifalu</p>
    <h1 class="ts-hero__title" data-ts-text="title">{$heroTitle}</h1>
    <p class="ts-hero__lead" data-ts-text="lead">{$heroLead}</p>
    <div class="ts-hero__actions">
      <a class="ts-btn ts-btn--primary" data-ts-btn="primary_style" data-ts-text="primary_label" data-ts-href="primary_href" href="#csaladi-apartman">Tüsi Vendégház</a>
      <a class="ts-btn ts-btn--inverse" data-ts-btn="secondary_style" data-ts-text="secondary_label" data-ts-href="secondary_href" href="#munkasszallas">Munkásszállás</a>
    </div>
  </div>
</section>

<section class="ts-layout ts-layout--2col ts-stay-chooser" id="szallasok" data-gjs-type="ts-layout-2col" data-gjs-name="2 oszlop" data-section-width="content">
  <div class="ts-layout__inner ts-layout__inner--2col">
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="Bal oszlop">
      <article class="ts-stay-card ts-stay-card--family">
        <p class="ts-stay-card__badge">Család · online foglalás</p>
        <h2 class="ts-stay-card__title">{$ghName}</h2>
        <p class="ts-stay-card__text">Akár {$ghCap} főnek, teljes ház konyhával és kerttel. Foglalás a naptáron, minimum {$ghMin} éjszaka.</p>
        <a class="ts-btn ts-btn--primary" href="#csaladi-apartman">Vendégház részletei</a>
      </article>
    </div>
    <div class="ts-layout__col" data-gjs-droppable="true" data-gjs-name="Jobb oszlop">
      <article class="ts-stay-card ts-stay-card--workers">
        <p class="ts-stay-card__badge">Munka · egyeztetés</p>
        <h2 class="ts-stay-card__title">Munkásszállás</h2>
        <p class="ts-stay-card__text">Akár {$wCap} fő, {$wMin} éjszakától. Közös konyha, wifi – a bentlakást nálunk rögzítjük.</p>
        <a class="ts-btn ts-btn--primary" href="#munkasszallas">Munkásszállás részletei</a>
      </article>
    </div>
  </div>
</section>

<section class="ts-split ts-stay-family" id="csaladi-apartman" data-gjs-type="ts-split" data-gjs-name="Szöveg + kép" data-layout="image-right" data-media-url="{$familyPhoto}" data-eyebrow="Tüsi Vendégház" data-title="Családi apartman Tüsifalun" data-body="{$familyBody}" data-button-label="Szabad időpont" data-button-href="#foglalas" data-button-style="primary">
  <div class="ts-split__inner">
    <div class="ts-split__copy">
      <p class="ts-split__eyebrow" data-ts-text="eyebrow">Tüsi Vendégház</p>
      <h2 data-ts-text="title">Családi apartman Tüsifalun</h2>
      <p data-ts-text="body">{$familyBody}</p>
      <a class="ts-btn ts-btn--primary" data-ts-btn="button_style" data-ts-text="button_label" data-ts-href="button_href" href="#foglalas">Szabad időpont</a>
    </div>
    <div class="ts-split__media"><img data-ts-bg-image src="{$familyPhoto}" alt="Tüsi Vendégház nappalija – családi apartman Tüsifalun, konyhával és kerttel"></div>
  </div>
</section>

<section class="ts-features ts-stay-family" data-gjs-type="ts-features" data-gjs-name="Előnyök" data-title="Amit a vendégházban megtaláltok" data-columns="3" data-items="{$familyItems}">
  <div class="ts-features__inner">
    <h2 class="ts-features__title" data-ts-text="title">Amit a vendégházban megtaláltok</h2>
    <div class="ts-features__grid" data-ts-items="items" data-ts-items-kind="features"></div>
  </div>
</section>

<section class="ts-dyn-block ts-dyn-search ts-stay-family" id="foglalas" data-gjs-type="ts-availability-search" data-gjs-name="Szabad hely kereső" data-ts-dynamic="availability-search" data-title="Szabad időpont a Tüsi Vendégházban" data-text="Add meg az érkezést, a távozást és a vendégszámot. A foglalás a naptáron indul, {$ghPrice}-tól / éj." data-button="Keresés" data-slug="tusi-vendeghaz"></section>

<section class="ts-split ts-stay-workers" id="munkasszallas" data-gjs-type="ts-split" data-gjs-name="Szöveg + kép" data-layout="image-left" data-media-url="{$workersPhoto}" data-eyebrow="{$wName}" data-title="Hosszabb távú elhelyezés Tüsifalun" data-body="{$workersBody}" data-button-label="Érdeklődés" data-button-href="#kapcsolat" data-button-style="primary">
  <div class="ts-split__inner">
    <div class="ts-split__copy">
      <p class="ts-split__eyebrow" data-ts-text="eyebrow">{$wName}</p>
      <h2 data-ts-text="title">Hosszabb távú elhelyezés Tüsifalun</h2>
      <p data-ts-text="body">{$workersBody}</p>
      <a class="ts-btn ts-btn--primary" data-ts-btn="button_style" data-ts-text="button_label" data-ts-href="button_href" href="#kapcsolat">Érdeklődés</a>
    </div>
    <div class="ts-split__media"><img data-ts-bg-image src="{$workersPhoto}" alt="Munkásszállás A szárny Tüsifalun – hosszabb távú elhelyezés közös konyhával"></div>
  </div>
</section>

<section class="ts-icon-list ts-stay-workers" data-gjs-type="ts-icon-list" data-gjs-name="Ikonlista" data-layout="grid" data-title="Mit nyújt a munkásszállás" data-lead="Kevesebb dekoráció, több kiszámíthatóság – céges brigádnak és hosszabb kiküldetésre." data-columns="3" data-items="{$workerItems}">
  <div class="ts-icon-list__inner">
    <h2 class="ts-icon-list__title" data-ts-text="title">Mit nyújt a munkásszállás</h2>
    <p class="ts-icon-list__lead" data-ts-text="lead">Kevesebb dekoráció, több kiszámíthatóság – céges brigádnak és hosszabb kiküldetésre.</p>
    <div class="ts-icon-list__grid" data-ts-items="items" data-ts-items-kind="icon-list"></div>
  </div>
</section>

<section class="ts-pricing ts-stay-workers" data-gjs-name="Ártábla">
  <div class="ts-pricing__inner">
    <h2>Munkásszállás irányára</h2>
    <p class="ts-pricing__lead">Az ár ágytól értendő. A végleges díjat létszám és időszak után küldjük – ez nem online kosár.</p>
    <div class="ts-pricing__table">
      <div class="ts-pricing__row ts-pricing__head"><span>Időszak</span><span>Ár / éj</span><span>Megjegyzés</span></div>
      <div class="ts-pricing__row"><span>Hosszabb táv</span><span>{$wPrice}-tól</span><span>Minimum {$wMin} éj, wifi, közös konyha</span></div>
      <div class="ts-pricing__row"><span>Céges brigád</span><span>Egyedi ajánlat</span><span>Több ágy, hosszabb szerződés</span></div>
    </div>
    <p class="ts-pricing__foot">A {$ghName} árai a foglalási naptárban élnek, {$ghPrice}-tól / éj.</p>
  </div>
</section>

<section class="ts-steps ts-stay-shared" data-gjs-name="Hogyan foglalj">
  <div class="ts-steps__inner">
    <h2>Hogyan foglalsz Tüsiszálláson</h2>
    <p class="ts-steps__lead">A vendégház három kattintás. A munkásszállás egy üzenet.</p>
    <ol class="ts-steps__grid">
      <li><span>1</span><h3>Szállás típusa</h3><p>Vendégház, ha család vagy barátok jönnek. Munkásszállás, ha hosszabb távra, munkára kerestek ágyat.</p></li>
      <li><span>2</span><h3>Időpont</h3><p>Vendégházhoz dátum és létszám a keresőben. Munkásszálláshoz írd meg a heteket és a létszámot.</p></li>
      <li><span>3</span><h3>Visszaigazolás</h3><p>Vendégház: e-mail a foglalásról. Munkásszállás: egyeztetés után rögzítjük a bentlakást.</p></li>
    </ol>
  </div>
</section>

<section class="ts-faq ts-stay-shared" data-gjs-type="ts-faq" data-gjs-name="GYIK" data-title="Gyakori kérdések a Tüsi szállásról" data-lead="Vendégház vagy munkásszállás: más szabály, ugyanaz a cím Tüsifalun." data-items="{$faq}">
  <div class="ts-faq__inner">
    <h2 data-ts-text="title">Gyakori kérdések a Tüsi szállásról</h2>
    <p class="ts-faq__lead" data-ts-text="lead">Vendégház vagy munkásszállás: más szabály, ugyanaz a cím Tüsifalun.</p>
    <div class="ts-faq__list" data-ts-items="items" data-ts-items-kind="faq"></div>
  </div>
</section>

<section class="ts-dyn-block ts-dyn-map ts-stay-contact" data-gjs-type="ts-site-map" data-gjs-name="Helyszín" data-ts-dynamic="site-map" data-title="Hol van a Tüsiszállás" data-text="{$mapText}" data-embed-url="{$mapEmbed}"></section>
<section class="ts-dyn-block ts-dyn-contact-form ts-stay-contact" id="kapcsolat" data-gjs-type="ts-contact-form" data-gjs-name="Kapcsolat űrlap" data-ts-dynamic="contact-form" data-title="Írj a Tüsiszállásnak" data-text="Vendégház-foglalás, munkásszállás vagy egyéb kérdés – egy űrlap, válaszolunk." data-button="Üzenet küldése"></section>
HTML;
    }

    protected function huf(mixed $amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' Ft';
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function json(array $items): string
    {
        $json = json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return htmlspecialchars((string) $json, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function css(): string
    {
        return $this->blockCss()."\n\n".$this->pageCss();
    }

    /**
     * Az oldalépítő blokk-sablonok szerkezeti CSS-e – Grapes nélkül a nyilvános oldalon is kell.
     */
    protected function blockCss(): string
    {
        $chunks = [];

        foreach ([
            'hero.html',
            'split.html',
            'features.html',
            'icon-list.html',
            'faq.html',
            'pricing-table.html',
            'how-to-book.html',
        ] as $file) {
            $html = (string) file_get_contents(resource_path('site-builder/templates/'.$file));
            if (preg_match_all('/<style>(.*?)<\/style>/si', $html, $matches) === false) {
                continue;
            }
            foreach ($matches[1] as $css) {
                $chunks[] = trim((string) $css);
            }
        }

        return implode("\n\n", $chunks);
    }

    protected function pageCss(): string
    {
        return <<<'CSS'
.site-nav.has-logo,
.ts-header-simple.has-logo {
  background: #ffffff;
  color: #3d3d3d;
  border-bottom: 1px solid #ececec;
}
.site-nav.has-logo .ts-nav-links a,
.ts-header-simple.has-logo .ts-nav-links a {
  color: #3d3d3d;
}

.site-builder-page {
  background: #f7f4f2;
}
.site-builder-content {
  display: flex;
  flex-direction: column;
}
.site-builder-content > section {
  margin: 0;
}

/* Hero: a kép takarja a szekciót, a szöveg olvasható */
.site-builder-content > .ts-hero {
  --ts-overlay-color: #2a2a2a;
  position: relative !important;
  isolation: isolate;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  min-height: 72vh !important;
  background: #2a2a2a !important;
  color: #fff !important;
  padding-top: 7rem !important;
  padding-bottom: 4.5rem !important;
}
.site-builder-content > .ts-hero .ts-hero__media {
  position: absolute !important;
  inset: 0 !important;
  z-index: 0 !important;
}
.site-builder-content > .ts-hero .ts-hero__img,
.site-builder-content > .ts-hero .ts-hero__video {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
  display: none;
}
.site-builder-content > .ts-hero[data-media-type="image"] .ts-hero__img {
  display: block !important;
}
.site-builder-content > .ts-hero .ts-hero__overlay {
  position: absolute !important;
  inset: 0 !important;
  z-index: 1 !important;
  pointer-events: none;
  background: linear-gradient(
    180deg,
    color-mix(in srgb, #2a2a2a 45%, transparent) 0%,
    color-mix(in srgb, #2a2a2a 72%, transparent) 55%,
    color-mix(in srgb, #2a2a2a 88%, transparent) 100%
  ) !important;
}
.site-builder-content > .ts-hero .ts-hero__inner {
  position: relative !important;
  z-index: 2 !important;
  color: #fff;
}
.site-builder-content > .ts-hero .ts-hero__title,
.site-builder-content > .ts-hero .ts-hero__lead,
.site-builder-content > .ts-hero .ts-hero__eyebrow {
  color: #fff !important;
}

.ts-stay-family {
  --stay-accent: #e3007f;
  --stay-ink: #3d3d3d;
}
.ts-stay-workers {
  --stay-accent: #4d4d4d;
  --stay-ink: #2a2a2a;
}

.site-builder-content > .ts-stay-family,
.site-builder-content > .ts-stay-workers,
.site-builder-content > .ts-stay-shared,
.site-builder-content > .ts-stay-contact {
  color: var(--stay-ink, var(--color-text));
  padding-top: 1.1rem !important;
  padding-bottom: 1.1rem !important;
}

.site-builder-content > .ts-stay-family,
.site-builder-content > .ts-stay-family.ts-features,
.site-builder-content > .ts-stay-family.ts-dyn-search,
.site-builder-content > .ts-stay-family.ts-split {
  background: #fff7fb !important;
}
.site-builder-content > .ts-stay-workers,
.site-builder-content > .ts-stay-workers.ts-icon-list,
.site-builder-content > .ts-stay-workers.ts-pricing,
.site-builder-content > .ts-stay-workers.ts-split {
  background: #eef0f3 !important;
}
.site-builder-content > .ts-stay-shared,
.site-builder-content > .ts-stay-shared.ts-steps,
.site-builder-content > .ts-stay-shared.ts-faq {
  background: #f7f4f2 !important;
}
.site-builder-content > .ts-stay-contact,
.site-builder-content > .ts-stay-contact.ts-dyn-map,
.site-builder-content > .ts-stay-contact.ts-dyn-contact-form {
  background: #ffffff !important;
}

.site-builder-content > :not(.ts-stay-family) + .ts-stay-family {
  padding-top: 3.25rem !important;
}
.site-builder-content > .ts-stay-family:has(+ :not(.ts-stay-family)) {
  padding-bottom: 3.25rem !important;
}
.site-builder-content > :not(.ts-stay-workers) + .ts-stay-workers {
  padding-top: 3.25rem !important;
}
.site-builder-content > .ts-stay-workers:has(+ :not(.ts-stay-workers)) {
  padding-bottom: 3.25rem !important;
}
.site-builder-content > :not(.ts-stay-shared) + .ts-stay-shared {
  padding-top: 3rem !important;
}
.site-builder-content > .ts-stay-shared:has(+ :not(.ts-stay-shared)) {
  padding-bottom: 3rem !important;
}
.site-builder-content > :not(.ts-stay-contact) + .ts-stay-contact {
  padding-top: 3rem !important;
}
.site-builder-content > .ts-stay-contact:last-child {
  padding-bottom: 4rem !important;
}

.ts-stay-family .ts-split__eyebrow,
.ts-stay-workers .ts-split__eyebrow {
  color: var(--stay-accent);
  opacity: 1;
}

.ts-stay-family .ts-btn--primary {
  background: #e3007f !important;
  color: #fff !important;
  border-color: #e3007f !important;
}
.ts-stay-workers .ts-btn--primary {
  background: #3d3d3d !important;
  color: #fff !important;
  border-color: #3d3d3d !important;
}

.ts-stay-family .ts-feature__icon { color: #e3007f; }
.ts-stay-workers .ts-icon-item__icon { color: #4d4d4d; }

.ts-stay-family .ts-feature,
.ts-stay-workers .ts-icon-item,
.ts-stay-workers .ts-pricing__table,
.ts-stay-family .ts-dyn-search__form {
  background: #fff !important;
}

.site-builder-content > .ts-stay-chooser {
  background: #f7f4f2 !important;
  margin-top: 0;
  padding-top: 2.5rem !important;
  padding-bottom: 2.75rem !important;
}
.ts-stay-chooser .ts-layout__placeholder { display: none !important; }
.ts-stay-chooser .ts-layout__inner--2col {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
  gap: 1.25rem !important;
}
.ts-stay-chooser .ts-layout__col {
  display: flex !important;
  min-height: 0;
}
.ts-stay-chooser .ts-stay-card {
  width: 100%;
  flex: 1;
}

.ts-stay-card {
  padding: 1.6rem 1.5rem;
  border-radius: 1rem;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
.ts-stay-card__badge {
  margin: 0;
  font-size: 0.7rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  font-weight: 700;
}
.ts-stay-card__title {
  margin: 0;
  font-family: var(--font-display, Literata, serif);
  font-size: clamp(1.4rem, 2.4vw, 1.85rem);
  line-height: 1.2;
}
.ts-stay-card__text {
  margin: 0 0 auto;
  line-height: 1.55;
  opacity: 0.88;
}
.ts-stay-card--family {
  background: #fff7fb;
  color: #3d3d3d;
  border: 1px solid color-mix(in srgb, #e3007f 22%, transparent);
}
.ts-stay-card--family .ts-stay-card__badge { color: #e3007f; }
.ts-stay-card--family .ts-btn {
  background: #e3007f !important;
  color: #fff !important;
  align-self: flex-start;
}
.ts-stay-card--workers {
  background: #eef0f3;
  color: #2a2a2a;
  border: 1px solid color-mix(in srgb, #4d4d4d 18%, transparent);
}
.ts-stay-card--workers .ts-stay-card__badge { color: #4d4d4d; }
.ts-stay-card--workers .ts-btn {
  background: #3d3d3d !important;
  color: #fff !important;
  align-self: flex-start;
}

.ts-stay-shared .ts-faq__item,
.ts-stay-shared .ts-steps__grid li {
  background: #fff !important;
}

.ts-stay-workers .ts-pricing__lead,
.ts-stay-workers .ts-pricing__foot,
.ts-stay-shared .ts-steps__lead {
  opacity: 0.78;
  max-width: 40rem;
}

.ts-split__inner {
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
  align-items: center !important;
}
.ts-features__grid,
.ts-icon-list__grid {
  display: grid !important;
}
.ts-steps__grid {
  display: grid !important;
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
}

@media (max-width: 800px) {
  .ts-stay-chooser .ts-layout__inner--2col,
  .ts-split__inner,
  .ts-steps__grid {
    grid-template-columns: 1fr !important;
  }
}
CSS;
    }
}
