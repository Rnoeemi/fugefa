<?php

namespace App\Models;

use App\Support\PhoneNormalizer;
use Database\Factories\WorkerFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'slug',
    'title',
    'bio',
    'is_active',
    'sort_order',
    'slot_duration_minutes',
    'work_starts_at',
    'work_ends_at',
    'google_calendar_enabled',
    'google_calendar_id',
    'google_credentials',
])]
#[Hidden(['password', 'remember_token', 'google_credentials'])]
class Worker extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<WorkerFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'google_calendar_enabled' => 'boolean',
            'google_credentials' => 'encrypted:array',
            'slot_duration_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Worker $worker): void {
            if (blank($worker->slug) && filled($worker->name)) {
                $worker->slug = static::uniqueSlugFrom($worker->name, $worker->getKey());
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'worker' && $this->is_active;
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(WorkerPackage::class);
    }

    public function activePackages(): HasMany
    {
        return $this->packages()->active()->orderBy('sort_order')->orderBy('name');
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (mixed $value): ?string => PhoneNormalizer::toString($value),
        );
    }

    public function workStartsAtTime(): string
    {
        return substr((string) ($this->work_starts_at ?? '09:00:00'), 0, 5);
    }

    public function workEndsAtTime(): string
    {
        return substr((string) ($this->work_ends_at ?? '17:00:00'), 0, 5);
    }

    public function workStartForDate(Carbon $date): Carbon
    {
        [$h, $m] = array_map('intval', explode(':', $this->workStartsAtTime()));

        return $date->copy()
            ->timezone(\App\Services\AppointmentBookingService::TIMEZONE)
            ->startOfDay()
            ->setTime($h, $m);
    }

    public function workEndForDate(Carbon $date): Carbon
    {
        [$h, $m] = array_map('intval', explode(':', $this->workEndsAtTime()));

        return $date->copy()
            ->timezone(\App\Services\AppointmentBookingService::TIMEZONE)
            ->startOfDay()
            ->setTime($h, $m);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBookable($query)
    {
        return $query->active()->orderBy('sort_order')->orderBy('name');
    }

    public function hasGoogleCalendarSync(): bool
    {
        if (! $this->google_calendar_enabled) {
            return false;
        }

        $credentials = $this->google_credentials ?? [];

        return filled($this->google_calendar_id)
            && filled($credentials['client_id'] ?? null)
            && filled($credentials['client_secret'] ?? null)
            && filled($credentials['refresh_token'] ?? null);
    }

    public static function uniqueSlugFrom(string $name, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'munkatars';
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
