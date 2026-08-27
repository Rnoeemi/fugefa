<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\AppointmentStatus;
use App\Models\Worker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Időpont')
                ->columns(2)
                ->schema([
                    SelectField::make('worker_id')
                        ->label('Munkatárs')
                        ->options(fn (): array => Worker::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    SelectField::make('status')
                        ->label('Státusz')
                        ->options(AppointmentStatus::class)
                        ->required()
                        ->default(AppointmentStatus::Pending),
                    DateTimePicker::make('starts_at')
                        ->label('Kezdés')
                        ->seconds(false)
                        ->required(),
                    DateTimePicker::make('ends_at')
                        ->label('Befejezés')
                        ->seconds(false)
                        ->required(),
                ]),
            Section::make('Ügyfél')
                ->columns(2)
                ->schema([
                    FlexTextInput::make('customer_name')
                        ->label('Név')
                        ->required(),
                    FlexTextInput::make('customer_email')
                        ->label('E-mail')
                        ->email()
                        ->required(),
                    PhoneField::make('customer_phone')
                        ->label('Telefon')
                        ->defaultCountry('HU'),
                    FlexTextareaField::make('notes')
                        ->label('Megjegyzés')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
