<?php

namespace App\Filament\Reception\Resources\Bookings;

use App\Filament\Reception\Resources\Bookings\Pages\ListReceptionBookings;
use App\Models\Booking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ReceptionBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Foglalások';

    protected static ?string $modelLabel = 'foglalás';

    protected static ?string $pluralModelLabel = 'Foglalások';

    protected static string|UnitEnum|null $navigationGroup = 'PMS';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->badge()->label('Státusz'),
                TextColumn::make('payment_status')->badge()->label('Fizetés'),
                TextColumn::make('guest.name')->label('Vendég')->searchable(),
                TextColumn::make('accommodation.name')->label('Szállás'),
                TextColumn::make('bed.name')->label('Ágy')->placeholder('—'),
                TextColumn::make('check_in')->date('Y.m.d.')->label('Érkezés')->sortable(),
                TextColumn::make('check_out')->date('Y.m.d.')->label('Távozás')->sortable(),
                TextColumn::make('guests_count')->label('Fő'),
                TextColumn::make('adults_count')->label('18+')->placeholder('—'),
                TextColumn::make('total_price')->money('HUF', locale: 'hu')->label('Összeg'),
            ])
            ->defaultSort('check_in', 'desc')
            ->filters([
                SelectFilter::make('status')->options(\App\Enums\BookingStatus::class),
                SelectFilter::make('accommodation_id')->relationship('accommodation', 'name')->label('Szállás'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceptionBookings::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
