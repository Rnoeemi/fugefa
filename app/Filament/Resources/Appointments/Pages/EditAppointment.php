<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Services\GoogleCalendarSyncService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (Appointment $record): void {
                    app(GoogleCalendarSyncService::class)->deleteAppointment($record->load('worker'));
                }),
        ];
    }

    protected function afterSave(): void
    {
        /** @var Appointment $record */
        $record = $this->record;
        app(GoogleCalendarSyncService::class)->syncAppointment($record->load('worker'));
    }
}
