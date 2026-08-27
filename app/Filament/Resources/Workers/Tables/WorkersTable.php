<?php

namespace App\Filament\Resources\Workers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Beosztás')
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('slot_duration_minutes')
                    ->label('Időtartam')
                    ->suffix(' perc')
                    ->alignEnd(),
                IconColumn::make('google_calendar_enabled')
                    ->label('Google')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktív')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Sorrend')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
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
