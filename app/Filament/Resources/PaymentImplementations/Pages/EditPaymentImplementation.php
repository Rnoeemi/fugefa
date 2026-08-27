<?php

namespace App\Filament\Resources\PaymentImplementations\Pages;

use App\Filament\Resources\PaymentImplementations\PaymentImplementationResource;
use App\Models\PaymentImplementation;
use Filament\Resources\Pages\EditRecord;

class EditPaymentImplementation extends EditRecord
{
    protected static string $resource = PaymentImplementationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['demo_info'] = $this->record->provider->demoInfo();

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var PaymentImplementation $record */
        $record = $this->record;

        if ($record->is_default) {
            $record->makeSoleDefault();
        }
    }
}
