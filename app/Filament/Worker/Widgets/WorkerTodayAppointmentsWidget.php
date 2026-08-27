<?php

namespace App\Filament\Worker\Widgets;

use App\Models\Appointment;
use App\Models\Worker;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class WorkerTodayAppointmentsWidget extends Widget
{
    protected static ?int $sort = 10;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected string $view = 'filament.worker.widgets.today-appointments-widget';

    public function todayAppointments()
    {
        $worker = Filament::auth()->user();

        if (! $worker instanceof Worker) {
            return collect();
        }

        return Appointment::query()
            ->where('worker_id', $worker->id)
            ->whereDate('starts_at', Carbon::today())
            ->orderBy('starts_at')
            ->get();
    }

    public static function canView(): bool
    {
        return Filament::auth()->user() instanceof Worker;
    }
}
