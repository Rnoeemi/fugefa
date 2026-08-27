<?php

namespace App\Models;

use App\Enums\GuestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'email',
    'phone',
    'id_number',
    'status',
    'internal_note',
])]
class Guest extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => GuestStatus::class,
        ];
    }

    /**
     * @return HasMany<GuestNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(GuestNote::class)->latest();
    }

    /**
     * @return HasMany<GuestDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(GuestDocument::class)->latest();
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest('check_in');
    }

    public function scopeWithStatus(Builder $query, GuestStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeBlacklisted(Builder $query): Builder
    {
        return $query->where('status', GuestStatus::Blacklisted);
    }

    public function isBlocked(): bool
    {
        return $this->status->isBlocked();
    }

    /**
     * Vendég keresése igazolványszám alapján (guest.id_number vagy korábbi okmány).
     */
    public static function findByIdNumber(string $idNumber): ?self
    {
        $normalized = strtoupper(trim($idNumber));

        if ($normalized === '') {
            return null;
        }

        $guest = static::query()
            ->whereRaw('UPPER(TRIM(id_number)) = ?', [$normalized])
            ->first();

        if ($guest) {
            return $guest;
        }

        return static::query()
            ->whereHas('documents', fn (Builder $query) => $query->whereRaw('UPPER(TRIM(document_number)) = ?', [$normalized]))
            ->first();
    }

    public function latestDocumentWithImages(): ?GuestDocument
    {
        return $this->documents()
            ->whereNotNull('id_card_front_path')
            ->whereNotNull('id_card_back_path')
            ->whereNotNull('address_card_front_path')
            ->where('id_card_front_path', '!=', '')
            ->where('id_card_back_path', '!=', '')
            ->where('address_card_front_path', '!=', '')
            ->latest()
            ->first();
    }

    public function hasStoredDocuments(): bool
    {
        return $this->latestDocumentWithImages() !== null;
    }
}
