<?php

namespace App\Filament\Worker\Widgets;

use App\Filament\Worker\Concerns\InteractsWithWorkerAppointmentCalendar;
use App\Models\Worker;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class WorkerAppointmentCalendarWidget extends Widget
{
    use InteractsWithWorkerAppointmentCalendar;

    protected static ?int $sort = 20;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected string $view = 'filament.worker.widgets.appointment-calendar-widget';

    public function mount(): void
    {
        $this->mountInteractsWithWorkerAppointmentCalendar();
    }

    public static function canView(): bool
    {
        return Filament::auth()->user() instanceof Worker;
    }
}
