<?php

namespace App\Filament\Concerns;

use App\Services\SiteThemeService;

trait UsesSiteShellBody
{
    /**
     * @return array<string, mixed>
     */
    public function getExtraBodyAttributes(): array
    {
        $theme = app(SiteThemeService::class)->forFrontend();
        $preset = (string) ($theme['style_preset'] ?? 'soft-ui');

        return [
            'class' => 'site-shell site-style-'.$preset,
        ];
    }
}
