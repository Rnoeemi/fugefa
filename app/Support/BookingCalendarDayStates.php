<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Booking;
use Illuminate\Support\Carbon;

class BookingCalendarDayStates
{
    public const PAST = 'past';

    public const FREE = 'free';

    public const OCCUPIED = 'occupied';

  /** Foglalás/zárás első napja – bal felső zöld, jobb alsó piros. */
    public const TURNOVER_OUT = 'turnover-out';

  /** Foglalás/zárás utolsó napja – bal felső piros, jobb alsó zöld. */
    public const TURNOVER_IN = 'turnover-in';

  /** Átfedő érkezés és távozás ugyanazon a napon. */
    public const COLLISION = 'collision';

    /**
     * @return array<string, string>
     */
    public function statesFor(Accommodation $accommodation, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->startOfDay();
        $today = now()->startOfDay();

        $periods = Booking::query()
            ->where('accommodation_id', $accommodation->id)
            ->whereNull('bed_id')
            ->blocking()
            ->where('check_out', '>', $from)
            ->where('check_in', '<', $to)
            ->get(['check_in', 'check_out']);

        $checkInDays = [];
        $checkOutDays = [];
        $occupiedNights = [];

        foreach ($periods as $booking) {
            $checkIn = $booking->check_in->copy()->startOfDay();
            $checkOut = $booking->check_out->copy()->startOfDay();

            $checkInDays[$checkIn->toDateString()] = true;
            $checkOutDays[$checkOut->toDateString()] = true;

            $cursor = $checkIn->copy();

            while ($cursor->lt($checkOut)) {
                $occupiedNights[$cursor->toDateString()] = true;
                $cursor->addDay();
            }
        }

        $states = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $iso = $cursor->toDateString();

            $states[$iso] = $this->classifyDay(
                $cursor,
                $today,
                isset($checkInDays[$iso]),
                isset($checkOutDays[$iso]),
                isset($occupiedNights[$iso]),
            );

            $cursor->addDay();
        }

        return $states;
    }

    protected function classifyDay(
        Carbon $day,
        Carbon $today,
        bool $isCheckInDay,
        bool $isCheckOutDay,
        bool $isOccupiedNight,
    ): string {
        if ($day->lt($today)) {
            return self::PAST;
        }

        if ($isCheckInDay && $isCheckOutDay) {
            return self::COLLISION;
        }

        if ($isCheckOutDay && ! $isOccupiedNight) {
            return self::TURNOVER_IN;
        }

        if ($isCheckInDay && $isOccupiedNight) {
            return self::TURNOVER_OUT;
        }

        if ($isOccupiedNight) {
            return self::OCCUPIED;
        }

        return self::FREE;
    }
}
