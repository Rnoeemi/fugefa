<?php

namespace App\Services;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\PaymentImplementation;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentGatewayService
{
    public function startCheckout(Booking $booking, PaymentProvider $provider): string
    {
        $implementation = PaymentImplementation::enabled($provider);

        if (! $implementation) {
            throw new \RuntimeException('A választott fizetési mód jelenleg nem elérhető.');
        }

        return match ($provider) {
            PaymentProvider::Stripe => $this->startStripe($booking, $implementation),
            PaymentProvider::Barion => $this->startBarionPlaceholder($booking, $implementation),
            PaymentProvider::Teya,
            PaymentProvider::KhSzep,
            PaymentProvider::OtpSzep => $this->startExternalPlaceholder($booking, $implementation),
            PaymentProvider::Cash => $this->startCash($booking),
        };
    }

    protected function startCash(Booking $booking): string
    {
        $booking->update([
            'payment_provider' => PaymentProvider::Cash->value,
            'payment_reference' => null,
        ]);

        return route('booking.thanks', $booking);
    }

    protected function startStripe(Booking $booking, PaymentImplementation $implementation): string
    {
        $credentials = $implementation->activeCredentials();
        $secret = $credentials['secret_key'] ?? null;

        if (blank($secret)) {
            throw new \RuntimeException('Stripe secret key hiányzik az implementációból.');
        }

        Stripe::setApiKey($secret);

        $amountDue = (float) data_get(
            $booking->price_breakdown,
            'payment_requirement.amount_due',
            $booking->total_price,
        );
        $amount = (int) round(max(0, $amountDue) * 100);

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => route('booking.thanks', $booking).'?paid=1',
            'cancel_url' => url('/foglalas-panel/foglalas').'?cancelled=1',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'huf',
                    'unit_amount' => max(100, $amount),
                    'product_data' => [
                        'name' => 'Foglalás #'.$booking->id.' – '.$booking->accommodation?->name,
                    ],
                ],
            ]],
            'metadata' => [
                'booking_id' => $booking->id,
            ],
        ]);

        $booking->update([
            'payment_provider' => PaymentProvider::Stripe->value,
            'payment_reference' => $session->id,
            'payment_status' => PaymentStatus::DepositDue,
        ]);

        return $session->url;
    }

    protected function startBarionPlaceholder(Booking $booking, PaymentImplementation $implementation): string
    {
        $credentials = $implementation->activeCredentials();
        $posKey = $credentials['pos_key'] ?? 'TEST-POS-KEY';

        Log::info('Barion checkout stub', [
            'booking_id' => $booking->id,
            'pos_key' => $posKey,
            'test_mode' => $implementation->is_test_mode,
        ]);

        $booking->update([
            'payment_provider' => PaymentProvider::Barion->value,
            'payment_reference' => 'barion-stub-'.$booking->id,
            'payment_status' => PaymentStatus::DepositDue,
        ]);

        // Sandbox gateway URL forma – éles POSKey-jel a Payment/Start API kell
        return 'https://secure.test.barion.com/Pay?Id=demo-booking-'.$booking->id;
    }

    protected function startExternalPlaceholder(Booking $booking, PaymentImplementation $implementation): string
    {
        $booking->update([
            'payment_provider' => $implementation->provider->value,
            'payment_reference' => $implementation->provider->value.'-stub-'.$booking->id,
            'payment_status' => PaymentStatus::DepositDue,
        ]);

        return 'https://demo.nevogate.com/?booking='.$booking->id.'&provider='.$implementation->provider->value;
    }
}
