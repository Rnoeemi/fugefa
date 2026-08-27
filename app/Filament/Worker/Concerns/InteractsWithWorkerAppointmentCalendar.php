<?php

namespace App\Filament\Worker\Concerns;

use App\Enums\AppointmentStatus;
use App\Filament\Worker\Resources\Appointments\WorkerAppointmentResource;
use App\Models\Appointment;
use App\Models\Worker;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;

trait InteractsWithWorkerAppointmentCalendar
{
    #[Url]
    public ?string $month = null;

    public function mountInteractsWithWorkerAppointmentCalendar(): void
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

    protected function authenticatedWorker(): ?Worker
    {
        $worker = Filament::auth()->user();

        return $worker instanceof Worker ? $worker : null;
    }

    /**
     * @return array<int, array{date: Carbon, isCurrentMonth: bool, appointments: list<Appointment>}>
     */
    public function calendarDays(): array
    {
        $worker = $this->authenticatedWorker();
        $month = $this->currentMonth();
        $start = $month->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $appointments = $worker
            ? Appointment::query()
                ->where('worker_id', $worker->id)
                ->blocking()
                ->where('starts_at', '<=', $end)
                ->where('ends_at', '>=', $start)
                ->orderBy('starts_at')
                ->get()
            : collect();

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dayAppointments = $appointments
                ->filter(fn (Appointment $appointment): bool => $appointment->starts_at->toDateString() === $cursor->toDateString())
                ->values()
                ->all();

            $days[] = [
                'date' => $cursor->copy(),
                'isCurrentMonth' => $cursor->month === $month->month,
                'appointments' => $dayAppointments,
            ];

            $cursor->addDay();
        }

        return $days;
    }

    public function appointmentUrl(Appointment $appointment): string
    {
        return WorkerAppointmentResource::getUrl('index');
    }

    public function statusClass(AppointmentStatus $status): string
    {
        return match ($status) {
            AppointmentStatus::Pending => 'pending',
            AppointmentStatus::Confirmed => 'confirmed',
            AppointmentStatus::Completed => 'checked_in',
            default => 'default',
        };
    }
}
