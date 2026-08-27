<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithBookingCalendar;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BookingCalendar extends Page
{
    use InteractsWithBookingCalendar;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    protected static ?string $navigationLabel = 'Naptár';

    protected static ?string $title = 'Foglalási naptár';

    protected static string|UnitEnum|null $navigationGroup = 'Foglalások';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'booking-calendar';

    protected string $view = 'filament.pages.booking-calendar';

    public function mount(): void
    {
        $this->mountInteractsWithBookingCalendar();
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return BookingResource::canViewAny();
    }
}
