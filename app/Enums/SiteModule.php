<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SiteModule: string implements HasLabel
{
    case Accommodation = 'accommodation';
    case Payment = 'payment';
    case Appointment = 'appointment';

    public function getLabel(): string
    {
        return match ($this) {
            self::Accommodation => 'Szálláshely',
            self::Payment => 'Fizetés',
            self::Appointment => 'Időpontfoglaló',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Accommodation => 'Szállások, foglalások, naptár, NTAK, recepció panel és a frontend foglalási felülete.',
            self::Payment => 'Fizetési módok, számlázás, valamint online fizetés választása a foglalási űrlapon.',
            self::Appointment => 'Munkatársak, időpontfoglalások, Google Naptár szinkron, worker panel és a frontend időpontfoglaló blokk.',
        };
    }

    /**
     * @return array<string, bool>
     */
    public static function defaultStates(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $module): array => [$module->value => true])
            ->all();
    }
}
