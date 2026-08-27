<?php

namespace App\Services;

use App\Enums\AccommodationType;
use App\Enums\BookingSource;
use App\Enums\GuestStatus;
use App\Models\Accommodation;
use App\Models\Bed;
use App\Models\Booking;
use App\Models\Guest;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    public function __construct(
        protected PricingService $pricing,
    ) {}

    public function isAvailable(
        Accommodation $accommodation,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        ?int $ignoreBookingId = null,
        ?int $bedId = null,
    ): bool {
        if ($bedId) {
            return ! $this->bedOverlappingQuery($bedId, $checkIn, $checkOut, $ignoreBookingId)->exists();
        }

        // Munkásszállás ágy nélkül: ha van szabad ágy, engedjük
        if ($accommodation->type === AccommodationType::WorkersLodging) {
            return $this->hasFreeBed($accommodation, $checkIn, $checkOut, $ignoreBookingId);
        }

        return ! $this->overlappingQuery($accommodation, $checkIn, $checkOut, $ignoreBookingId)->exists();
    }

    public function hasFreeBed(
        Accommodation $accommodation,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        ?int $ignoreBookingId = null,
    ): bool {
        return $this->firstAvailableBed($accommodation, $checkIn, $checkOut, $ignoreBookingId) !== null;
    }

    public function firstAvailableBed(
        Accommodation $accommodation,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        ?int $ignoreBookingId = null,
    ): ?Bed {
        $beds = Bed::query()
            ->whereHas('room', fn ($q) => $q->where('accommodation_id', $accommodation->id)->where('is_active', true))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($beds as $bed) {
            if (! $this->bedOverlappingQuery($bed->id, $checkIn, $checkOut, $ignoreBookingId)->exists()) {
                return $bed;
            }
        }

        return null;
    }

    /**
     * @return list<array{date: string, status: string}>
     */
    public function occupiedDates(
        Accommodation $accommodation,
        CarbonInterface|string $from,
        CarbonInterface|string $to,
        ?int $bedId = null,
    ): array {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->startOfDay();

        $query = $bedId
            ? $this->bedOverlappingQuery($bedId, $from, $to)
            : $this->overlappingQuery($accommodation, $from, $to);

        // Munkásszállásnál csak akkor jelöljük foglaltnak a napot, ha minden ágy foglalt
        if (! $bedId && $accommodation->type === AccommodationType::WorkersLodging) {
            return $this->fullyOccupiedWorkerDates($accommodation, $from, $to);
        }

        $dates = [];

        foreach ($query->get() as $booking) {
            $cursor = $booking->check_in->copy()->max($from);
            $end = $booking->check_out->copy()->min($to);

            while ($cursor->lt($end)) {
                $dates[$cursor->toDateString()] = [
                    'date' => $cursor->toDateString(),
                    'status' => $booking->status->value,
                ];
                $cursor->addDay();
            }
        }

        ksort($dates);

        return array_values($dates);
    }

    public function assertCanBook(
        Accommodation $accommodation,
        Guest $guest,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        int $guestsCount,
        BookingSource $source,
        ?int $ignoreBookingId = null,
        bool $enforceMinNights = true,
        ?int $bedId = null,
        ?int $adultsCount = null,
    ): void {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();
        $nights = (int) $checkIn->diffInDays($checkOut);

        if ($checkOut->lte($checkIn)) {
            throw ValidationException::withMessages([
                'check_out' => 'A távozás napjának későbbinek kell lennie az érkezésnél.',
            ]);
        }

        if ($guestsCount < 1) {
            throw ValidationException::withMessages([
                'guests_count' => 'Legalább egy vendég megadása kötelező.',
            ]);
        }

        if ($adultsCount !== null && $adultsCount > $guestsCount) {
            throw ValidationException::withMessages([
                'adults_count' => 'A felnőttek száma nem lehet több a vendégek számánál.',
            ]);
        }

        if ($guestsCount > $accommodation->capacity && $accommodation->type !== AccommodationType::WorkersLodging) {
            throw ValidationException::withMessages([
                'guests_count' => "A szállás maximális férőhelye: {$accommodation->capacity} fő.",
            ]);
        }

        if (! $accommodation->is_active) {
            throw ValidationException::withMessages([
                'accommodation_id' => 'Ez a szállás jelenleg nem foglalható.',
            ]);
        }

        if ($guest->status === GuestStatus::Blacklisted) {
            throw ValidationException::withMessages([
                'guest_id' => 'Tiltólistás vendégnek nem rögzíthető foglalás.',
            ]);
        }

        if (
            $accommodation->type === AccommodationType::WorkersLodging
            && $source === BookingSource::Website
        ) {
            throw ValidationException::withMessages([
                'accommodation_id' => 'Munkásszállás csak adminisztrátoron keresztül foglalható.',
            ]);
        }

        if ($enforceMinNights) {
            $minNights = $this->pricing->minNightsFor($accommodation, $checkIn);

            if ($nights < $minNights) {
                throw ValidationException::withMessages([
                    'check_out' => "Erre az időszakra a minimális foglalható éjszakák száma: {$minNights}.",
                ]);
            }
        }

        if ($bedId) {
            $bed = Bed::query()->with('room')->find($bedId);

            if (! $bed || $bed->room?->accommodation_id !== $accommodation->id) {
                throw ValidationException::withMessages([
                    'bed_id' => 'Érvénytelen ágy a választott szálláshoz.',
                ]);
            }

            if (! $this->isAvailable($accommodation, $checkIn, $checkOut, $ignoreBookingId, $bedId)) {
                throw ValidationException::withMessages([
                    'bed_id' => 'A választott ágy a megadott időszakban foglalt.',
                ]);
            }

            return;
        }

        if (! $this->isAvailable($accommodation, $checkIn, $checkOut, $ignoreBookingId)) {
            throw ValidationException::withMessages([
                'check_in' => $accommodation->type === AccommodationType::WorkersLodging
                    ? 'Nincs szabad ágy a választott időszakban.'
                    : 'A választott időszakban a szállás már foglalt.',
            ]);
        }
    }

    /**
     * @return list<array{date: string, status: string}>
     */
    protected function fullyOccupiedWorkerDates(Accommodation $accommodation, Carbon $from, Carbon $to): array
    {
        $beds = Bed::query()
            ->whereHas('room', fn ($q) => $q->where('accommodation_id', $accommodation->id)->where('is_active', true))
            ->where('is_active', true)
            ->pluck('id');

        if ($beds->isEmpty()) {
            return [];
        }

        $dates = [];
        $cursor = $from->copy();

        while ($cursor->lt($to)) {
            $free = false;

            foreach ($beds as $bedId) {
                if (! $this->bedOverlappingQuery($bedId, $cursor, $cursor->copy()->addDay())->exists()) {
                    $free = true;
                    break;
                }
            }

            if (! $free) {
                $dates[$cursor->toDateString()] = [
                    'date' => $cursor->toDateString(),
                    'status' => 'full',
                ];
            }

            $cursor->addDay();
        }

        return array_values($dates);
    }

    protected function overlappingQuery(
        Accommodation $accommodation,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        ?int $ignoreBookingId = null,
    ): \Illuminate\Database\Eloquent\Builder {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        return Booking::query()
            ->where('accommodation_id', $accommodation->id)
            ->whereNull('bed_id')
            ->blocking()
            ->overlapping($checkIn, $checkOut)
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId));
    }

    protected function bedOverlappingQuery(
        int $bedId,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        ?int $ignoreBookingId = null,
    ): \Illuminate\Database\Eloquent\Builder {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        return Booking::query()
            ->where('bed_id', $bedId)
            ->blocking()
            ->overlapping($checkIn, $checkOut)
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId));
    }
}
