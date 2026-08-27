<?php

namespace App\Providers\Filament;

use App\Filament\Reception\Pages\ReceptionDashboard;
use App\Filament\Reception\Resources\Bookings\ReceptionBookingResource;
use App\Http\Middleware\EnsureSiteModuleEnabled;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Bjanczak\FilamentFlexFields\FilamentFlexFieldsPlugin;
use Prodstarter\FilamentNotificationCenter\FilamentNotificationCenterPlugin;
use Prodstarter\FilamentNotificationCenter\NotificationCenterCategory;
use Filament\Support\Icons\Heroicon;

class ReceptionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('recepcio')
            ->path('recepcio')
            ->login()
            ->brandName('Tüsiszállás Recepció')
            ->colors(['primary' => Color::Emerald])
            ->font('Karla')
            ->spa()
            ->resources([
                ReceptionBookingResource::class,
            ])
            ->pages([
                ReceptionDashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureSiteModuleEnabled::class.':accommodation',
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentFlexFieldsPlugin::make(),
                FilamentNotificationCenterPlugin::make()->categories([
                    NotificationCenterCategory::make('booking')
                        ->label('Foglalások')
                        ->icon(Heroicon::CalendarDays)
                        ->color(Color::Emerald)
                        ->order(1),
                ]),
            ]);
    }
}
