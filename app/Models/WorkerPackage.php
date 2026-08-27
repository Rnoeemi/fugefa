<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'worker_id',
    'name',
    'description',
    'duration_minutes',
    'price',
    'is_active',
    'sort_order',
])]
class WorkerPackage extends Model
{
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', ' ').' Ft';
    }
}
