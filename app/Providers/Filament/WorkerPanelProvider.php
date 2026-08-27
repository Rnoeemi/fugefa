<?php

namespace App\Providers\Filament;

use App\Filament\Worker\Pages\WorkerDashboard;
use App\Filament\Worker\Pages\WorkerGoogleCalendarSettings;
use App\Filament\Worker\Pages\WorkerProfileSettings;
use App\Filament\Worker\Resources\Appointments\WorkerAppointmentResource;
use App\Filament\Worker\Resources\Packages\WorkerPackageResource;
use App\Filament\Worker\Widgets\WorkerAppointmentCalendarWidget;
use App\Filament\Worker\Widgets\WorkerTodayAppointmentsWidget;
use App\Http\Middleware\EnsureSiteModuleEnabled;
use Bjanczak\FilamentFlexFields\FilamentFlexFieldsPlugin;
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

class WorkerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('worker')
            ->path('worker')
            ->login()
            ->authGuard('worker')
            ->brandName('Munkatársi panel')
            ->colors(['primary' => Color::Teal])
            ->font('Karla')
            ->spa()
            ->resources([
                WorkerAppointmentResource::class,
                WorkerPackageResource::class,
            ])
            ->pages([
                WorkerDashboard::class,
                WorkerProfileSettings::class,
                WorkerGoogleCalendarSettings::class,
            ])
            ->widgets([
                AccountWidget::class,
                WorkerTodayAppointmentsWidget::class,
                WorkerAppointmentCalendarWidget::class,
            ])
            ->homeUrl(fn (): string => WorkerDashboard::getUrl())
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
                EnsureSiteModuleEnabled::class.':appointment',
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentFlexFieldsPlugin::make(),
            ]);
    }
}
