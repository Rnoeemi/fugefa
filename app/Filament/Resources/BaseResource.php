<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    use AuthorizesResourcePermissions;
}
