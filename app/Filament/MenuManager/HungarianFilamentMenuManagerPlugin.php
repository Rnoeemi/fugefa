<?php

namespace App\Filament\MenuManager;

use App\Filament\Pages\MenuManagerPage;
use Filament\Panel;
use NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin;

class HungarianFilamentMenuManagerPlugin extends FilamentMenuManagerPlugin
{
    public function register(Panel $panel): void
    {
        $panel->pages([MenuManagerPage::class]);
    }
}

