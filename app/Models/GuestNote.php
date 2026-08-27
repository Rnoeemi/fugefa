<?php

namespace App\Models;

use App\Enums\GuestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'guest_id',
    'user_id',
    'body',
    'status_snapshot',
])]
class GuestNote extends Model
{
    protected function casts(): array
    {
        return [
            'status_snapshot' => GuestStatus::class,
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
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
