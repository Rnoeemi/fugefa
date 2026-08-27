<?php

namespace App\Filament\Worker\Pages;

use App\Filament\Worker\Widgets\WorkerAppointmentCalendarWidget;
use App\Filament\Worker\Widgets\WorkerTodayAppointmentsWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;

class WorkerDashboard extends Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static ?string $navigationLabel = 'Áttekintés';

    protected static ?string $title = 'Áttekintés';

    protected static ?int $navigationSort = 1;

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            WorkerTodayAppointmentsWidget::class,
            WorkerAppointmentCalendarWidget::class,
        ];
    }
}
