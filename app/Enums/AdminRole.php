<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Colors\Color;

enum AdminRole: string implements HasLabel, HasColor
{
    case SystemAdmin = 'system_admin';
    case Administrator = 'administrator';
    case Receptionist = 'receptionist';

    /**
     * @return list<self>
     */
    public static function assignableCases(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $role): bool => $role !== self::SystemAdmin,
        ));
    }

    public function rank(): int
    {
        return match ($this) {
            self::SystemAdmin => 300,
            self::Administrator => 200,
            self::Receptionist => 100,
        };
    }

    public function isHigherThan(self $other): bool
    {
        return $this->rank() > $other->rank();
    }

    public function isAtLeast(self $other): bool
    {
        return $this->rank() >= $other->rank();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SystemAdmin => 'Rendszergazda',
            self::Administrator => 'Adminisztrátor',
            self::Receptionist => 'Recepciós',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SystemAdmin => Color::Red,
            self::Administrator => Color::Emerald,
            self::Receptionist => Color::Emerald
        };
    }
}
