<?php

namespace App\Services;

use App\Enums\AdminRole;
use App\Models\ResourcePermission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResourcePermissionService
{
    public function __construct(
        protected FilamentResourceRegistry $registry,
    ) {}

    /**
     * @return array{permissions: array<string, array<string, array{can_view: bool, can_edit: bool, can_add: bool, can_remove: bool}>>}
     */
    public function getFormState(): array
    {
        $stored = ResourcePermission::query()
            ->get()
            ->groupBy(fn (ResourcePermission $permission): string => $permission->role->value);

        $permissions = [];

        foreach (AdminRole::assignableCases() as $role) {
            foreach ($this->registry->all() as $resource) {
                $existing = $stored->get($role->value)?->firstWhere('resource', $resource['class']);

                $permissions[$role->value][$resource['class']] = [
                    'can_view' => (bool) ($existing?->can_view ?? false),
                    'can_edit' => (bool) ($existing?->can_edit ?? false),
                    'can_add' => (bool) ($existing?->can_add ?? false),
                    'can_remove' => (bool) ($existing?->can_remove ?? false),
                ];
            }
        }

        return compact('permissions');
    }

    /**
     * @param  array{permissions?: array<string, array<string, array<string, bool>>>}  $state
     */
    public function syncFromFormState(array $state): void
    {
        foreach (AdminRole::assignableCases() as $role) {
            foreach ($this->registry->all() as $resource) {
                $values = $state['permissions'][$role->value][$resource['class']] ?? [];

                ResourcePermission::query()->updateOrCreate(
                    [
                        'role' => $role->value,
                        'resource' => $resource['class'],
                    ],
                    [
                        'can_view' => (bool) ($values['can_view'] ?? false),
                        'can_edit' => (bool) ($values['can_edit'] ?? false),
                        'can_add' => (bool) ($values['can_add'] ?? false),
                        'can_remove' => (bool) ($values['can_remove'] ?? false),
                    ],
                );
            }
        }
    }

    public function canView(User $user, string $resourceClass): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        return $this->getPermission($user, $resourceClass)?->can_view ?? false;
    }

    public function canAdd(User $user, string $resourceClass): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        return $this->getPermission($user, $resourceClass)?->can_add ?? false;
    }

    public function canEdit(User $user, string $resourceClass, ?Model $record = null): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        if (! ($this->getPermission($user, $resourceClass)?->can_edit ?? false)) {
            return false;
        }

        return $this->passesRecordRankCheck($user, $resourceClass, $record);
    }

    public function canRemove(User $user, string $resourceClass, ?Model $record = null): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        if (! ($this->getPermission($user, $resourceClass)?->can_remove ?? false)) {
            return false;
        }

        return $this->passesRecordRankCheck($user, $resourceClass, $record);
    }

    public function canRemoveAny(User $user, string $resourceClass): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        return $this->getPermission($user, $resourceClass)?->can_remove ?? false;
    }

    protected function getPermission(User $user, string $resourceClass): ?ResourcePermission
    {
        return ResourcePermission::query()
            ->where('role', $user->role)
            ->where('resource', $resourceClass)
            ->first();
    }

    protected function passesRecordRankCheck(User $user, string $resourceClass, ?Model $record): bool
    {
        if (! $record instanceof User) {
            return true;
        }

        if ($record->is($user)) {
            return false;
        }

        return $user->role->isHigherThan($record->role);
    }
}
