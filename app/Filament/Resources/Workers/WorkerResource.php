<?php

namespace App\Filament\Resources\Workers;

use App\Enums\SiteModule;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Workers\Pages\CreateWorker;
use App\Filament\Resources\Workers\Pages\EditWorker;
use App\Filament\Resources\Workers\Pages\ListWorkers;
use App\Filament\Resources\Workers\Schemas\WorkerForm;
use App\Filament\Resources\Workers\Tables\WorkersTable;
use App\Models\Worker;
use App\Services\ModuleService;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WorkerResource extends BaseResource
{
    protected static ?string $model = Worker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $navigationLabel = 'Munkatársak';

    protected static ?string $modelLabel = 'munkatárs';

    protected static ?string $pluralModelLabel = 'Munkatársak';

    protected static string|UnitEnum|null $navigationGroup = 'Időpontfoglaló';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        if (! app(ModuleService::class)->isEnabled(SiteModule::Appointment)) {
            return false;
        }

        return parent::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return WorkerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'edit' => EditWorker::route('/{record}/edit'),
        ];
    }
}
