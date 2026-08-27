<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingSource: string implements HasLabel, HasColor
{
    case Website = 'website';
    case Admin = 'admin';
    case Phone = 'phone';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Website => 'Weboldal',
            self::Admin => 'Admin',
            self::Phone => 'Telefon',
            self::Other => 'Egyéb',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Website => Color::Sky,
            self::Admin => Color::Emerald,
            self::Phone => Color::Amber,
            self::Other => Color::Gray,
        };
    }
}
