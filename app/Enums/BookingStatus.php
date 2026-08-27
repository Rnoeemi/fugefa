<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Függőben',
            self::Confirmed => 'Megerősítve',
            self::CheckedIn => 'Bejelentkezett',
            self::CheckedOut => 'Kijelentkezett',
            self::Cancelled => 'Lemondva',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => Color::Amber,
            self::Confirmed => Color::Emerald,
            self::CheckedIn => Color::Sky,
            self::CheckedOut => Color::Gray,
            self::Cancelled => Color::Red,
        };
    }

    public function blocksAvailability(): bool
    {
        return match ($this) {
            self::Cancelled, self::CheckedOut => false,
            default => true,
        };
    }

    /**
     * @return list<self>
     */
    public static function blockingCases(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $status): bool => $status->blocksAvailability(),
        ));
    }
}
