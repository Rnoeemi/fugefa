<?php

namespace App\Filament\Worker\Resources\Packages\Pages;

use App\Filament\Worker\Resources\Packages\WorkerPackageResource;
use App\Models\Worker;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListWorkerPackages extends ListRecords
{
    protected static string $resource = WorkerPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
