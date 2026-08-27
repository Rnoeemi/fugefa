<?php

namespace App\Filament\Resources\Workers\Pages;

use App\Filament\Resources\Workers\WorkerResource;
use App\Support\PhoneNormalizer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorker extends EditRecord
{
    protected static string $resource = WorkerResource::class;

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
        $data['phone'] = PhoneNormalizer::toString($data['phone'] ?? null);

        $incoming = $data['google_credentials'] ?? null;
        if (is_array($incoming)) {
            $existing = $this->record->google_credentials ?? [];
            $data['google_credentials'] = array_filter([
                'client_id' => $incoming['client_id'] ?? $existing['client_id'] ?? null,
                'client_secret' => $incoming['client_secret'] ?? $existing['client_secret'] ?? null,
                'refresh_token' => $incoming['refresh_token'] ?? $existing['refresh_token'] ?? null,
                'access_token' => $existing['access_token'] ?? null,
                'access_token_expires_at' => $existing['access_token_expires_at'] ?? null,
            ], fn ($value) => filled($value)) ?: null;
        }

        return $data;
    }
}
