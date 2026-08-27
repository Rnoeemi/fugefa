<?php

namespace App\Models;

use App\Enums\PaymentProvider;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'provider',
    'label',
    'is_enabled',
    'is_default',
    'is_test_mode',
    'test_credentials',
    'live_credentials',
    'notes',
    'demo_info',
])]
class PaymentImplementation extends Model
{
    protected function casts(): array
    {
        return [
            'provider' => PaymentProvider::class,
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
            'is_test_mode' => 'boolean',
            'test_credentials' => 'encrypted:array',
            'live_credentials' => 'encrypted:array',
        ];
    }

    public function activeCredentials(): array
    {
        return $this->is_test_mode
            ? ($this->test_credentials ?? [])
            : ($this->live_credentials ?? []);
    }

    public static function enabled(PaymentProvider $provider): ?self
    {
        return static::query()
            ->where('provider', $provider)
            ->where('is_enabled', true)
            ->first();
    }

    public static function defaultProviderValue(): ?string
    {
        $default = static::query()
            ->where('is_enabled', true)
            ->where('is_default', true)
            ->first();

        if ($default) {
            return $default->provider->value;
        }

        $fallback = static::query()
            ->where('is_enabled', true)
            ->orderBy('id')
            ->first();

        return $fallback?->provider->value;
    }

    public function makeSoleDefault(): void
    {
        static::query()
            ->whereKeyNot($this->getKey())
            ->where('is_default', true)
            ->update(['is_default' => false]);

        if (! $this->is_default) {
            $this->forceFill(['is_default' => true])->saveQuietly();
        }
    }
}
