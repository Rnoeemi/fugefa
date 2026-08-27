<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'worker_id',
    'worker_package_id',
    'package_name',
    'duration_minutes',
    'price',
    'customer_name',
    'customer_email',
    'customer_phone',
    'starts_at',
    'ends_at',
    'status',
    'notes',
    'google_event_id',
])]
class Appointment extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => AppointmentStatus::class,
            'duration_minutes' => 'integer',
            'price' => 'integer',
        ];
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WorkerPackage::class, 'worker_package_id');
    }

    protected function customerPhone(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value): ?string => PhoneNormalizer::toString($value),
        );
    }

    public function isBlocking(): bool
    {
        return in_array($this->status, [
            AppointmentStatus::Pending,
            AppointmentStatus::Confirmed,
        ], true);
    }

    public function scopeBlocking($query)
    {
        return $query->whereIn('status', [
            AppointmentStatus::Pending->value,
            AppointmentStatus::Confirmed->value,
        ]);
    }

    public function formattedPrice(): ?string
    {
        if ($this->price === null) {
            return null;
        }

        return number_format($this->price, 0, ',', ' ').' Ft';
    }
}
