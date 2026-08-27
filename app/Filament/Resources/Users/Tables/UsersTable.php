<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Leek\FilamentRightClick\Menu\ContextMenuItem;
use Leek\FilamentRightClick\Menu\ContextMenuSection;
use Leek\FilamentRightClick\Menu\ContextMenuSeparator;
use Bjanczak\FilamentFlexFields\Filament\Tables\Columns\UserColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->label('Jogkör')
                    ->badge(),
                TextColumn::make('name')
                    ->label('Név')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),                
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
            ->contextMenuActions([
                /*ContextMenuSection::make([
                    ContextMenuItem::for(
                        DeleteAction::make()
                            ->label('Törlés')
                            ->icon(Heroicon::Trash)
                            ->color(Color::Red)
                            ->modalDescription('Biztosan törli a felhasználót?')
                    ),
                    ContextMenuSeparator::make(),
                ]),*/
            ])
            ->filters([
                //
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
