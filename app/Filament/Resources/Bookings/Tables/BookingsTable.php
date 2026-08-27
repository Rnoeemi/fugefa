<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->sortable(),
                TextColumn::make('accommodation.name')
                    ->label('Szállás')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guest.name')
                    ->label('Vendég')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->guest?->status?->getLabel()),
                TextColumn::make('check_in')
                    ->label('Érkezés')
                    ->date('Y.m.d.')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label('Távozás')
                    ->date('Y.m.d.')
                    ->sortable(),
                TextColumn::make('guests_count')
                    ->label('Fő')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('source')
                    ->label('Forrás')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('total_price')
                    ->label('Összeg')
                    ->money('HUF', locale: 'hu')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('check_in', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Státusz')
                    ->options(BookingStatus::class),
                SelectFilter::make('source')
                    ->label('Forrás')
                    ->options(BookingSource::class),
                SelectFilter::make('accommodation_id')
                    ->label('Szállás')
                    ->relationship('accommodation', 'name'),
                Filter::make('period')
                    ->label('Időszak')
                    ->schema([
                        FlexDatePicker::make('from')
                            ->label('Ettől'),
                        FlexDatePicker::make('until')
                            ->label('Eddig'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('check_out', '>', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('check_in', '<', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
