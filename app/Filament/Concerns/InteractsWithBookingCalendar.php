<?php

namespace App\Filament\Concerns;

use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Accommodation;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;

trait InteractsWithBookingCalendar
{
    #[Url]
    public ?string $month = null;

    #[Url]
    public ?string $accommodationId = null;

    public function mountInteractsWithBookingCalendar(): void
    {
        $this->month ??= now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = $this->currentMonth()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->currentMonth()->addMonth()->format('Y-m');
    }

    public function goToday(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function currentMonth(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month ?? now()->format('Y-m'))->startOfMonth();
    }

    /**
     * @return array<int, array{date: Carbon, isCurrentMonth: bool, bookings: list<Booking>}>
     */
    public function calendarDays(): array
    {
        $month = $this->currentMonth();
        $start = $month->copy()->startOfWeek(Carbon::MONDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $bookings = Booking::query()
            ->with(['guest', 'accommodation'])
            ->blocking()
            ->where('check_in', '<=', $end)
            ->where('check_out', '>', $start)
            ->when(
                filled($this->accommodationId),
                fn ($query) => $query->where('accommodation_id', $this->accommodationId),
            )
            ->orderBy('check_in')
            ->get();

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dayBookings = $bookings
                ->filter(fn (Booking $booking): bool => $booking->check_in->lte($cursor) && $booking->check_out->gt($cursor))
                ->values()
                ->all();

            $days[] = [
                'date' => $cursor->copy(),
                'isCurrentMonth' => $cursor->month === $month->month,
                'bookings' => $dayBookings,
            ];

            $cursor->addDay();
        }

        return $days;
    }

    /**
     * @return array<int|string, string>
     */
    public function accommodations(): array
    {
        return Accommodation::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function bookingUrl(Booking $booking): string
    {
        return BookingResource::getUrl('edit', ['record' => $booking]);
    }

    public function statusClass(BookingStatus $status): string
    {
        return match ($status) {
            BookingStatus::Pending => 'pending',
            BookingStatus::Confirmed => 'confirmed',
            BookingStatus::CheckedIn => 'checked_in',
            default => 'default',
        };
    }
}
