<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class BookingNotificationService
{
    public function notifyNewBooking(Booking $booking): void
    {
        $booking->loadMissing(['guest', 'accommodation', 'bed.room']);

        $title = 'Új foglalás: '.($booking->accommodation?->name ?? 'Szállás');
        $body = sprintf(
            '%s · %s – %s · %d fő%s',
            $booking->guest?->name ?? 'Vendég',
            $booking->check_in?->format('Y.m.d.'),
            $booking->check_out?->format('Y.m.d.'),
            $booking->guests_count,
            $booking->bed ? ' · '.$booking->bed->label() : '',
        );

        $recipients = User::query()->whereNull('deleted_at')->get();

        foreach ($recipients as $user) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->icon(Heroicon::CalendarDays)
                ->color('success')
                ->category('booking')
                ->sendToDatabase($user);
        }
    }
}
