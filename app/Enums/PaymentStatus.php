<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor
{
    case Unpaid = 'unpaid';
    case DepositDue = 'deposit_due';
    case DepositPaid = 'deposit_paid';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unpaid => 'Fizetetlen',
            self::DepositDue => 'Előleg vár',
            self::DepositPaid => 'Előleg fizetve',
            self::Paid => 'Fizetve',
            self::Failed => 'Sikertelen',
            self::Refunded => 'Visszatérítve',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unpaid => Color::Gray,
            self::DepositDue => Color::Amber,
            self::DepositPaid => Color::Sky,
            self::Paid => Color::Emerald,
            self::Failed => Color::Red,
            self::Refunded => Color::Orange,
        };
    }
}
