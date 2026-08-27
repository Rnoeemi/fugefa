<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\Booking;
use App\Models\EmailTemplate;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;

class BookingMailService
{
    public const TEMPLATE_GUEST_RECEIVED = 'booking_received_guest';

    public const TEMPLATE_ADMIN_RECEIVED = 'booking_received_admin';

    public const TEMPLATE_GUEST_CONFIRMED = 'booking_confirmed_guest';

    public function sendReceived(Booking $booking): void
    {
        $booking->loadMissing(['guest', 'accommodation']);
        $variables = $this->variables($booking);

        if (filled($booking->guest?->email)) {
            $this->sendTemplate(self::TEMPLATE_GUEST_RECEIVED, $booking->guest->email, $variables);
        }

        $adminEmail = SiteSetting::current()->notification_email
            ?: SiteSetting::current()->email;

        if (filled($adminEmail)) {
            $this->sendTemplate(self::TEMPLATE_ADMIN_RECEIVED, $adminEmail, $variables);
        }
    }

    public function sendConfirmed(Booking $booking): void
    {
        $booking->loadMissing(['guest', 'accommodation']);

        if (blank($booking->guest?->email)) {
            return;
        }

        $this->sendTemplate(
            self::TEMPLATE_GUEST_CONFIRMED,
            $booking->guest->email,
            $this->variables($booking),
        );
    }

    public function sendTemplate(string $key, string $to, array $variables): void
    {
        $template = EmailTemplate::findActive($key);

        if (! $template) {
            return;
        }

        $body = nl2br(e($template->renderBody($variables)));

        Mail::to($to)->send(new TemplatedMail(
            subjectLine: $template->renderSubject($variables),
            bodyHtml: $body,
        ));
    }

    /**
     * @return array<string, string|int|float|null>
     */
    public function variables(Booking $booking): array
    {
        $settings = SiteSetting::current();
        $quoteNights = $booking->nights();

        return [
            'guest_name' => $booking->guest?->name,
            'guest_email' => $booking->guest?->email,
            'guest_phone' => $booking->guest?->phone,
            'accommodation' => $booking->accommodation?->name,
            'check_in' => $booking->check_in?->format('Y.m.d.'),
            'check_out' => $booking->check_out?->format('Y.m.d.'),
            'nights' => $quoteNights,
            'guests_count' => $booking->guests_count,
            'total_price' => $this->formatMoney($booking->total_price),
            'accommodation_total' => $this->formatMoney($booking->accommodation_total),
            'ifa_total' => $this->formatMoney($booking->ifa_total),
            'booking_id' => $booking->id,
            'status' => $booking->status?->getLabel(),
            'notes' => $booking->notes,
            'site_name' => $settings->site_name,
            'site_phone' => $settings->phone,
            'site_email' => $settings->email,
            'site_address' => $settings->address,
        ];
    }

    protected function formatMoney(mixed $amount): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        return number_format((float) $amount, 0, ',', ' ').' Ft';
    }
}
