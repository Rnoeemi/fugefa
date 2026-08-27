<?php

namespace App\Filament\Resources\PaymentImplementations\Schemas;

use App\Enums\PaymentProvider;
use App\Models\PaymentImplementation;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PaymentImplementationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Állapot')
                ->columns(4)
                ->schema([
                    FlexTextInput::make('label')->label('Megnevezés')->required()->disabled(),
                    SwitchField::make('is_enabled')->label('Bekapcsolva'),
                    SwitchField::make('is_default')
                        ->label('Alapértelmezett')
                        ->helperText('A foglalási űrlapon ez lesz előre kiválasztva.'),
                    SwitchField::make('is_test_mode')
                        ->label('Teszt mód')
                        ->visible(fn (?PaymentImplementation $record): bool => $record?->provider?->hasCredentialSettings() ?? true),
                ]),
            Tabs::make('credentials')
                ->visible(fn (?PaymentImplementation $record): bool => $record?->provider?->hasCredentialSettings() ?? true)
                ->tabs([
                    Tab::make('test')->label('Teszt credentialek')->schema([
                        FlexTextInput::make('test_credentials.publishable_key')->label('Publishable key')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Stripe),
                        FlexTextInput::make('test_credentials.secret_key')->label('Secret key')->password()->revealable()->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::Stripe, PaymentProvider::Teya], true)),
                        FlexTextInput::make('test_credentials.webhook_secret')->label('Webhook secret')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Stripe),
                        FlexTextInput::make('test_credentials.pos_key')->label('POSKey')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Barion),
                        FlexTextInput::make('test_credentials.payee_email')->label('Payee e-mail')->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Barion),
                        FlexTextInput::make('test_credentials.store_name')->label('StoreName')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('test_credentials.api_user')->label('API user')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep, PaymentProvider::Teya], true)),
                        FlexTextInput::make('test_credentials.api_password')->label('API password')->password()->revealable()->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('test_credentials.api_url')->label('API URL')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep, PaymentProvider::Teya], true)),
                        FlexTextInput::make('test_credentials.pocket_id')->label('SZÉP zseb ID')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('test_credentials.merchant_id')->label('Merchant ID')->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Teya),
                    ]),
                    Tab::make('live')->label('Éles credentialek')->schema([
                        FlexTextInput::make('live_credentials.publishable_key')->label('Publishable key')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Stripe),
                        FlexTextInput::make('live_credentials.secret_key')->label('Secret key')->password()->revealable()->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::Stripe, PaymentProvider::Teya], true)),
                        FlexTextInput::make('live_credentials.webhook_secret')->label('Webhook secret')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Stripe),
                        FlexTextInput::make('live_credentials.pos_key')->label('POSKey')->password()->revealable()->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Barion),
                        FlexTextInput::make('live_credentials.payee_email')->label('Payee e-mail')->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Barion),
                        FlexTextInput::make('live_credentials.store_name')->label('StoreName')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('live_credentials.api_user')->label('API user')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep, PaymentProvider::Teya], true)),
                        FlexTextInput::make('live_credentials.api_password')->label('API password')->password()->revealable()->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('live_credentials.api_url')->label('API URL')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep, PaymentProvider::Teya], true)),
                        FlexTextInput::make('live_credentials.pocket_id')->label('SZÉP zseb ID')->visible(fn (?PaymentImplementation $record) => in_array($record?->provider, [PaymentProvider::KhSzep, PaymentProvider::OtpSzep], true)),
                        FlexTextInput::make('live_credentials.merchant_id')->label('Merchant ID')->visible(fn (?PaymentImplementation $record) => $record?->provider === PaymentProvider::Teya),
                    ]),
                    Tab::make('demo')->label('Demo adatok')->schema([
                        FlexTextareaField::make('demo_info')->label('Teszt / demo információ')->rows(10)->disabled()->dehydrated(false)->columnSpanFull(),
                        FlexTextareaField::make('notes')->label('Belső megjegyzés')->rows(4)->columnSpanFull(),
                    ]),
                ]),
            Section::make('Információ')
                ->visible(fn (?PaymentImplementation $record): bool => $record?->provider === PaymentProvider::Cash)
                ->schema([
                    FlexTextareaField::make('demo_info')
                        ->label('Leírás')
                        ->rows(3)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                    FlexTextareaField::make('notes')
                        ->label('Belső megjegyzés')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
