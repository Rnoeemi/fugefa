<?php

namespace App\Filament\Resources\Concerns;

use App\Services\ResourcePermissionService;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesResourcePermissions
{
    protected static bool $registersPermissions = true;

    public static function registersForPermissions(): bool
    {
        return static::$registersPermissions;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return app(ResourcePermissionService::class)->canView($user, static::class);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return app(ResourcePermissionService::class)->canAdd($user, static::class);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return app(ResourcePermissionService::class)->canEdit($user, static::class, $record);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return app(ResourcePermissionService::class)->canRemove($user, static::class, $record);
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSystemAdmin()) {
            return true;
        }

        return app(ResourcePermissionService::class)->canRemoveAny($user, static::class);
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
