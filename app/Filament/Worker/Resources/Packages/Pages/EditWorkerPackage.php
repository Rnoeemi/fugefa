<?php

namespace App\Filament\Worker\Resources\Packages\Pages;

use App\Filament\Worker\Resources\Packages\WorkerPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkerPackage extends EditRecord
{
    protected static string $resource = WorkerPackageResource::class;

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
        $data['duration_minutes'] = max(15, (int) round(((int) $data['duration_minutes']) / 15) * 15);

        return $data;
    }
}
