<?php

namespace App\Filament\Resources\PaymentImplementations;

use App\Enums\SiteModule;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\PaymentImplementations\Pages\EditPaymentImplementation;
use App\Filament\Resources\PaymentImplementations\Pages\ListPaymentImplementations;
use App\Filament\Resources\PaymentImplementations\Schemas\PaymentImplementationForm;
use App\Filament\Resources\PaymentImplementations\Tables\PaymentImplementationsTable;
use App\Models\PaymentImplementation;
use App\Services\ModuleService;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PaymentImplementationResource extends BaseResource
{
    protected static ?string $model = PaymentImplementation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CreditCard;

    protected static ?string $slug = 'implementations';

    protected static ?string $navigationLabel = 'Implementációk';

    protected static ?string $modelLabel = 'fizetési implementáció';

    protected static ?string $pluralModelLabel = 'Implementációk';

    protected static string|UnitEnum|null $navigationGroup = 'Adminisztráció';

    protected static ?int $navigationSort = 70;

    protected static ?string $recordTitleAttribute = 'label';

    public static function canViewAny(): bool
    {
        $modules = app(ModuleService::class);

        if (! $modules->paymentEnabled() && ! $modules->accommodationEnabled()) {
            return false;
        }

        return parent::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        if (! app(ModuleService::class)->paymentEnabled()) {
            return false;
        }

        return parent::canEdit($record);
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentImplementationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentImplementationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentImplementations::route('/'),
            'edit' => EditPaymentImplementation::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
