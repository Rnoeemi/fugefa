<?php

namespace App\Providers\Filament;

use App\Filament\Appointment\Pages\AppointmentBookingPage;
use App\Http\Middleware\EnsureSiteModuleEnabled;
use App\Models\SiteSetting;
use App\Services\SiteThemeService;
use Bjanczak\FilamentFlexFields\FilamentFlexFieldsPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppointmentBookingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('appointment-booking')
            ->path('idopontfoglalas')
            ->brandName(fn (): string => SiteSetting::current()->site_name ?: 'Tüsiszállás')
            ->darkMode(false)
            ->topbar(false)
            ->navigation(false)
            ->font(fn (): string => app(SiteThemeService::class)->forFrontend()['font_sans'] ?? 'Karla')
            ->colors([
                'primary' => Color::hex('#2f6b52'),
                'gray' => Color::Stone,
            ])
            ->viteTheme('resources/css/filament/appointment-booking-theme.css')
            ->maxContentWidth(Width::SevenExtraLarge)
            ->pages([
                AppointmentBookingPage::class,
            ])
            ->homeUrl(fn (): string => AppointmentBookingPage::getUrl(panel: 'appointment-booking'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureSiteModuleEnabled::class.':appointment',
            ])
            ->plugins([
                FilamentFlexFieldsPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@include(\'filament.appointment.hooks.scripts\')'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => view('filament.appointment.hooks.nav')->render()
                    .view('filament.appointment.partials.hero')->render(),
            )
            ->renderHook(
                PanelsRenderHook::LAYOUT_END,
                fn (): string => view('filament.appointment.hooks.footer')->render(),
            );
    }
}
