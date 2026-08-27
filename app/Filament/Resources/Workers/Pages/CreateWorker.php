<?php

namespace App\Filament\Resources\Workers\Pages;

use App\Filament\Resources\Workers\WorkerResource;
use App\Support\PhoneNormalizer;
use Filament\Resources\Pages\CreateRecord;

class CreateWorker extends CreateRecord
{
    protected static string $resource = WorkerResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone'] = PhoneNormalizer::toString($data['phone'] ?? null);

        if (blank($data['google_credentials'] ?? null) || $data['google_credentials'] === []) {
            $data['google_credentials'] = null;
        }

        return $data;
    }
}
