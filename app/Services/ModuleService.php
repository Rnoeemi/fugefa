<?php

namespace App\Services;

use App\Enums\SiteModule;
use App\Models\SiteSetting;

class ModuleService
{
    public function isEnabled(SiteModule $module): bool
    {
        $modules = $this->states();

        return (bool) ($modules[$module->value] ?? true);
    }

    public function accommodationEnabled(): bool
    {
        return $this->isEnabled(SiteModule::Accommodation);
    }

    public function paymentEnabled(): bool
    {
        return $this->isEnabled(SiteModule::Payment);
    }

    public function appointmentEnabled(): bool
    {
        return $this->isEnabled(SiteModule::Appointment);
    }

    /**
     * @return array<string, bool>
     */
    public function states(): array
    {
        $stored = SiteSetting::current()->modules;

        if (! is_array($stored)) {
            return SiteModule::defaultStates();
        }

        $states = SiteModule::defaultStates();

        foreach (SiteModule::cases() as $module) {
            if (array_key_exists($module->value, $stored)) {
                $states[$module->value] = (bool) $stored[$module->value];
            }
        }

        return $states;
    }

    /**
     * @return array{modules: array<string, bool>}
     */
    public function getFormState(): array
    {
        return ['modules' => $this->states()];
    }

    /**
     * @param  array{modules?: array<string, mixed>}  $data
     */
    public function syncFromFormState(array $data): void
    {
        $incoming = $data['modules'] ?? [];
        $states = SiteModule::defaultStates();

        foreach (SiteModule::cases() as $module) {
            if (array_key_exists($module->value, $incoming)) {
                $states[$module->value] = (bool) $incoming[$module->value];
            }
        }

        SiteSetting::current()->update([
            'modules' => $states,
        ]);
    }

    public function isAccommodationRelatedUrl(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? $url;

        return str_contains($path, '/szallasok')
            || str_contains($path, '/foglalas-panel')
            || str_contains($path, '/ical/')
            || str_contains($path, '#szallasok');
    }

    public function isAppointmentRelatedUrl(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? $url;

        return str_contains($path, '/idopontfoglalas')
            || str_contains($path, '/worker');
    }
}
