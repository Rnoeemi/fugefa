<?php

namespace App\Filament\Reception\Pages;

use App\Models\Booking;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class ReceptionDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?string $navigationLabel = 'KIOSK';

    protected static ?string $title = 'Recepció – mai forgalom';

    protected static ?string $slug = 'kiosk';

    protected string $view = 'filament.reception.pages.dashboard';

    public function arrivalsToday()
    {
        return Booking::query()
            ->with(['guest', 'accommodation', 'bed.room'])
            ->whereDate('check_in', Carbon::today())
            ->blocking()
            ->orderBy('check_in')
            ->get();
    }

    public function departuresToday()
    {
        return Booking::query()
            ->with(['guest', 'accommodation', 'bed.room'])
            ->whereDate('check_out', Carbon::today())
            ->orderBy('check_out')
            ->get();
    }

    public function inHouse()
    {
        $today = Carbon::today();

        return Booking::query()
            ->with(['guest', 'accommodation', 'bed.room'])
            ->blocking()
            ->where('check_in', '<=', $today)
            ->where('check_out', '>', $today)
            ->orderBy('accommodation_id')
            ->get();
    }
}
