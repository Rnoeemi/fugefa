<?php

namespace App\Filament\Resources\Guests\Schemas;

use App\Enums\GuestStatus;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make('basic')
                            ->label('Alapadatok')
                            ->columns(3)
                            ->schema([
                                FlexTextInput::make('name')
                                    ->label('Név')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                SelectField::make('status')
                                    ->label('Státusz')
                                    ->options(GuestStatus::class)
                                    ->enum(GuestStatus::class)
                                    ->required()
                                    ->default(GuestStatus::Welcome)
                                    ->searchable()
                                    ->preload()
                                    ->helperText(function (mixed $state): ?string {
                                        if ($state instanceof GuestStatus) {
                                            return $state->getDescription();
                                        }

                                        return filled($state)
                                            ? GuestStatus::tryFrom((string) $state)?->getDescription()
                                            : null;
                                    })
                                    ->live()
                                    ->columnSpan(1),
                                FlexTextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                PhoneField::make('phone')
                                    ->label('Telefon')
                                    ->defaultCountry('HU')
                                    ->columnSpan(1),
                                FlexTextInput::make('id_number')
                                    ->label('Igazolványszám')
                                    ->maxLength(64)
                                    ->columnSpan(1),
                            ]),
                        Tab::make('notes')
                            ->label('Belső megjegyzés')
                            ->schema([
                                FlexTextareaField::make('internal_note')
                                    ->label('Rövid belső megjegyzés')
                                    ->helperText('Általános megjegyzés a vendég profilján. Részletes jelentések a „Jegyzetek” fülön.')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
