<?php

namespace App\Filament\Resources\SitePages\Schemas;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Schemas\Schema;

class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FlexTextInput::make('title')
                    ->label('Cím')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get, ?string $old): void {
                        if (blank($get('slug')) || $get('slug') === \Illuminate\Support\Str::slug((string) $old)) {
                            $set('slug', \Illuminate\Support\Str::slug((string) $state));
                        }
                    })
                    ->columnSpan(1),
                FlexTextInput::make('slug')
                    ->label('URL slug')
                    ->helperText('Üresen: a címből generálódik. Kezdőlapnál is megmarad (a / útvonal a kezdőlap kapcsolótól függ).')
                    ->columnSpan(1),
                SwitchField::make('is_published')
                    ->label('Publikálva')
                    ->default(false),
                SwitchField::make('is_homepage')
                    ->label('Ez a kezdőlap')
                    ->helperText('Egyszerre csak egy oldal lehet kezdőlap.'),
                NumberStepper::make('sort_order')
                    ->label('Sorrend')
                    ->default(0)
                    ->minValue(0),
            ]);
    }
}
