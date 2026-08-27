<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Filament\Pages\BookingCalendar;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OperationalStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -30;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Mai üzemeltetés';

    protected ?string $description = 'Érkezések, távozások és aktuális foglalások.';

    protected int | array | null $columns = [
        'default' => 2,
        'md' => 3,
        'xl' => 6,
    ];

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && BookingResource::canViewAny();
    }

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $arrivalsToday = Booking::query()->blocking()->whereDate('check_in', $today)->count();
        $departuresToday = Booking::query()->blocking()->whereDate('check_out', $today)->count();
        $arrivalsTomorrow = Booking::query()->blocking()->whereDate('check_in', $tomorrow)->count();
        $staying = Booking::query()->where('status', BookingStatus::CheckedIn)->count();
        $pending = Booking::query()->where('status', BookingStatus::Pending)->count();
        $active = Booking::query()->blocking()->count();

        return [
            Stat::make('Mai érkezés', (string) $arrivalsToday)
                ->description('Mai napra check-in')
                ->descriptionIcon(Heroicon::ArrowRightEndOnRectangle)
                ->color('success')
                ->url(BookingCalendar::getUrl()),
            Stat::make('Mai távozás', (string) $departuresToday)
                ->description('Mai napra check-out')
                ->descriptionIcon(Heroicon::ArrowLeftStartOnRectangle)
                ->color('warning')
                ->url(BookingCalendar::getUrl()),
            Stat::make('Holnapi érkezés', (string) $arrivalsTomorrow)
                ->description('Holnapra check-in')
                ->descriptionIcon(Heroicon::CalendarDays)
                ->color('info')
                ->url(BookingCalendar::getUrl(['month' => Carbon::parse($tomorrow)->format('Y-m')])),
            Stat::make('Bentlakók', (string) $staying)
                ->description('Bejelentkezett vendégek')
                ->descriptionIcon(Heroicon::Home)
                ->color('primary')
                ->url(BookingResource::getUrl('index')),
            Stat::make('Függőben', (string) $pending)
                ->description('Megerősítésre vár')
                ->descriptionIcon(Heroicon::Clock)
                ->color($pending > 0 ? 'danger' : 'gray')
                ->url(BookingResource::getUrl('index')),
            Stat::make('Aktív foglalások', (string) $active)
                ->description('Blokkoló státuszok')
                ->descriptionIcon(Heroicon::Bookmark)
                ->color('gray')
                ->url(BookingResource::getUrl('index')),
        ];
    }
}
