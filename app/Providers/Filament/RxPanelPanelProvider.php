<?php

namespace App\Providers\Filament;

use App\Filament\MenuManager\HungarianFilamentMenuManagerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Data\AuthPageConfig;
use Caresome\FilamentAuthDesigner\Enums\MediaPosition;
use Prodstarter\FilamentNotificationCenter\FilamentNotificationCenterPlugin;
use Prodstarter\FilamentNotificationCenter\NotificationCenterCategory;
use Leek\FilamentRightClick\FilamentRightClickPlugin;
use Bjanczak\FilamentFlexFields\FilamentFlexFieldsPlugin;

class RxPanelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->brandName('Tüsiszállás Adminisztráció')
            ->default()
            ->id('rx-panel')
            ->path('rx-panel')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\Filament\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\Filament\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\Filament\Widgets'
            )
            ->widgets([
                \App\Filament\Widgets\OperationalStatsOverview::class,
                \App\Filament\Widgets\BookingStatsOverview::class,
                \App\Filament\Widgets\RevenueStatsOverview::class,
                \App\Filament\Widgets\InventoryStatsOverview::class,
                \App\Filament\Widgets\BookingCalendarWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->plugins([
                AuthDesignerPlugin::make()
                    ->login(fn (AuthPageConfig $config) => $config
                        ->media(asset('images/lsscreen.jpg'))
                        ->mediaPosition(MediaPosition::Left)
                        ->blur(5)
                    ),
                FilamentNotificationCenterPlugin::make()->categories([
                    NotificationCenterCategory::make('booking')
                        ->label('Foglalások')
                        ->icon(Heroicon::BookOpen)
                        ->color(Color::Emerald)
                        ->order(1),
                    NotificationCenterCategory::make('contact')
                        ->label('Kapcsolat')
                        ->icon(Heroicon::ChatBubbleBottomCenterText)
                        ->color(Color::Emerald)
                        ->order(2),
                    NotificationCenterCategory::make('payment')
                        ->label('Fizetés')
                        ->icon(Heroicon::CreditCard)
                        ->color(Color::Emerald)
                        ->order(3),
                ]),
                FilamentRightClickPlugin::make(),
                FilamentFlexFieldsPlugin::make(),
                HungarianFilamentMenuManagerPlugin::make()
                    ->locations([
                        'primary' => 'Webhely',
                    ])
                    ->modelSources([
                        \App\Models\SitePage::class,
                        \App\Models\Accommodation::class,
                    ])
                    ->navigationGroup('Webhely')
                    ->navigationLabel('Menükezelő')
                    ->navigationIcon('heroicon-o-bars-3'),
            ]);
    }
}
