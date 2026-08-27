<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -26;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Pénzügyek';

    protected ?string $description = 'Bevétel, befizetések és fizetési státuszok.';

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
        $billable = Booking::query()
            ->whereNotIn('status', [
                BookingStatus::Cancelled->value,
            ]);

        $totalRevenue = (clone $billable)->sum('total_price');
        $amountPaid = (clone $billable)->sum('amount_paid');
        $accommodationTotal = (clone $billable)->sum('accommodation_total');
        $ifaTotal = (clone $billable)->sum('ifa_total');

        $monthStart = now()->startOfMonth();
        $monthRevenue = Booking::query()
            ->whereNotIn('status', [BookingStatus::Cancelled->value])
            ->where('created_at', '>=', $monthStart)
            ->sum('total_price');

        $byPayment = Booking::query()
            ->selectRaw('payment_status, count(*) as aggregate')
            ->groupBy('payment_status')
            ->pluck('aggregate', 'payment_status');

        $unpaid = (int) ($byPayment[PaymentStatus::Unpaid->value] ?? 0)
            + (int) ($byPayment[PaymentStatus::DepositDue->value] ?? 0);
        $paid = (int) ($byPayment[PaymentStatus::Paid->value] ?? 0);
        $failed = (int) ($byPayment[PaymentStatus::Failed->value] ?? 0);

        $outstanding = max(0, (float) $totalRevenue - (float) $amountPaid);

        return [
            Stat::make('Összes bevétel', $this->money($totalRevenue))
                ->description('Lemondott nélkül')
                ->descriptionIcon(Heroicon::Banknotes)
                ->color('success')
                ->url(BookingResource::getUrl('index')),
            Stat::make('Befizetve', $this->money($amountPaid))
                ->description('amount_paid összege')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('primary'),
            Stat::make('Kintlévőség', $this->money($outstanding))
                ->description('Bevétel − befizetett')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color($outstanding > 0 ? 'warning' : 'gray'),
            Stat::make('E havi bevétel', $this->money($monthRevenue))
                ->description(now()->translatedFormat('Y. F').' (létrehozás)')
                ->descriptionIcon(Heroicon::Calendar)
                ->color('info'),
            Stat::make('Szállás díj', $this->money($accommodationTotal))
                ->description('IFA nélkül')
                ->descriptionIcon(Heroicon::BuildingOffice2)
                ->color('gray'),
            Stat::make('IFA összesen', $this->money($ifaTotal))
                ->description('Idegenforgalmi adó')
                ->descriptionIcon(Heroicon::ReceiptPercent)
                ->color('gray'),
            Stat::make('Fizetetlen / előleg vár', (string) $unpaid)
                ->description('Beavatkozást igényelhet')
                ->descriptionIcon(Heroicon::Clock)
                ->color($unpaid > 0 ? 'danger' : 'gray'),
            Stat::make('Fizetve', (string) $paid)
                ->description($failed > 0 ? "Sikertelen: {$failed}" : 'Teljesített fizetések')
                ->descriptionIcon(Heroicon::CreditCard)
                ->color('success'),
        ];
    }

    protected function money(mixed $amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' Ft';
    }
}
