<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Pages\MenuManagerPage as BaseMenuManagerPage;

class MenuManagerPage extends BaseMenuManagerPage
{
    protected static ?string $navigationLabel = 'Menükezelő';

    protected ?string $subheading = 'Hely alapú, többszintű menük kezelése a webhely navigációjához.';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Menükezelő';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createMenu')
                ->label('Új menü')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('menu_location_id')
                        ->label('Elhelyezés')
                        ->options(fn () => $this->getLocations()->pluck('name', 'id')->toArray())
                        ->default(fn () => $this->activeLocationId)
                        ->required(),
                    TextInput::make('name')
                        ->label('Menü neve')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $menuModel = config('filament-menu-manager.models.menu', Menu::class);
                    $menu = $menuModel::create($data);
                    $this->activeMenuId = $menu->id;

                    Notification::make('menu_created')
                        ->title('A menü létrejött')
                        ->success()
                        ->send();
                }),

            Action::make('deleteMenu')
                ->label('Menü törlése')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->activeMenuId !== null)
                ->modalHeading('Menü törlése')
                ->modalDescription('Biztosan törölni szeretnéd a kiválasztott menüt?')
                ->modalSubmitActionLabel('Törlés')
                ->action(function (): void {
                    if ($this->activeMenuId) {
                        $menuModel = config('filament-menu-manager.models.menu', Menu::class);
                        $menuModel::destroy($this->activeMenuId);
                        $this->activeMenuId = null;

                        $menus = $this->getMenusForActiveLocation();
                        if ($menus->isNotEmpty()) {
                            $this->activeMenuId = $menus->first()->id;
                        }

                        Notification::make('menu_deleted')
                            ->title('A menü törölve lett')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}

