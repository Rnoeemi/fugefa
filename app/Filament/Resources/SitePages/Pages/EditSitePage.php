<?php

namespace App\Filament\Resources\SitePages\Pages;

use App\Filament\Resources\SitePages\SitePageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('builder')
                ->label('Vizuális szerkesztő')
                ->icon(Heroicon::PaintBrush)
                ->url(route('site-builder.page', ['page' => $this->getRecord()]))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
