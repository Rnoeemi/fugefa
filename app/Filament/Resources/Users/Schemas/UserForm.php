<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\AdminRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make('basic')
                            ->columns(3)
                            ->label('Alapvető adatok')
                            ->schema([
                                FlexTextInput::make('name')
                                    ->label('Név')
                                    ->required()
                                    ->columns(1),
                                SelectField::make('role')
                                    ->label('Jogkör')
                                    ->options(fn (): array => self::availableRoles())
                                    ->enum(AdminRole::class)
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(AdminRole::Receptionist)
                                    ->columns(1),
                            ]),
                        Tab::make('login')
                            ->columns(3)
                            ->label('Bejelentkezési adatok')
                            ->schema([
                                FlexTextInput::make('email')
                                    ->label('E-mail cím')
                                    ->email()
                                    ->required(),
                                FlexTextInput::make('password')
                                    ->label('Jelszó')
                                    ->password()
                                    ->revealable()
                                    ->copyable()
                                    ->passwordStrength()
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create'),
                            ]),
                        
                    ])
            ]);
    }

    /**
     * @return array<string, string>
     */
    protected static function availableRoles(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        return collect(AdminRole::cases())
            ->filter(function (AdminRole $role) use ($user): bool {
                if ($user->isSystemAdmin()) {
                    return true;
                }

                return $role->rank() < $user->role->rank();
            })
            ->mapWithKeys(fn (AdminRole $role): array => [
                $role->value => $role->getLabel(),
            ])
            ->all();
    }
}
