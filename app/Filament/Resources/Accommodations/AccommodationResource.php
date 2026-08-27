<?php

namespace App\Filament\Resources\Accommodations;

use App\Filament\Resources\Accommodations\Pages\CreateAccommodation;
use App\Filament\Resources\Accommodations\Pages\EditAccommodation;
use App\Filament\Resources\Accommodations\Pages\ListAccommodations;
use App\Filament\Resources\Accommodations\RelationManagers\IcalFeedsRelationManager;
use App\Filament\Resources\Accommodations\RelationManagers\PaymentRulesRelationManager;
use App\Filament\Resources\Accommodations\RelationManagers\RatePeriodsRelationManager;
use App\Filament\Resources\Accommodations\RelationManagers\RoomsRelationManager;
use App\Filament\Resources\Accommodations\Schemas\AccommodationForm;
use App\Filament\Resources\Accommodations\Tables\AccommodationsTable;
use App\Enums\SiteModule;
use App\Filament\Resources\BaseResource;
use App\Models\Accommodation;
use App\Services\ModuleService;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AccommodationResource extends BaseResource
{
    protected static ?string $model = Accommodation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::HomeModern;

    protected static ?string $navigationLabel = 'Szállások';

    protected static ?string $modelLabel = 'szállás';

    protected static ?string $pluralModelLabel = 'Szállások';

    protected static string|UnitEnum|null $navigationGroup = 'Szálláshelyek';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        if (! app(ModuleService::class)->isEnabled(SiteModule::Accommodation)) {
            return false;
        }

        return parent::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return AccommodationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccommodationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RatePeriodsRelationManager::class,
            PaymentRulesRelationManager::class,
            RoomsRelationManager::class,
            IcalFeedsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccommodations::route('/'),
            'create' => CreateAccommodation::route('/create'),
            'edit' => EditAccommodation::route('/{record}/edit'),
        ];
    }
}
