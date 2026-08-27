<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'accommodation_id',
    'name',
    'export_token',
    'import_url',
    'last_imported_at',
    'last_exported_at',
    'is_active',
])]
class IcalFeed extends Model
{
    protected function casts(): array
    {
        return [
            'last_imported_at' => 'datetime',
            'last_exported_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (IcalFeed $feed): void {
            if (blank($feed->export_token)) {
                $feed->export_token = Str::random(40);
            }
        });
    }

    /**
     * @return BelongsTo<Accommodation, $this>
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function exportUrl(): string
    {
        return route('ical.export', $this->export_token);
    }
}
