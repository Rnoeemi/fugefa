<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'accommodation_id',
    'name',
    'code',
    'capacity',
    'ifa_per_person_night',
    'is_active',
    'sort_order',
    'notes',
    'images',
])]
class Room extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'ifa_per_person_night' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'images' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Accommodation, $this>
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * @return HasMany<Bed, $this>
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function resolvedIfaPerPersonNight(): float
    {
        if ($this->ifa_per_person_night !== null) {
            return (float) $this->ifa_per_person_night;
        }

        return (float) ($this->accommodation?->ifa_per_person_night ?? 0);
    }
}
