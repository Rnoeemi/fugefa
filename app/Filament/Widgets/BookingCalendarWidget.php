<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithBookingCalendar;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class BookingCalendarWidget extends Widget
{
    use InteractsWithBookingCalendar;

    protected static ?int $sort = 10;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.booking-calendar-widget';

    public function mount(): void
    {
        $this->mountInteractsWithBookingCalendar();
    }

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return BookingResource::canViewAny();
    }
}
