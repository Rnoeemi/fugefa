<?php

namespace App\Filament\Worker\Resources\Packages\Pages;

use App\Filament\Worker\Resources\Packages\WorkerPackageResource;
use App\Models\Worker;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkerPackage extends CreateRecord
{
    protected static string $resource = WorkerPackageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $worker = Filament::auth()->user();
        abort_unless($worker instanceof Worker, 403);

        $data['worker_id'] = $worker->id;
        $data['duration_minutes'] = max(15, (int) round(((int) $data['duration_minutes']) / 15) * 15);

        return $data;
    }
}
