<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'accommodation_id',
    'name',
    'starts_on',
    'ends_on',
    'require_deposit',
    'deposit_percent',
    'require_full_payment',
    'is_active',
])]
class AccommodationPaymentRule extends Model
{
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'require_deposit' => 'boolean',
            'deposit_percent' => 'integer',
            'require_full_payment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Accommodation, $this>
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function covers(Carbon|string $date): bool
    {
        $date = Carbon::parse($date)->startOfDay();

        return $date->betweenIncluded($this->starts_on->startOfDay(), $this->ends_on->startOfDay());
    }
}
