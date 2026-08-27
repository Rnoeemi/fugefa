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

class PaymentRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentRules';

    protected static ?string $title = 'Előleg / előre fizetés időszakok';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FlexTextInput::make('name')->label('Megnevezés')->columnSpanFull(),
            FlexDatePicker::make('starts_on')->label('Kezdet')->required(),
            FlexDatePicker::make('ends_on')->label('Vég')->required(),
            SwitchField::make('require_deposit')->label('Előleg kötelező')->live(),
            NumberStepper::make('deposit_percent')
                ->label('Előleg %')
                ->minValue(1)
                ->maxValue(100)
                ->default(30)
                ->visible(fn ($get) => (bool) $get('require_deposit')),
            SwitchField::make('require_full_payment')->label('Teljes összeg előre'),
            SwitchField::make('is_active')->label('Aktív')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Név')->placeholder('—'),
                TextColumn::make('starts_on')->date('Y.m.d.')->label('Kezdet'),
                TextColumn::make('ends_on')->date('Y.m.d.')->label('Vég'),
                IconColumn::make('require_deposit')->boolean()->label('Előleg'),
                TextColumn::make('deposit_percent')->label('%')->placeholder('—'),
                IconColumn::make('require_full_payment')->boolean()->label('Teljes'),
                IconColumn::make('is_active')->boolean()->label('Aktív'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
