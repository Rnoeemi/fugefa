<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Accommodation;
use App\Models\Bed;
use App\Models\Booking;
use App\Models\IcalFeed;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IcalSyncService
{
    public function export(Accommodation $accommodation): string
    {
        $bookings = Booking::query()
            ->where('accommodation_id', $accommodation->id)
            ->whereNull('bed_id')
            ->blocking()
            ->orderBy('check_in')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Tusiszallas//Booking Sync//HU',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($bookings as $booking) {
            $uid = $booking->ical_uid ?: ('booking-'.$booking->id.'@tusiszallas.hu');

            if (blank($booking->ical_uid)) {
                $booking->update(['ical_uid' => $uid]);
            }

            $lines = array_merge($lines, [
                'BEGIN:VEVENT',
                'UID:'.$uid,
                'DTSTAMP:'.$this->formatDate(now()),
                'DTSTART;VALUE=DATE:'.$booking->check_in->format('Ymd'),
                'DTEND;VALUE=DATE:'.$booking->check_out->format('Ymd'),
                'SUMMARY:'.($booking->guest?->name ?: 'Foglalás').' – '.$accommodation->name,
                'STATUS:CONFIRMED',
                'END:VEVENT',
            ]);
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines);
    }

    public function import(IcalFeed $feed): int
    {
        if (blank($feed->import_url) || $feed->accommodation?->isAdminOnlyBooking()) {
            return 0;
        }

        $response = Http::timeout(20)->get($feed->import_url);

        if (! $response->ok()) {
            return 0;
        }

        $events = $this->parseEvents($response->body());
        $imported = 0;

        foreach ($events as $event) {
            if (blank($event['uid'] ?? null) || blank($event['start'] ?? null) || blank($event['end'] ?? null)) {
                continue;
            }

            $booking = Booking::query()->firstOrNew(['ical_uid' => $event['uid']]);

            if ($booking->exists && $booking->source !== \App\Enums\BookingSource::Other) {
                continue;
            }

            $booking->fill([
                'accommodation_id' => $feed->accommodation_id,
                'guest_id' => $booking->guest_id ?: $this->ensureSyncGuest($event['summary'] ?? 'iCal vendég')->id,
                'check_in' => $event['start'],
                'check_out' => $event['end'],
                'guests_count' => 1,
                'status' => BookingStatus::Confirmed,
                'source' => \App\Enums\BookingSource::Other,
                'notes' => 'Importálva iCal-ból: '.($event['summary'] ?? ''),
                'confirmed_at' => now(),
            ]);
            $booking->save();
            $imported++;
        }

        $feed->update(['last_imported_at' => now()]);

        return $imported;
    }

    /**
     * @return list<array{uid:?string,start:?string,end:?string,summary:?string}>
     */
    protected function parseEvents(string $ics): array
    {
        $events = [];
        $blocks = preg_split('/BEGIN:VEVENT/', $ics) ?: [];

        foreach (array_slice($blocks, 1) as $block) {
            $chunk = 'BEGIN:VEVENT'.$block;
            $events[] = [
                'uid' => $this->matchField($chunk, 'UID'),
                'start' => $this->parseIcalDate($this->matchField($chunk, 'DTSTART')),
                'end' => $this->parseIcalDate($this->matchField($chunk, 'DTEND')),
                'summary' => $this->matchField($chunk, 'SUMMARY'),
            ];
        }

        return $events;
    }

    protected function matchField(string $chunk, string $field): ?string
    {
        if (preg_match('/'.$field.'(?:;[^:]*)?:(.+)/i', $chunk, $matches)) {
            return trim(str_replace(["\r", '\\,', '\\n'], ['', ',', ' '], $matches[1]));
        }

        return null;
    }

    protected function parseIcalDate(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = Str::before($value, 'T');
        $value = preg_replace('/[^0-9]/', '', $value) ?? '';

        if (strlen($value) < 8) {
            return null;
        }

        return Carbon::createFromFormat('Ymd', substr($value, 0, 8))->toDateString();
    }

    protected function formatDate(Carbon $date): string
    {
        return $date->copy()->utc()->format('Ymd\THis\Z');
    }

    protected function ensureSyncGuest(string $name): \App\Models\Guest
    {
        $email = 'ical+'.Str::slug(Str::limit($name, 40, '')).'@tusiszallas.local';

        return \App\Models\Guest::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => Str::limit($name, 120) ?: 'iCal vendég',
                'status' => \App\Enums\GuestStatus::Welcome,
            ],
        );
    }
}
