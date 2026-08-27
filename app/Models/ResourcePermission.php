<?php

namespace App\Models;

use App\Enums\AdminRole;
use Illuminate\Database\Eloquent\Model;

class ResourcePermission extends Model
{
    protected $fillable = [
        'role',
        'resource',
        'can_view',
        'can_edit',
        'can_add',
        'can_remove',
    ];

    protected function casts(): array
    {
        return [
            'role' => AdminRole::class,
            'can_view' => 'boolean',
            'can_edit' => 'boolean',
            'can_add' => 'boolean',
            'can_remove' => 'boolean',
        ];
    }
}
