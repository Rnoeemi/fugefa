<?php

namespace App\Filament\Resources\Appointments;

use App\Enums\SiteModule;
use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Filament\Resources\BaseResource;
use App\Models\Appointment;
use App\Services\ModuleService;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AppointmentResource extends BaseResource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Időpontok';

    protected static ?string $modelLabel = 'időpont';

    protected static ?string $pluralModelLabel = 'Időpontok';

    protected static string|UnitEnum|null $navigationGroup = 'Időpontfoglaló';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'customer_name';

    public static function canViewAny(): bool
    {
        if (! app(ModuleService::class)->isEnabled(SiteModule::Appointment)) {
            return false;
        }

        return parent::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
