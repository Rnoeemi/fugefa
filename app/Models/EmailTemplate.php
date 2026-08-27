<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'name',
    'subject',
    'body',
    'description',
    'is_active',
])]
class EmailTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function findActive(string $key): ?self
    {
        return static::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     */
    public function renderSubject(array $variables): string
    {
        return $this->replaceVariables($this->subject, $variables);
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     */
    public function renderBody(array $variables): string
    {
        return $this->replaceVariables($this->body, $variables);
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     */
    protected function replaceVariables(string $content, array $variables): string
    {
        $replacements = [];

        foreach ($variables as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) ($value ?? '');
        }

        return strtr($content, $replacements);
    }
}
