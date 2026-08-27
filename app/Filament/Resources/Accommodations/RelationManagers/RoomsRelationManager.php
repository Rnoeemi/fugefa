<?php

namespace App\Filament\Resources\Accommodations\RelationManagers;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $title = 'Szobák / ágyak';

    protected static ?string $modelLabel = 'szoba';

    protected static ?string $pluralModelLabel = 'szobák';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FlexTextInput::make('name')->label('Szoba neve')->required(),
            FlexTextInput::make('code')->label('Kód'),
            NumberStepper::make('capacity')->label('Férőhely')->minValue(1)->maxValue(20)->default(1),
            FlexTextInput::make('ifa_per_person_night')
                ->label('IFA / fő / éj (18+)')
                ->numeric()
                ->suffix('Ft')
                ->helperText('Üresen: a szállás alap IFA-ja. Csak 18 év felettiekre.'),
            NumberStepper::make('sort_order')->label('Sorrend')->default(0)->minValue(0),
            SwitchField::make('is_active')->label('Aktív')->default(true),
            FlexTextareaField::make('notes')->label('Megjegyzés')->rows(2)->columnSpanFull(),
            FileUpload::make('images')
                ->label('Szoba képei')
                ->image()
                ->multiple()
                ->reorderable()
                ->disk('public')
                ->directory('rooms')
                ->visibility('public')
                ->panelLayout('grid')
                ->columnSpanFull(),
            Repeater::make('beds')
                ->relationship()
                ->label('Ágyak')
                ->schema([
                    FlexTextInput::make('name')->label('Ágy')->required(),
                    FlexTextInput::make('code')->label('Kód'),
                    FlexTextInput::make('bed_type')->label('Típus')->default('single'),
                    NumberStepper::make('sort_order')->label('Sorrend')->default(0)->minValue(0),
                    SwitchField::make('is_active')->label('Aktív')->default(true),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->columnSpanFull()
                ->visible(fn ($livewire): bool => (bool) $livewire->getOwnerRecord()?->supportsBeds()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Kép')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->defaultImageUrl(null),
                TextColumn::make('name')->label('Szoba')->searchable(),
                TextColumn::make('code')->label('Kód'),
                TextColumn::make('capacity')->label('Férőhely'),
                TextColumn::make('ifa_per_person_night')->label('IFA')->money('HUF', locale: 'hu')->placeholder('alap'),
                TextColumn::make('beds_count')->counts('beds')->label('Ágyak'),
                IconColumn::make('is_active')->label('Aktív')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
