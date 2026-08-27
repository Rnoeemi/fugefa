<?php

namespace App\Filament\Resources\Guests;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Guests\Pages\CreateGuest;
use App\Filament\Resources\Guests\Pages\EditGuest;
use App\Filament\Resources\Guests\Pages\ListGuests;
use App\Filament\Resources\Guests\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\Guests\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Guests\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Guests\Schemas\GuestForm;
use App\Filament\Resources\Guests\Tables\GuestsTable;
use App\Models\Guest;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GuestResource extends BaseResource
{
    protected static ?string $model = Guest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'Vendégek';

    protected static ?string $modelLabel = 'vendég';

    protected static ?string $pluralModelLabel = 'Vendégek';

    protected static string|UnitEnum|null $navigationGroup = 'Vendégek';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GuestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            NotesRelationManager::class,
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuests::route('/'),
            'create' => CreateGuest::route('/create'),
            'edit' => EditGuest::route('/{record}/edit'),
        ];
    }
}
