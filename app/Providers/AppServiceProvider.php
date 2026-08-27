<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\ModuleService;
use App\Services\SiteThemeService;
use App\Support\WindowsSafeFilesystem;
use Livewire\Livewire;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(100);

        $this->app->singleton(ModuleService::class);

        if (windows_os()) {
            $this->app->singleton('files', fn () => new WindowsSafeFilesystem);
        }
    }

    public function boot(): void
    {
        // Visual builder mentés: HTML + CSS + Grapes projectData könnyen >1MB lehet.
        config(['livewire.payload.max_size' => 8 * 1024 * 1024]);

        $this->app->booted(function (): void {
            Livewire::component('filament-menu-manager.menu-panel', \App\Filament\MenuManager\Livewire\MenuPanel::class);
        });

        View::composer('*', function ($view): void {
            $name = $view->name();

            if (
                $name === 'layouts.site'
                || str_starts_with((string) $name, 'site.')
                || str_starts_with((string) $name, 'filament.guest.')
                || str_starts_with((string) $name, 'filament.appointment.')
            ) {
                $settings = SiteSetting::current();
                $modules = app(ModuleService::class);
                $view->with('siteSettings', $settings);
                $view->with('siteTheme', app(SiteThemeService::class)->forFrontend($settings));
                $view->with('accommodationModuleEnabled', $modules->accommodationEnabled());
                $view->with('paymentModuleEnabled', $modules->paymentEnabled());
                $view->with('appointmentModuleEnabled', $modules->appointmentEnabled());
            }
        });
    }
}
