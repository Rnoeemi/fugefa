<?php

namespace App\Models;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'accommodation_id',
    'room_id',
    'bed_id',
    'guest_id',
    'created_by',
    'check_in',
    'check_out',
    'guests_count',
    'adults_count',
    'children_count',
    'status',
    'source',
    'payment_status',
    'payment_provider',
    'payment_reference',
    'amount_paid',
    'total_price',
    'accommodation_total',
    'ifa_total',
    'price_breakdown',
    'notes',
    'ical_uid',
    'confirmed_at',
    'cancelled_at',
])]
class Booking extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'guests_count' => 'integer',
            'adults_count' => 'integer',
            'children_count' => 'integer',
            'status' => BookingStatus::class,
            'source' => BookingSource::class,
            'payment_status' => PaymentStatus::class,
            'amount_paid' => 'decimal:2',
            'total_price' => 'decimal:2',
            'accommodation_total' => 'decimal:2',
            'ifa_total' => 'decimal:2',
            'price_breakdown' => 'array',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
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
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsTo<Bed, $this>
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * @return BelongsTo<Guest, $this>
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasOne<GuestDocument, $this>
     */
    public function document(): HasOne
    {
        return $this->hasOne(GuestDocument::class);
    }

    /**
     * @return HasMany<GuestDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(GuestDocument::class);
    }

    public function scopeBlocking(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            fn (BookingStatus $status): string => $status->value,
            BookingStatus::blockingCases(),
        ));
    }

    public function scopeOverlapping(Builder $query, mixed $checkIn, mixed $checkOut): Builder
    {
        return $query
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn);
    }

    public function nights(): int
    {
        return (int) $this->check_in->diffInDays($this->check_out);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            BookingStatus::Pending,
            BookingStatus::Confirmed,
        ], true);
    }
}
