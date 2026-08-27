<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Worker;
use App\Services\GoogleCalendarSyncService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['ends_at'] ?? null) && filled($data['starts_at'] ?? null) && filled($data['worker_id'] ?? null)) {
            $worker = Worker::query()->find($data['worker_id']);
            if ($worker) {
                $data['ends_at'] = Carbon::parse($data['starts_at'])
                    ->addMinutes($worker->slot_duration_minutes)
                    ->toDateTimeString();
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Appointment $record */
        $record = $this->record;
        app(GoogleCalendarSyncService::class)->syncAppointment($record->load('worker'));
    }
}
