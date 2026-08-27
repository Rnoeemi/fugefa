<?php

namespace App\Services;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

class FilamentResourceRegistry
{
    /**
     * @return array<int, array{class: class-string<Resource>, label: string}>
     */
    public function all(): array
    {
        return collect(Filament::getPanel('rx-panel')->getResources())
            ->filter(fn (string $resourceClass): bool => is_subclass_of($resourceClass, Resource::class))
            ->reject(fn (string $resourceClass): bool => $resourceClass === \App\Filament\Resources\BaseResource::class)
            ->filter(fn (string $resourceClass): bool => $resourceClass::registersForPermissions())
            ->map(fn (string $resourceClass): array => [
                'class' => $resourceClass,
                'label' => $resourceClass::getPluralModelLabel(),
            ])
            ->sortBy('label')
            ->values()
            ->all();
    }

    public function labels(): array
    {
        return collect($this->all())
            ->mapWithKeys(fn (array $resource): array => [
                $resource['class'] => $resource['label'],
            ])
            ->all();
    }

    public function slug(string $resourceClass): string
    {
        return Str::of(class_basename($resourceClass))
            ->beforeLast('Resource')
            ->snake()
            ->toString();
    }
}
