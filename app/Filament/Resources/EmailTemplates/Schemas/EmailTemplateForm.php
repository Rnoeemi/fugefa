<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sablon')
                    ->columns(2)
                    ->schema([
                        FlexTextInput::make('name')
                            ->label('Megnevezés')
                            ->required()
                            ->columnSpan(1),
                        FlexTextInput::make('key')
                            ->label('Kulcs')
                            ->required()
                            ->helperText('Pl. booking_received_guest – programkódban azonosításhoz.')
                            ->columnSpan(1),
                        FlexTextInput::make('subject')
                            ->label('Tárgy')
                            ->required()
                            ->columnSpanFull(),
                        FlexTextareaField::make('body')
                            ->label('Szövegtörzs')
                            ->required()
                            ->rows(14)
                            ->helperText('Használható változók: {{guest_name}}, {{guest_email}}, {{guest_phone}}, {{accommodation}}, {{check_in}}, {{check_out}}, {{nights}}, {{guests_count}}, {{total_price}}, {{accommodation_total}}, {{ifa_total}}, {{booking_id}}, {{status}}, {{notes}}, {{site_name}}, {{site_phone}}, {{site_email}}, {{site_address}}')
                            ->columnSpanFull(),
                        FlexTextareaField::make('description')
                            ->label('Belső megjegyzés')
                            ->rows(2)
                            ->columnSpanFull(),
                        SwitchField::make('is_active')
                            ->label('Aktív')
                            ->default(true),
                    ]),
            ]);
    }
}
