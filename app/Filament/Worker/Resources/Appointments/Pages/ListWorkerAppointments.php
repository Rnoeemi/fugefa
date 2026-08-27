<?php

namespace App\Filament\Worker\Resources\Appointments\Pages;

use App\Filament\Worker\Resources\Appointments\WorkerAppointmentResource;
use Filament\Resources\Pages\ListRecords;

class ListWorkerAppointments extends ListRecords
{
    protected static string $resource = WorkerAppointmentResource::class;
}
