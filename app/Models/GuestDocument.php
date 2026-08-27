<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'guest_id',
    'booking_id',
    'id_card_front_path',
    'id_card_back_path',
    'address_card_front_path',
    'document_number',
    'full_name_on_document',
    'birth_date',
    'nationality',
    'address_on_card',
    'name_matches',
    'id_number_matches',
    'address_present',
    'is_validated',
    'ntak_ready',
    'validation_notes',
])]
class GuestDocument extends Model
{
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'name_matches' => 'boolean',
            'id_number_matches' => 'boolean',
            'address_present' => 'boolean',
            'is_validated' => 'boolean',
            'ntak_ready' => 'boolean',
            'validation_notes' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Guest, $this>
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function pathUrl(?string $path): ?string
    {
        return filled($path) ? Storage::disk('public')->url($path) : null;
    }
}
