<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Függőben',
            self::Confirmed => 'Megerősítve',
            self::Cancelled => 'Lemondva',
            self::Completed => 'Teljesítve',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => Color::Amber,
            self::Confirmed => Color::Emerald,
            self::Cancelled => Color::Gray,
            self::Completed => Color::Blue,
        };
    }
}
