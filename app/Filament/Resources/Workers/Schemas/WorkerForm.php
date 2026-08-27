<?php

namespace App\Filament\Resources\Workers\Schemas;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class WorkerForm
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
                            ->columns(2)
                            ->schema([
                                FlexTextInput::make('name')
                                    ->label('Név')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, callable $set, ?string $old, mixed $get): void {
                                        if (blank($get('slug')) || $get('slug') === \Illuminate\Support\Str::slug((string) $old)) {
                                            $set('slug', \Illuminate\Support\Str::slug((string) $state));
                                        }
                                    }),
                                FlexTextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                FlexTextInput::make('title')
                                    ->label('Beosztás'),
                                PhoneField::make('phone')
                                    ->label('Telefon')
                                    ->defaultCountry('HU'),
                                FlexTextareaField::make('bio')
                                    ->label('Bemutatkozás')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                FlexTextInput::make('work_starts_at')
                                    ->label('Munkaidő kezdete')
                                    ->type('time')
                                    ->required()
                                    ->default('09:00'),
                                FlexTextInput::make('work_ends_at')
                                    ->label('Munkaidő vége')
                                    ->type('time')
                                    ->required()
                                    ->default('17:00'),
                                FlexTextInput::make('slot_duration_minutes')
                                    ->label('Alapértelmezett időpont hossz (perc)')
                                    ->helperText('Csomag hiányában használt tartalék érték. A csomagok a munkatárs panelen állíthatók.')
                                    ->numeric()
                                    ->required()
                                    ->default(60)
                                    ->minValue(15)
                                    ->maxValue(480),
                                FlexTextInput::make('sort_order')
                                    ->label('Sorrend')
                                    ->numeric()
                                    ->default(0),
                                SwitchField::make('is_active')
                                    ->label('Aktív')
                                    ->default(true),
                            ]),
                        Tab::make('login')
                            ->label('Bejelentkezés')
                            ->columns(2)
                            ->schema([
                                FlexTextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                FlexTextInput::make('password')
                                    ->label('Jelszó')
                                    ->password()
                                    ->revealable()
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create'),
                            ]),
                        Tab::make('google')
                            ->label('Google Naptár')
                            ->schema([
                                Section::make('Szinkronizálás')
                                    ->description('A munkatárs időpontjai a megadott Google Naptárba kerülnek, ha a szinkron be van kapcsolva.')
                                    ->columns(2)
                                    ->schema([
                                        SwitchField::make('google_calendar_enabled')
                                            ->label('Google Naptár szinkron')
                                            ->columnSpanFull(),
                                        FlexTextInput::make('google_calendar_id')
                                            ->label('Naptár ID')
                                            ->helperText('Általában az e-mail cím, vagy a naptár beállításaiban látható azonosító.')
                                            ->columnSpanFull(),
                                        FlexTextInput::make('google_credentials.client_id')
                                            ->label('Client ID')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(fn (?string $state): bool => filled($state)),
                                        FlexTextInput::make('google_credentials.client_secret')
                                            ->label('Client secret')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(fn (?string $state): bool => filled($state)),
                                        FlexTextInput::make('google_credentials.refresh_token')
                                            ->label('Refresh token')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(fn (?string $state): bool => filled($state))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
