<?php

namespace App\Filament\Resources\PaymentImplementations\Pages;

use App\Filament\Resources\PaymentImplementations\PaymentImplementationResource;
use App\Services\ModuleService;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;

class ListPaymentImplementations extends ListRecords
{
    protected static string $resource = PaymentImplementationResource::class;

    public function content(Schema $schema): Schema
    {
        $modules = app(ModuleService::class);
        $tabs = [];

        if ($modules->paymentEnabled()) {
            $tabs[] = Tab::make('payments')
                ->label('Fizetési módok')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    EmbeddedTable::make(),
                ]);

            $tabs[] = Tab::make('invoicing')
                ->label('Számlázás')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Text::make('A számlázási beállítások hamarosan elérhetők.')
                        ->color('gray'),
                ]);
        }

        if ($modules->accommodationEnabled()) {
            $tabs[] = Tab::make('ntak')
                ->label('NTAK')
                ->icon('heroicon-o-building-library')
                ->schema([
                    Text::make('Az NTAK adatközlés beállításai hamarosan elérhetők.')
                        ->color('gray'),
                ]);
        }

        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                Tabs::make('implementationTabs')
                    ->persistTabInQueryString('tab')
                    ->tabs($tabs),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
