<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Worker;
use App\Models\WorkerPackage;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentBookingService
{
    public const SLOT_STEP_MINUTES = 15;

    public const TIMEZONE = 'Europe/Budapest';

    public function __construct(
        protected GoogleCalendarSyncService $googleCalendar,
    ) {}

    public function now(): Carbon
    {
        return Carbon::now(self::TIMEZONE);
    }

    /**
     * @param  array{
     *     worker_id: int,
     *     worker_package_id: int,
     *     customer_name: string,
     *     customer_email: string,
     *     customer_phone?: string|null,
     *     starts_at: string|Carbon,
     *     notes?: string|null,
     * }  $data
     */
    public function create(array $data): Appointment
    {
        $worker = Worker::query()->bookable()->findOrFail($data['worker_id']);
        $package = WorkerPackage::query()
            ->where('worker_id', $worker->id)
            ->active()
            ->findOrFail($data['worker_package_id']);

        $startsAt = Carbon::parse($data['starts_at'], self::TIMEZONE);
        $endsAt = $startsAt->copy()->addMinutes($package->duration_minutes);

        $this->assertAvailable($worker, $startsAt, $endsAt, $package->duration_minutes);

        $appointment = Appointment::query()->create([
            'worker_id' => $worker->id,
            'worker_package_id' => $package->id,
            'package_name' => $package->name,
            'duration_minutes' => $package->duration_minutes,
            'price' => $package->price,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => AppointmentStatus::Pending,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->googleCalendar->syncAppointment($appointment->fresh(['worker']));

        return $appointment;
    }

    public function assertAvailable(
        Worker $worker,
        Carbon $startsAt,
        Carbon $endsAt,
        ?int $durationMinutes = null,
        ?int $ignoreId = null,
    ): void {
        $startsAt = $startsAt->copy()->timezone(self::TIMEZONE);
        $endsAt = $endsAt->copy()->timezone(self::TIMEZONE);

        if ($startsAt->lt($this->now())) {
            throw ValidationException::withMessages([
                'starts_at' => 'Az időpont nem lehet a múltban.',
            ]);
        }

        if ($endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Érvénytelen időtartam.',
            ]);
        }

        $workStart = $worker->workStartForDate($startsAt);
        $workEnd = $worker->workEndForDate($startsAt);

        if ($startsAt->lt($workStart) || $endsAt->gt($workEnd)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Az időpont kilép a munkaidőből.',
            ]);
        }

        if ($durationMinutes !== null) {
            $aligned = $startsAt->minute % self::SLOT_STEP_MINUTES === 0
                && $startsAt->second === 0;

            if (! $aligned) {
                throw ValidationException::withMessages([
                    'starts_at' => 'Az időpont csak 15 perces lépésközzel foglalható.',
                ]);
            }
        }

        if (! $this->isRangeFree($worker, $startsAt, $endsAt, $ignoreId)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Ez az időpont már foglalt, vagy ütközik másik foglalással.',
            ]);
        }
    }

    public function isRangeFree(Worker $worker, Carbon $startsAt, Carbon $endsAt, ?int $ignoreId = null): bool
    {
        return ! Appointment::query()
            ->where('worker_id', $worker->id)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->blocking()
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }

    /**
     * 15 perces skála a naphoz. State: outside|booked|available
     *
     * @return list<array{time: string, starts_at: string, state: string, label: string}>
     */
    public function dayGrid(Worker $worker, Carbon $date, int $durationMinutes = 15): array
    {
        $now = $this->now();
        $day = $date->copy()->timezone(self::TIMEZONE)->startOfDay();
        $workStart = $worker->workStartForDate($day);
        $workEnd = $worker->workEndForDate($day);

        // Teljes nap 06:00–22:00 skála, hogy a munkaidőn kívüli sávok szürkén látszódjanak.
        $gridStart = $day->copy()->setTime(6, 0);
        $gridEnd = $day->copy()->setTime(22, 0);

        $busy = Appointment::query()
            ->where('worker_id', $worker->id)
            ->blocking()
            ->where('starts_at', '<', $gridEnd)
            ->where('ends_at', '>', $gridStart)
            ->get(['starts_at', 'ends_at']);

        $cells = [];
        $cursor = $gridStart->copy();

        while ($cursor->lt($gridEnd)) {
            $cellStart = $cursor->copy();
            $cellEnd = $cursor->copy()->addMinutes(self::SLOT_STEP_MINUTES);
            $withinWork = $cellStart->gte($workStart) && $cellEnd->lte($workEnd);
            $isPast = $cellStart->lt($now);
            $isBooked = $busy->contains(
                fn (Appointment $appointment): bool => $appointment->starts_at->copy()->timezone(self::TIMEZONE)->lt($cellEnd)
                    && $appointment->ends_at->copy()->timezone(self::TIMEZONE)->gt($cellStart)
            );

            $canStartPackage = false;
            if ($withinWork && ! $isBooked && ! $isPast && $durationMinutes > 0) {
                $packageEnd = $cellStart->copy()->addMinutes($durationMinutes);
                $canStartPackage = $packageEnd->lte($workEnd)
                    && $this->isRangeFree($worker, $cellStart, $packageEnd);
            }

            $state = match (true) {
                ! $withinWork, $isPast => 'outside',
                $isBooked => 'booked',
                default => 'available',
            };

            $cells[] = [
                'time' => $cellStart->format('H:i'),
                'starts_at' => $cellStart->format('Y-m-d\TH:i'),
                'state' => $state,
                'selectable' => $canStartPackage,
                'label' => $cellStart->format('H:i'),
            ];

            $cursor->addMinutes(self::SLOT_STEP_MINUTES);
        }

        return $cells;
    }

    /**
     * @return list<string> starts_at values that belong to the selected package block
     */
    public function selectedBlockStarts(Carbon $startsAt, int $durationMinutes): array
    {
        $values = [];
        $cursor = $startsAt->copy()->timezone(self::TIMEZONE);
        $end = $cursor->copy()->addMinutes($durationMinutes);

        while ($cursor->lt($end)) {
            $values[] = $cursor->format('Y-m-d\TH:i');
            $cursor->addMinutes(self::SLOT_STEP_MINUTES);
        }

        return $values;
    }
}
