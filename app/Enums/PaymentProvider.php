<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentProvider: string implements HasLabel, HasColor
{
    case Stripe = 'stripe';
    case Teya = 'teya';
    case Barion = 'barion';
    case KhSzep = 'kh_szep';
    case OtpSzep = 'otp_szep';
    case Cash = 'cash';

    public function getLabel(): string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::Teya => 'Teya',
            self::Barion => 'Barion',
            self::KhSzep => 'K&H SZÉP kártya',
            self::OtpSzep => 'OTP SZÉP kártya',
            self::Cash => 'Készpénz',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Stripe => Color::Violet,
            self::Teya => Color::Blue,
            self::Barion => Color::Emerald,
            self::KhSzep => Color::Sky,
            self::OtpSzep => Color::Green,
            self::Cash => Color::Amber,
        };
    }

    public function requiresOnlineCheckout(): bool
    {
        return $this !== self::Cash;
    }

    public function hasCredentialSettings(): bool
    {
        return $this !== self::Cash;
    }

    public function demoInfo(): string
    {
        return match ($this) {
            self::Stripe => "Teszt kártya: 4242 4242 4242 4242\nLejárat: bármely jövőbeli dátum\nCVC: bármely 3 számjegy\nDocs: https://docs.stripe.com/testing",
            self::Teya => "Teya (korábban SaltPay/Borgun) sandbox a partnerportálon igényelhető.\nDemo környezet: Nevogate Teya RPG / SecurePay.\nDocs: https://demo.nevogate.com/",
            self::Barion => "Sandbox: https://api.test.barion.com\nGateway: https://secure.test.barion.com\nPOSKey a Barion test shop Details oldaláról.\nTesztkártya (success): 4444 8888 8888 5559\nDocs: https://docs.barion.com/Sandbox",
            self::KhSzep => "Nevogate demo / Payment Gateway test:\nStore: sdk_test\nAPI user: sdk_test / 86af3-80e4f-f8228-9498f-910ad\nProvider: KHBSZEP\nSikeres tesztkártya: 61013170 00000128, CVV: 497\nDocs: https://docs.paymentgateway.hu/ + https://demo.nevogate.com/",
            self::OtpSzep => "Nevogate demo:\nProvider: OTP / OTPSZEP\nSikeres: 3086 7825 6471 9254 / 12/2030 / 213 / jelszó: PGtest01\nSikertelen: 3086 7825 0510 1182 / 12/2019 / 508 / Aa123456\nDocs: https://demo.nevogate.com/",
            self::Cash => 'Helyszíni készpénzes fizetés. Nincs online beállítási opció – csak be-/kikapcsolható.',
        };
    }

    /**
     * @return list<string>
     */
    public function credentialKeys(): array
    {
        return match ($this) {
            self::Stripe => ['publishable_key', 'secret_key', 'webhook_secret'],
            self::Teya => ['merchant_id', 'secret_key', 'api_url'],
            self::Barion => ['pos_key', 'payee_email', 'pixel_id'],
            self::KhSzep => ['store_name', 'api_user', 'api_password', 'api_url', 'pocket_id'],
            self::OtpSzep => ['store_name', 'api_user', 'api_password', 'api_url', 'pocket_id'],
            self::Cash => [],
        };
    }
}
