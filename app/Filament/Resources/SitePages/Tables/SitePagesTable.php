<?php

namespace App\Filament\Resources\SitePages\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitePagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Cím')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                IconColumn::make('is_homepage')->label('Kezdőlap')->boolean(),
                IconColumn::make('is_published')->label('Publikálva')->boolean(),
                TextColumn::make('updated_at')->label('Módosítva')->dateTime('Y.m.d. H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('builder')
                    ->label('Szerkesztő')
                    ->icon(Heroicon::PaintBrush)
                    ->url(fn ($record): string => route('site-builder.page', ['page' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
