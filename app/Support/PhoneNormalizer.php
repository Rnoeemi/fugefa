<?php

namespace App\Support;

final class PhoneNormalizer
{
    public static function toString(mixed $phone): ?string
    {
        if (is_string($phone)) {
            $trimmed = trim($phone);
            if ($trimmed === '') {
                return null;
            }

            if (str_starts_with($trimmed, '{')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    return self::fromArray($decoded);
                }
            }

            return $trimmed;
        }

        if (is_array($phone)) {
            return self::fromArray($phone);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $phone
     */
    protected static function fromArray(array $phone): ?string
    {
        if (filled($phone['e164'] ?? null)) {
            return (string) $phone['e164'];
        }

        if (filled($phone['national'] ?? null)) {
            return (string) $phone['national'];
        }

        return null;
    }

    /**
     * Builder HTML-ben / láblécben beragadt PhoneField JSON → olvasható szám.
     */
    public static function sanitizeHtml(string $html): string
    {
        if ($html === '' || (! str_contains($html, '"e164"') && ! str_contains($html, '"national"'))) {
            return $html;
        }

        $replaced = preg_replace_callback(
            '/\{[^{}]*"(?:e164|national|country)"\s*:\s*"[^"]*"[^{}]*\}/',
            function (array $matches): string {
                $normalized = self::toString($matches[0]);

                return $normalized ?? $matches[0];
            },
            $html
        );

        return is_string($replaced) ? $replaced : $html;
    }
}
