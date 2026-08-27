<?php

namespace App\Filament\Resources\Accommodations\RelationManagers;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RatePeriodsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratePeriods';

    protected static ?string $title = 'Időszaki árak / szabályok';

    protected static ?string $modelLabel = 'időszaki szabály';

    protected static ?string $pluralModelLabel = 'időszaki szabályok';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FlexTextInput::make('name')
                    ->label('Megnevezés')
                    ->placeholder('Pl. Főszezon, Húsvét')
                    ->columnSpanFull(),
                FlexDatePicker::make('starts_on')
                    ->label('Kezdete')
                    ->required(),
                FlexDatePicker::make('ends_on')
                    ->label('Vége')
                    ->required(),
                FlexTextInput::make('nightly_price')
                    ->label('Éjszakánkénti ár')
                    ->numeric()
                    ->suffix('Ft')
                    ->helperText('Üresen: az alapár marad.'),
                FlexTextInput::make('ifa_per_person_night')
                    ->label('IFA / fő / éj')
                    ->numeric()
                    ->suffix('Ft')
                    ->helperText('Üresen: az alap IFA marad.'),
                NumberStepper::make('min_nights')
                    ->label('Min. éjszakák (web)')
                    ->minValue(1)
                    ->maxValue(30)
                    ->helperText('Üresen: az alap minimum. Az admin panel ezt nem kényszeríti.'),
                SwitchField::make('is_active')
                    ->label('Aktív')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Név')
                    ->placeholder('—'),
                TextColumn::make('starts_on')
                    ->label('Kezdet')
                    ->date('Y.m.d.'),
                TextColumn::make('ends_on')
                    ->label('Vég')
                    ->date('Y.m.d.'),
                TextColumn::make('nightly_price')
                    ->label('Ár/éj')
                    ->money('HUF', locale: 'hu')
                    ->placeholder('alap'),
                TextColumn::make('ifa_per_person_night')
                    ->label('IFA')
                    ->money('HUF', locale: 'hu')
                    ->placeholder('alap'),
                TextColumn::make('min_nights')
                    ->label('Min. éj')
                    ->placeholder('alap'),
                IconColumn::make('is_active')
                    ->label('Aktív')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
