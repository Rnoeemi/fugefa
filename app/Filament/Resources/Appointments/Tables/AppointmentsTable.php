<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Enums\AppointmentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Kezdés')
                    ->dateTime('Y.m.d. H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Vége')
                    ->dateTime('H:i')
                    ->sortable(),
                TextColumn::make('worker.name')
                    ->label('Munkatárs')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Ügyfél')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('E-mail')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Státusz')
                    ->options(AppointmentStatus::class),
                SelectFilter::make('worker_id')
                    ->label('Munkatárs')
                    ->relationship('worker', 'name'),
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
