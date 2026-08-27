<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum GuestStatus: string implements HasLabel, HasColor, HasDescription
{
    case Welcome = 'welcome';
    case Caution = 'caution';
    case Problematic = 'problematic';
    case Blacklisted = 'blacklisted';

    public function getLabel(): string
    {
        return match ($this) {
            self::Welcome => 'Szívesen látott',
            self::Caution => 'Némi probléma',
            self::Problematic => 'Problémás',
            self::Blacklisted => 'Tiltólistás',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Welcome => 'Zöld – szívesen látott vendég.',
            self::Caution => 'Sárga – kisebb problémák merültek fel.',
            self::Problematic => 'Narancs – problémás vendég.',
            self::Blacklisted => 'Piros – tiltólistás vendég.',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Welcome => Color::Green,
            self::Caution => Color::Yellow,
            self::Problematic => Color::Orange,
            self::Blacklisted => Color::Red,
        };
    }

    public function isBlocked(): bool
    {
        return $this === self::Blacklisted;
    }
}
