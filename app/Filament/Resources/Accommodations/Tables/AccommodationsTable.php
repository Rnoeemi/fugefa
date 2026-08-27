<?php

namespace App\Filament\Resources\Accommodations\Tables;

use App\Enums\AccommodationType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AccommodationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Megnevezés')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Típus')
                    ->badge()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label('Férőhely')
                    ->suffix(' fő')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Aktív')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Módosítva')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('type')
                    ->label('Típus')
                    ->options(AccommodationType::class),
                TernaryFilter::make('is_active')
                    ->label('Aktív'),
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
