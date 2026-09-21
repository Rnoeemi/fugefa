<?php

namespace App\Models;

use App\Services\SitePageCssWriter;
use App\Services\SiteThemeService;
use App\Support\SiteButtonStyles;
use App\Support\SiteColors;
use App\Support\SiteStylePresets;
use App\Support\SiteTypography;
use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'site_name',
    'phone',
    'email',
    'notification_email',
    'contact_notification_email',
    'address',
    'hero_image',
    'footer_text',
    'header_html',
    'header_css',
    'header_grapes_data',
    'footer_html',
    'footer_css',
    'footer_grapes_data',
    'font_sans',
    'font_display',
    'font_size_base',
    'line_height',
    'typography',
    'global_colors',
    'button_styles',
    'style_preset',
    'custom_css',
    'modules',
])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'header_grapes_data' => 'array',
            'footer_grapes_data' => 'array',
            'global_colors' => 'array',
            'button_styles' => 'array',
            'typography' => 'array',
            'modules' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (SiteSetting $settings): void {
            $theme = app(SiteThemeService::class);
            $themeFields = ['font_sans', 'font_display', 'font_size_base', 'line_height', 'typography', 'global_colors', 'button_styles', 'style_preset', 'site_name'];

            if ($settings->wasChanged($themeFields) || $settings->wasRecentlyCreated) {
                $theme->writeThemeCss($settings);
            }

            if ($settings->wasChanged(['custom_css']) || ($settings->wasRecentlyCreated && filled($settings->custom_css))) {
                $theme->writeCustomCss($settings);
            }

            $writer = app(SitePageCssWriter::class);

            if ($settings->wasChanged(['header_css']) || ($settings->wasRecentlyCreated && filled($settings->header_css))) {
                $writer->writeLayoutPart('header', (string) ($settings->header_css ?? ''));
            }

            if ($settings->wasChanged(['footer_css']) || ($settings->wasRecentlyCreated && filled($settings->footer_css))) {
                $writer->writeLayoutPart('footer', (string) ($settings->footer_css ?? ''));
            }
        });
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'site_name' => 'Tüsiszállás',
            'phone' => '+36 30 123 4567',
            'email' => 'info@tusiszallas.hu',
            'notification_email' => 'foglalas@tusiszallas.hu',
            'address' => '1234 Mintaút, Tüsifalu, Fő utca 12.',
            'hero_image' => 'images/site/hero.jpg',
            'footer_text' => 'Vendégház és szobák nyugodt környezetben. Foglaljon online, vagy keressen minket telefonon.',
            'font_sans' => 'Karla',
            'font_display' => 'Literata',
            'font_size_base' => '16px',
            'line_height' => '1.6',
            'global_colors' => SiteColors::defaults(),
            'style_preset' => SiteStylePresets::DEFAULT,
        ]);
    }

    public function resolvedStylePreset(): string
    {
        return SiteStylePresets::normalizeKey($this->style_preset ?? null);
    }

    /**
     * @return array{
     *     primary: string,
     *     text: string,
     *     accent: string,
     *     light: string,
     *     extra: list<array{key: string, label: string, value: string}>
     * }
     */
    public function resolvedGlobalColors(): array
    {
        return SiteColors::normalize(is_array($this->global_colors) ? $this->global_colors : null);
    }

    /**
     * @return array{
     *     primary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     secondary: array{bg: string, fg: string, hover_bg: string, border: string},
     *     inverse: array{bg: string, fg: string, hover_bg: string, border: string}
     * }
     */
    public function resolvedButtonStyles(): array
    {
        return SiteButtonStyles::normalize(
            is_array($this->button_styles) ? $this->button_styles : null,
            $this->resolvedGlobalColors()
        );
    }

    /**
     * @return array{
     *     section_title: string,
     *     section_lead: string,
     *     card_title: string,
     *     card_body: string
     * }
     */
    public function resolvedTypography(): array
    {
        return SiteTypography::normalize(
            is_array($this->typography) ? $this->typography : null,
            $this->resolvedStylePreset()
        );
    }

    public function heroUrl(): string
    {
        if (blank($this->hero_image)) {
            return asset('images/site/hero.jpg');
        }

        if (str_starts_with($this->hero_image, 'images/')) {
            return asset($this->hero_image);
        }

        return Storage::disk('public')->url($this->hero_image);
    }

    public function hasCustomHeader(): bool
    {
        return filled($this->header_html);
    }

    public function hasCustomFooter(): bool
    {
        return filled($this->footer_html);
    }

    public function headerCssUrl(): ?string
    {
        return app(SitePageCssWriter::class)->layoutPartUrl('header', $this->updated_at?->timestamp);
    }

    public function footerCssUrl(): ?string
    {
        return app(SitePageCssWriter::class)->layoutPartUrl('footer', $this->updated_at?->timestamp);
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): ?string => PhoneNormalizer::toString($value),
            set: fn (mixed $value): ?string => PhoneNormalizer::toString($value),
        );
    }
}
