<?php

namespace App\Livewire;

use App\Filament\Resources\SitePages\SitePageResource;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Services\SiteDynamicBlockRenderer;
use App\Services\SitePageCssWriter;
use App\Services\SiteThemeService;
use App\Support\GrapesJs\SiteBlockCatalog;
use App\Support\GrapesJs\SiteBlockLayouts;
use App\Support\GrapesJs\SiteBlockVisibility;
use App\Support\GrapesJs\SiteBuilderPublicLinks;
use App\Support\GrapesJs\SiteContentIcons;
use App\Support\GrapesJs\SiteDynamicBlockStyles;
use App\Support\GrapesJs\SiteLayoutDefaults;
use App\Support\GrapesJs\SiteRichAttr;
use App\Support\Seo\SitePageSeo;
use App\Support\SiteBuilderCss;
use App\Support\SiteColors;
use App\Support\SiteFonts;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Component;

#[Layout('layouts.grapes-builder')]
class SiteGrapesBuilder extends Component
{
    public string $target = 'page';

    public ?int $pageId = null;

    public string $title = 'Vizuális szerkesztő';

    public function mount(?SitePage $page = null): void
    {
        abort_unless(auth()->check(), 403);

        $this->target = match (true) {
            request()->routeIs('site-builder.header') => 'header',
            request()->routeIs('site-builder.footer') => 'footer',
            default => 'page',
        };

        if ($this->target === 'page') {
            abort_unless($page instanceof SitePage, 404);
            abort_unless(SitePageResource::canEdit($page), 403);
            $this->pageId = $page->id;
            $this->title = 'Oldal: '.$page->title;
        } else {
            abort_unless(SitePageResource::canViewAny(), 403);
            $this->title = $this->target === 'header' ? 'Fejléc szerkesztő' : 'Lábléc szerkesztő';
        }
    }

