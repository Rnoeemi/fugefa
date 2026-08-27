<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Services\BookingMailService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // A readonly mezőket mindig a rekordból vesszük – ne lehessen kliens oldalon átírni.
        $data = array_merge($data, $this->record->only([
            'accommodation_id',
            'guest_id',
            'check_in',
            'check_out',
            'guests_count',
            'adults_count',
            'children_count',
            'source',
            'total_price',
        ]));

        $status = $data['status'] ?? null;
        $statusValue = $status instanceof BookingStatus ? $status->value : $status;
        $wasConfirmed = $this->record->status === BookingStatus::Confirmed;

        if ($statusValue === BookingStatus::Confirmed->value && blank($this->record->confirmed_at)) {
            $data['confirmed_at'] = now();
        }

        if ($statusValue === BookingStatus::Cancelled->value && blank($this->record->cancelled_at)) {
            $data['cancelled_at'] = now();
        }

        $data = BookingForm::validateAvailability($data, $this->record->id, enforceMinNights: false);

        $this->wasNewlyConfirmed = ! $wasConfirmed && $statusValue === BookingStatus::Confirmed->value;

        return $data;
    }

    protected bool $wasNewlyConfirmed = false;

    protected function afterSave(): void
    {
        if ($this->wasNewlyConfirmed) {
            app(BookingMailService::class)->sendConfirmed($this->record);
        }
    }
}
