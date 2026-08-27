<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Worker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleCalendarSyncService
{
    public function syncAppointment(Appointment $appointment): void
    {
        $worker = $appointment->worker;

        if (! $worker instanceof Worker || ! $worker->hasGoogleCalendarSync()) {
            return;
        }

        try {
            $accessToken = $this->accessToken($worker);

            if (blank($accessToken)) {
                return;
            }

            $payload = [
                'summary' => 'Időpont: '.$appointment->customer_name,
                'description' => trim(implode("\n", array_filter([
                    'Ügyfél: '.$appointment->customer_name,
                    'E-mail: '.$appointment->customer_email,
                    filled($appointment->customer_phone) ? 'Telefon: '.$appointment->customer_phone : null,
                    filled($appointment->notes) ? 'Megjegyzés: '.$appointment->notes : null,
                ]))),
                'start' => [
                    'dateTime' => $appointment->starts_at->toIso8601String(),
                ],
                'end' => [
                    'dateTime' => $appointment->ends_at->toIso8601String(),
                ],
            ];

            $calendarId = urlencode((string) $worker->google_calendar_id);

            if (filled($appointment->google_event_id)) {
                Http::withToken($accessToken)
                    ->patch("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$appointment->google_event_id}", $payload)
                    ->throw();

                return;
            }

            $response = Http::withToken($accessToken)
                ->post("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events", $payload)
                ->throw()
                ->json();

            $eventId = $response['id'] ?? null;

            if (filled($eventId)) {
                $appointment->forceFill(['google_event_id' => $eventId])->saveQuietly();
            }
        } catch (Throwable $e) {
            Log::warning('Google Calendar sync failed', [
                'appointment_id' => $appointment->id,
                'worker_id' => $worker->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteAppointment(Appointment $appointment): void
    {
        $worker = $appointment->worker;

        if (! $worker instanceof Worker || ! $worker->hasGoogleCalendarSync() || blank($appointment->google_event_id)) {
            return;
        }

        try {
            $accessToken = $this->accessToken($worker);

            if (blank($accessToken)) {
                return;
            }

            $calendarId = urlencode((string) $worker->google_calendar_id);
            $eventId = urlencode((string) $appointment->google_event_id);

            Http::withToken($accessToken)
                ->delete("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}")
                ->throw();

            $appointment->forceFill(['google_event_id' => null])->saveQuietly();
        } catch (Throwable $e) {
            Log::warning('Google Calendar delete failed', [
                'appointment_id' => $appointment->id,
                'worker_id' => $worker->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function accessToken(Worker $worker): ?string
    {
        $credentials = $worker->google_credentials ?? [];
        $expiresAt = (int) ($credentials['access_token_expires_at'] ?? 0);

        if (filled($credentials['access_token'] ?? null) && $expiresAt > now()->addMinute()->timestamp) {
            return (string) $credentials['access_token'];
        }

        try {
            $response = Http::asForm()
                ->post('https://oauth2.googleapis.com/token', [
                    'client_id' => $credentials['client_id'] ?? null,
                    'client_secret' => $credentials['client_secret'] ?? null,
                    'refresh_token' => $credentials['refresh_token'] ?? null,
                    'grant_type' => 'refresh_token',
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            Log::warning('Google OAuth token refresh failed', [
                'worker_id' => $worker->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }

        $accessToken = $response['access_token'] ?? null;

        if (blank($accessToken)) {
            return null;
        }

        $credentials['access_token'] = $accessToken;
        $credentials['access_token_expires_at'] = now()->addSeconds((int) ($response['expires_in'] ?? 3600))->timestamp;

        $worker->forceFill(['google_credentials' => $credentials])->saveQuietly();

        return (string) $accessToken;
    }
}