    /**
     * @param  array<string, mixed>  $grapesData
     */
    #[Renderless]
    public function syncFromEditor(string $html, string $css, array $grapesData = []): void
    {
        $writer = app(SitePageCssWriter::class);
        $css = SiteBuilderCss::sanitize($css);
        if ($grapesData !== []) {
            $grapesData = SiteBuilderCss::sanitizeProjectData($grapesData);
        }

        if ($this->target === 'page') {
            $page = SitePage::query()->findOrFail($this->pageId);
            abort_unless(SitePageResource::canEdit($page), 403);

            $page->update([
                'html' => SiteRichAttr::repairFeaturesSections($html),
                'css' => $css,
                'grapes_data' => $grapesData,
            ]);

            $writer->write($page->fresh(), $css);

            return;
        }

        abort_unless(SitePageResource::canViewAny(), 403);

        $settings = SiteSetting::current();

        if ($this->target === 'header') {
            // A főmenü linkek ne legyenek szerkeszthetők a fejléc szerkesztőben:
            // a menü tartalom helye placeholder, amit a publikus oldalon a menu-manager tölt be.
            $html = SiteLayoutDefaults::ensureHeaderChrome($html);

            // Új fejléc CSS szabályok (logo / hamburger / inherit szín) – régi mentésekhez is.
            if (! str_contains($css, '.ts-nav-toggle') || ! str_contains($css, '--ts-logo-height') || ! str_contains($css, 'color:inherit')) {
                $css = trim($css."\n".SiteLayoutDefaults::headerCss());
            }

            $settings->update([
                'header_html' => $html,
                'header_css' => $css,
                'header_grapes_data' => $grapesData,
            ]);
            $writer->writeLayoutPart('header', $css);

            return;
        }

        $settings->update([
            'footer_html' => $html,
            'footer_css' => $css,
            'footer_grapes_data' => $grapesData,
        ]);
        $writer->writeLayoutPart('footer', $css);
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    #[Renderless]
    public function renderDynamicPreview(string $type, array $attrs = []): string
    {
        abort_unless(auth()->check(), 403);

        return app(SiteDynamicBlockRenderer::class)->renderBlock($type, $attrs);
    }

    /**
     * @return array<string, mixed>
     */
    public function editorPayload(): array
    {
        $theme = app(SiteThemeService::class)->forFrontend();
        $renderer = app(SiteDynamicBlockRenderer::class);
        // A téma CSS-t a canvas betöltése után injektáljuk (lásd builder JS),
        // különben a Grapes komponens-CSS felülírja, és a fehér kártyákon eltűnik a szöveg.
        // A teljes fontkatalógus kell a tipográfia előnézethez (ne csak a kiválasztott 2).
        $canvasStyles = array_values(array_filter([
            SiteFonts::catalogStylesheet() ?: $theme['font_stylesheet'],
        ]));

        $settings = SiteSetting::current();

        $shared = [
            'target' => $this->target,
            'accommodationOptions' => $renderer->accommodationOptions(),
            'dynamicCss' => SiteDynamicBlockStyles::css(),
            'visibilityCss' => SiteBlockVisibility::css(),
            'layoutCss' => SiteBlockLayouts::css(),
            'canvasStyles' => $canvasStyles,
            'themeCssUrl' => $theme['theme_css_url'] ?? null,
            'customCssUrl' => $theme['custom_css_url'] ?? null,
            'globalColors' => $theme['global_colors'] ?? [],
            'fontFamilies' => SiteFonts::builderOptions(),
            'mediaListUrl' => route('site-builder.media.index'),
            'mediaUploadUrl' => route('site-builder.media.store'),
            'csrfToken' => csrf_token(),
            'publicLinks' => SiteBuilderPublicLinks::all(),
            'menuPlaceholderHtml' => SiteLayoutDefaults::menuPlaceholderHtml(),
            'menuMobilePlaceholderHtml' => SiteLayoutDefaults::menuMobilePlaceholderHtml(),
            'contentIcons' => SiteContentIcons::optionsForBuilder(),
            'siteContact' => [
                'phone' => \App\Support\PhoneNormalizer::toString($settings->phone) ?? '',
                'email' => (string) ($settings->email ?? ''),
                'address' => (string) ($settings->address ?? ''),
                'brand' => (string) ($settings->site_name ?? ''),
                'footer_text' => (string) ($settings->footer_text ?? ''),
            ],
        ];

        if ($this->target === 'page') {
            $page = SitePage::query()->findOrFail($this->pageId);

            $rawHtml = $page->html ?? '';
            $html = SiteRichAttr::repairFeaturesSections($rawHtml);
            $projectData = is_array($page->grapes_data) && filled($page->grapes_data) ? $page->grapes_data : null;
            if (is_array($projectData)) {
                $projectData = SiteBuilderCss::sanitizeProjectData($projectData);
            }
            $css = SiteBuilderCss::sanitize((string) ($page->css ?? ''));

            // Sérült features markup: a grapes projectData felülírná a javított HTML-t.
            if ($html !== $rawHtml) {
                $projectData = null;
                if ($page->html !== $html || filled($page->grapes_data)) {
                    $page->forceFill([
                        'html' => $html,
                        'grapes_data' => null,
                    ])->save();
                }
            }

            return [
                ...$shared,
                'html' => $html,
                'css' => $css,
                'projectData' => $projectData,
                'blocks' => SiteBlockCatalog::definitionsFor('page'),
                'dynamicBlocks' => SiteBlockCatalog::dynamicDefinitionsFor('page'),
                'interactiveBlocks' => SiteBlockCatalog::interactiveDefinitionsFor('page'),
                'emptyHint' => 'Húzza be a bal oldali elemeket.',
                'enableDynamicBlocks' => true,
                'enableSeo' => true,
                'seo' => SitePageSeo::forPage($page),
                'seoPageUrl' => SitePageSeo::pageUrl($page),
                'seoPageTitle' => $page->title,
            ];
        }

        $settings = SiteSetting::current();

        if ($this->target === 'header') {
            $html = $settings->header_html;
            $css = $settings->header_css;

            if (blank($html)) {
                $html = SiteLayoutDefaults::headerHtml($settings->site_name ?: 'Tüsiszállás');
                $css = SiteLayoutDefaults::headerCss();
            }

            // Régi mentések: logo + hamburger váz, menü placeholder.
            $html = SiteLayoutDefaults::ensureHeaderChrome((string) $html);

            if (! str_contains((string) $css, '.ts-nav-toggle') || ! str_contains((string) $css, '--ts-logo-height') || ! str_contains((string) $css, 'color:inherit')) {
                $css = trim((string) $css."\n".SiteLayoutDefaults::headerCss());
            }

            if (! str_contains((string) $css, '.ts-menu-placeholder')) {
                $css = trim((string) $css)."\n.ts-menu-placeholder{display:inline-flex;align-items:center;padding:.45rem .85rem;border:1px dashed color-mix(in srgb,currentColor 50%,transparent);border-radius:.25rem;opacity:.8;font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;user-select:none;pointer-events:none;color:inherit}";
            }

            $css = SiteLayoutDefaults::sanitizeHeaderCss((string) ($css ?? ''));
            $css = trim($css."\n".SiteLayoutDefaults::headerLayoutCss());
            $css = trim($css."\n".SiteLayoutDefaults::headerReadableCss());
            $css = trim($css."\n".SiteLayoutDefaults::mobileMenuCss());

            // A projectData felülírná a placeholder HTML-t a régi menülinkekkel,
            // ezért fejlécnél HTML + CSS betöltést használunk.
            return [
                ...$shared,
                'html' => $html ?? '',
                'css' => SiteBuilderCss::sanitize((string) ($css ?? '')),
                'projectData' => null,
                'blocks' => SiteBlockCatalog::definitionsFor('header'),
                'dynamicBlocks' => [],
                'interactiveBlocks' => SiteBlockCatalog::interactiveDefinitionsFor('header'),
                'emptyHint' => 'Szerkessze a fejlécet. A menüpontokat a Menükezelőben állítsa be.',
                'enableDynamicBlocks' => false,
                'enableSeo' => false,
                'seo' => SitePageSeo::empty(),
            ];
        }

        $html = $settings->footer_html;
        $css = $settings->footer_css;
        $project = $settings->footer_grapes_data;

        if (blank($html)) {
            $html = SiteLayoutDefaults::footerHtml(
                $settings->site_name ?: 'Tüsiszállás',
                $settings->footer_text,
                $settings->address,
                $settings->phone,
                $settings->email,
            );
            $css = SiteLayoutDefaults::footerCss();
        }

        $html = SiteLayoutDefaults::hydrateFooterContact((string) $html, $settings);
        $html = SiteLayoutDefaults::stripFooterCopyright($html);
        $css = trim((string) $css."\n".SiteLayoutDefaults::footerCss());

        return [
            ...$shared,
            'html' => $html ?? '',
            'css' => SiteBuilderCss::sanitize((string) ($css ?? '')),
            'projectData' => is_array($project) && filled($project)
                ? SiteBuilderCss::sanitizeProjectData($project)
                : null,
            'blocks' => SiteBlockCatalog::definitionsFor('footer'),
            'dynamicBlocks' => [],
            'interactiveBlocks' => SiteBlockCatalog::interactiveDefinitionsFor('footer'),
            'emptyHint' => 'Szerkessze a láblécet, vagy húzzon be új szekciókat (1 / 2 / 3 oszlop, szöveg, kép).',
            'enableDynamicBlocks' => false,
            'enableSeo' => false,
            'seo' => SitePageSeo::empty(),
        ];
    }

    /**
     * @param  array<string, mixed>  $seo
     */
    #[Renderless]
    public function saveSeo(array $seo): array
    {
        abort_unless($this->target === 'page' && $this->pageId, 403);

        $page = SitePage::query()->findOrFail($this->pageId);
        abort_unless(SitePageResource::canEdit($page), 403);

        $normalized = SitePageSeo::normalize($seo);
        $page->update(['seo' => $normalized]);

        return SitePageSeo::forPage($page->fresh());
    }

    public function render(): View
    {
        return view('livewire.site-grapes-builder', [
            'payload' => $this->editorPayload(),
            'backUrl' => $this->backUrl(),
            'previewUrl' => $this->previewUrl(),
            'enableSeo' => $this->target === 'page',
        ]);
    }

    protected function backUrl(): string
    {
        if ($this->target === 'page' && $this->pageId) {
            return SitePageResource::getUrl('edit', ['record' => $this->pageId]);
        }

        return url('/rx-panel/site-appearance');
    }

    protected function previewUrl(): ?string
    {
        if ($this->target !== 'page' || ! $this->pageId) {
            return url('/');
        }

        $page = SitePage::query()->find($this->pageId);

        if (! $page) {
            return url('/');
        }

        return $page->is_homepage
            ? url('/')
            : (filled($page->slug) ? route('site-pages.show', ['slug' => $page->slug]) : url('/'));
    }
}
