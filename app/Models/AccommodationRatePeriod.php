<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'accommodation_id',
    'name',
    'starts_on',
    'ends_on',
    'nightly_price',
    'ifa_per_person_night',
    'min_nights',
    'is_active',
])]
class AccommodationRatePeriod extends Model
{
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'nightly_price' => 'decimal:2',
            'ifa_per_person_night' => 'decimal:2',
            'min_nights' => 'integer',
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

    public function covers(\Carbon\CarbonInterface|string $date): bool
    {
        $date = \Illuminate\Support\Carbon::parse($date)->startOfDay();

        return $date->betweenIncluded($this->starts_on->startOfDay(), $this->ends_on->startOfDay());
    }
}
