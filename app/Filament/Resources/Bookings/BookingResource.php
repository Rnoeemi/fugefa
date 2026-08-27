<?php

namespace App\Filament\Resources\Bookings;

use App\Enums\SiteModule;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Filament\Resources\Bookings\Tables\BookingsTable;
use App\Models\Booking;
use App\Services\ModuleService;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BookingResource extends BaseResource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Foglalások';

    protected static ?string $modelLabel = 'foglalás';

    protected static ?string $pluralModelLabel = 'Foglalások';

    protected static string|UnitEnum|null $navigationGroup = 'Foglalások';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewAny(): bool
    {
        if (! app(ModuleService::class)->isEnabled(SiteModule::Accommodation)) {
            return false;
        }

        return parent::canViewAny();
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        if (! $record instanceof Booking) {
            return parent::getRecordTitle($record);
        }

        $guest = $record->guest?->name ?? 'Vendég';
        $dates = $record->check_in?->format('Y.m.d') . ' – ' . $record->check_out?->format('Y.m.d');

        return "{$guest} ({$dates})";
    }

    public static function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
