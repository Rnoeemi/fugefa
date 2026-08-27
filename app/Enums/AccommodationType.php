<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AccommodationType: string implements HasLabel, HasColor, HasDescription
{
    case Guesthouse = 'guesthouse';
    case Room = 'room';
    case WorkersLodging = 'workers_lodging';

    public function getLabel(): string
    {
        return match ($this) {
            self::Guesthouse => 'Vendégház',
            self::Room => 'Szoba',
            self::WorkersLodging => 'Munkásszállás',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Guesthouse => 'A teljes házat lehet bérelni.',
            self::Room => 'Szobát lehet bérelni.',
            self::WorkersLodging => 'Csak adminról rögzíthető.',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Guesthouse => Color::Emerald,
            self::Room => Color::Sky,
            self::WorkersLodging => Color::Amber,
        };
    }

    /**
     * A nyilvános foglalás tiltott; csak admin rögzíthet.
     */
    public function isAdminOnlyBooking(): bool
    {
        return $this === self::WorkersLodging;
    }

    /**
     * Egységként (teljes ház) bérelhető.
     */
    public function isWholeUnit(): bool
    {
        return $this === self::Guesthouse;
    }
}
