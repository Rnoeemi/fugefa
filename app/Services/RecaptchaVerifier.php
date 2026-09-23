<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaVerifier
{
    public function isEnabled(): bool
    {
        if (! (bool) config('services.recaptcha.enabled')) {
            return false;
        }

        return filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public function verify(?string $response, ?string $remoteIp = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (blank($response)) {
            return false;
        }

        try {
            $payload = [
                'secret' => (string) config('services.recaptcha.secret_key'),
                'response' => $response,
            ];

            if (filled($remoteIp)) {
                $payload['remoteip'] = $remoteIp;
            }

            $result = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', $payload)
                ->json();

            return (bool) ($result['success'] ?? false);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification failed: '.$e->getMessage());

            return false;
        }
    }
}
