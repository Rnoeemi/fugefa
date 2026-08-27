<?php

namespace App\Filament\Widgets;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -28;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Foglalások';

    protected ?string $description = 'Státuszok, források és havi forgalom.';

    protected int | array | null $columns = [
        'default' => 2,
        'md' => 3,
        'xl' => 4,
    ];

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && BookingResource::canViewAny();
    }

    protected function getStats(): array
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $byStatus = Booking::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $thisMonth = Booking::query()
            ->whereDate('check_in', '>=', $monthStart)
            ->whereDate('check_in', '<=', $monthEnd)
            ->count();

        $website = Booking::query()->where('source', BookingSource::Website)->count();
        $admin = Booking::query()->where('source', BookingSource::Admin)->count();
        $phone = Booking::query()->where('source', BookingSource::Phone)->count();

        $chart = $this->lastDaysBookingChart(14);

        return [
            Stat::make('Összes foglalás', (string) Booking::query()->count())
                ->description('Teljes állomány')
                ->descriptionIcon(Heroicon::QueueList)
                ->chart($chart)
                ->color('primary')
                ->url(BookingResource::getUrl('index')),
            Stat::make('E havi érkezés', (string) $thisMonth)
                ->description(now()->translatedFormat('Y. F'))
                ->descriptionIcon(Heroicon::Calendar)
                ->color('success')
                ->url(BookingResource::getUrl('index')),
            Stat::make(BookingStatus::Pending->getLabel(), (string) ($byStatus[BookingStatus::Pending->value] ?? 0))
                ->color('warning')
                ->url(BookingResource::getUrl('index')),
            Stat::make(BookingStatus::Confirmed->getLabel(), (string) ($byStatus[BookingStatus::Confirmed->value] ?? 0))
                ->color('success')
                ->url(BookingResource::getUrl('index')),
            Stat::make(BookingStatus::CheckedIn->getLabel(), (string) ($byStatus[BookingStatus::CheckedIn->value] ?? 0))
                ->color('info')
                ->url(BookingResource::getUrl('index')),
            Stat::make(BookingStatus::CheckedOut->getLabel(), (string) ($byStatus[BookingStatus::CheckedOut->value] ?? 0))
                ->color('gray')
                ->url(BookingResource::getUrl('index')),
            Stat::make(BookingStatus::Cancelled->getLabel(), (string) ($byStatus[BookingStatus::Cancelled->value] ?? 0))
                ->color('danger')
                ->url(BookingResource::getUrl('index')),
            Stat::make('Forrás', "Web {$website} · Admin {$admin} · Tel {$phone}")
                ->description('Foglalási csatornák')
                ->descriptionIcon(Heroicon::GlobeAlt)
                ->color('gray'),
        ];
    }

    /**
     * @return list<int|float>
     */
    protected function lastDaysBookingChart(int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Booking::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, count(*) as aggregate')
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $chart = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $chart[] = (int) ($rows[$day] ?? 0);
        }

        return $chart;
    }
}
