<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Services\BookingMailService;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    public function mount(): void
    {
        parent::mount();

        $guestId = request()->query('guest_id');

        if (filled($guestId)) {
            $this->form->fill([
                'guest_id' => (int) $guestId,
                'status' => BookingStatus::Confirmed,
                'source' => BookingSource::Admin,
                'guests_count' => 1,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] ??= BookingSource::Admin->value;
        $data['created_by'] = auth()->id();

        $status = $data['status'] ?? null;
        $statusValue = $status instanceof BookingStatus ? $status->value : $status;

        if ($statusValue === BookingStatus::Confirmed->value) {
            $data['confirmed_at'] ??= now();
        }

        return BookingForm::validateAvailability($data, enforceMinNights: false);
    }

    protected function afterCreate(): void
    {
        $mailer = app(BookingMailService::class);
        $mailer->sendReceived($this->record);

        if ($this->record->status === BookingStatus::Confirmed) {
            $mailer->sendConfirmed($this->record);
        }

        app(\App\Services\BookingNotificationService::class)->notifyNewBooking($this->record);
    }
}
