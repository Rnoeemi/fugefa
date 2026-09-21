<?php

namespace App\Services;

use App\Mail\ContactFormMail;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactMailService
{
    /**
     * @param  array{name: string, email: string, phone?: ?string, message: string}  $payload
     */
    public function send(array $payload): bool
    {
        $settings = SiteSetting::current();
        $to = $settings->contact_notification_email
            ?: $settings->email
            ?: $settings->notification_email;

        if (blank($to)) {
            Log::warning('Contact form submitted but no recipient email is configured.');

            return false;
        }

        try {
            Mail::to($to)->send(new ContactFormMail(
                payload: [
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'phone' => $payload['phone'] ?? null,
                    'message' => $payload['message'],
                ],
                siteName: (string) ($settings->site_name ?: 'Weboldal'),
            ));

            return true;
        } catch (Throwable $e) {
            Log::error('Contact form mail failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return false;
        }
    }
}
