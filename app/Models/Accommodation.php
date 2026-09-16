<?php

namespace App\Models;

use App\Enums\AccommodationType;
use NoteBrainsLab\FilamentMenuManager\Concerns\HasMenuItems;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'cover_image',
    'hero_image',
    'type',
    'description',
    'capacity',
    'base_price',
    'ifa_per_person_night',
    'min_nights',
    'price_from',
    'amenities',
    'address',
    'map_embed_url',
    'is_active',
    'sort_order',
])]
class Accommodation extends Model
{
    use HasMenuItems;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => AccommodationType::class,
            'capacity' => 'integer',
            'base_price' => 'decimal:2',
            'ifa_per_person_night' => 'decimal:2',
            'min_nights' => 'integer',
            'price_from' => 'decimal:2',
            'amenities' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Accommodation $accommodation): void {
            if (blank($accommodation->slug) && filled($accommodation->name)) {
                $accommodation->slug = Str::slug($accommodation->name);
            }

            if ($accommodation->base_price !== null) {
                $accommodation->price_from = $accommodation->base_price;
            }
        });
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<AccommodationRatePeriod, $this>
     */
    public function ratePeriods(): HasMany
    {
        return $this->hasMany(AccommodationRatePeriod::class)->orderBy('starts_on');
    }

    /**
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<AccommodationPaymentRule, $this>
     */
    public function paymentRules(): HasMany
    {
        return $this->hasMany(AccommodationPaymentRule::class)->orderBy('starts_on');
    }

    /**
     * @return HasMany<IcalFeed, $this>
     */
    public function icalFeeds(): HasMany
    {
        return $this->hasMany(IcalFeed::class);
    }

    public function supportsIcal(): bool
    {
        return ! $this->isAdminOnlyBooking();
    }

    public function supportsBeds(): bool
    {
        return $this->type === AccommodationType::WorkersLodging;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, AccommodationType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeBookableOnline(Builder $query): Builder
    {
        return $query
            ->active()
            ->where('type', '!=', AccommodationType::WorkersLodging);
    }

    public function isAdminOnlyBooking(): bool
    {
        return $this->type->isAdminOnlyBooking();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coverUrl(): string
    {
        return $this->resolveImageUrl($this->cover_image);
    }

    /**
     * Egyedi oldal fejléc / hero. Üresen a kártyaképre esik vissza.
     */
    public function heroUrl(): string
    {
        if (filled($this->hero_image)) {
            return $this->resolveImageUrl($this->hero_image);
        }

        return $this->coverUrl();
    }

    protected function resolveImageUrl(?string $path): string
    {
        if (blank($path)) {
            return asset('images/site/hero.jpg');
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function displayPriceFrom(): ?float
    {
        $value = $this->base_price ?? $this->price_from;

        return $value !== null ? (float) $value : null;
    }

    /**
     * Cover + room images for gallery blocks.
     *
     * @return list<string>
     */
    public function galleryUrls(int $limit = 12): array
    {
        $urls = [$this->coverUrl()];

        foreach ($this->roomGalleryItems() as $item) {
            $urls[] = $item['url'];

            if (count($urls) >= $limit) {
                break;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Aktív szobák képei, szobánként csoportosítva (egyedi oldal galéria).
     *
     * @return list<array{name: string, capacity: int|null, images: list<array{url: string, alt: string}>}>
     */
    public function roomGalleryGroups(): array
    {
        $this->loadMissing('rooms');

        $groups = [];

        foreach ($this->rooms as $room) {
            if (! $room->is_active) {
                continue;
            }

            $images = [];
            foreach ($room->images ?? [] as $image) {
                if (! filled($image)) {
                    continue;
                }

                $url = $this->resolveMediaUrl((string) $image);
                $images[] = [
                    'url' => $url,
                    'alt' => trim($room->name.' – '.$this->name),
                ];
            }

            if ($images === []) {
                continue;
            }

            $groups[] = [
                'name' => (string) $room->name,
                'capacity' => $room->capacity !== null ? (int) $room->capacity : null,
                'images' => $images,
            ];
        }

        return $groups;
    }

    /**
     * @return list<array{url: string, alt: string}>
     */
    public function roomGalleryItems(): array
    {
        $items = [];

        foreach ($this->roomGalleryGroups() as $group) {
            foreach ($group['images'] as $image) {
                $items[] = $image;
            }
        }

        return $items;
    }

    public function resolveMediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return Storage::disk('public')->url($path);
    }

    public function getMenuLabel(): string
    {
        return $this->name;
    }

    public function getMenuUrl(): string
    {
        return route('accommodations.show', ['accommodation' => $this->slug]);
    }

    public function getMenuIcon(): ?string
    {
        return 'heroicon-o-home-modern';
    }
}
