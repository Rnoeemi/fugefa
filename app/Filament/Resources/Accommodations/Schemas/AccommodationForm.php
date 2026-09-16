<?php

namespace App\Filament\Resources\Accommodations\Schemas;

use App\Enums\AccommodationType;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\TagsField;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccommodationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Alapadatok')
                    ->columns(3)
                    ->schema([
                        FlexTextInput::make('name')
                            ->label('Megnevezés')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->columnSpan(2),
                        SelectField::make('type')
                            ->label('Szobatípus')
                            ->options(AccommodationType::class)
                            ->enum(AccommodationType::class)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText(function (mixed $state): ?string {
                                if ($state instanceof AccommodationType) {
                                    return $state->getDescription();
                                }

                                return filled($state)
                                    ? AccommodationType::tryFrom((string) $state)?->getDescription()
                                    : 'A lista később bővíthető.';
                            })
                            ->live()
                            ->columnSpan(1),
                        FlexTextInput::make('slug')
                            ->label('URL slug')
                            ->maxLength(255)
                            ->helperText('Üresen hagyva automatikusan generálódik.')
                            ->columnSpan(2),
                        NumberStepper::make('capacity')
                            ->label('Férőhely')
                            ->required()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(1)
                            ->suffix('fő')
                            ->columnSpan(1),
                        NumberStepper::make('sort_order')
                            ->label('Sorrend')
                            ->minValue(0)
                            ->maxValue(999)
                            ->default(0)
                            ->columnSpan(1),
                        SwitchField::make('is_active')
                            ->label('Aktív')
                            ->description('Megjelenik-e a nyilvános listában / foglalható-e.')
                            ->default(true)
                            ->columnSpan(2),
                        FileUpload::make('cover_image')
                            ->label('Kártyakép')
                            ->helperText('A szálláslisták / élő kártyák képe.')
                            ->image()
                            ->disk('public')
                            ->directory('accommodations')
                            ->visibility('public')
                            ->columnSpanFull(),
                        FileUpload::make('hero_image')
                            ->label('Egyedi oldal fejléc')
                            ->helperText('Az apartman oldal nagy háttérképe. Üresen a kártyakép jelenik meg.')
                            ->image()
                            ->disk('public')
                            ->directory('accommodations')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                Section::make('Árazás')
                    ->columns(3)
                    ->schema([
                        FlexTextInput::make('base_price')
                            ->label('Alapár / éjszaka')
                            ->numeric()
                            ->suffix('Ft')
                            ->required()
                            ->helperText('Alapértelmezett éjszakánkénti díj.')
                            ->columnSpan(1),
                        FlexTextInput::make('ifa_per_person_night')
                            ->label('IFA / fő / éj (18+)')
                            ->numeric()
                            ->suffix('Ft')
                            ->default(0)
                            ->helperText('Alap IFA. Szobánként felülírható. Csak 18 év felettiekre.')
                            ->columnSpan(1),
                        NumberStepper::make('min_nights')
                            ->label('Min. éjszakák (web)')
                            ->minValue(1)
                            ->maxValue(30)
                            ->default(1)
                            ->helperText('Csak a nyilvános foglalásra érvényes. Az rx-panel admin nem kényszeríti.')
                            ->columnSpan(1),
                    ]),
                Section::make('Leírás')
                    ->schema([
                        FlexTextareaField::make('description')
                            ->label('Leírás')
                            ->rows(6)
                            ->columnSpanFull(),
                        TagsField::make('amenities')
                            ->label('Jellemzők')
                            ->helperText('Pl. Wifi, Klíma, Parkoló – a nyilvános oldalon jelennek meg.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Térkép')
                    ->schema([
                        FlexTextInput::make('address')
                            ->label('Pontos cím')
                            ->helperText('Pl. 7625 Pécs, Kálvária utca 12 – megjelenik a térképnél, és ebből készül a térkép, ha nincs külön Maps link.')
                            ->columnSpanFull(),
                        FlexTextInput::make('map_embed_url')
                            ->label('Google Maps link / embed (opcionális)')
                            ->helperText('Ha megadod, ezt használjuk a térképhez. Üresen hagyva a pontos cím alapján jelenik meg.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
