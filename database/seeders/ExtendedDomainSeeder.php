<?php

namespace Database\Seeders;

use App\Enums\PaymentProvider;
use App\Models\Accommodation;
use App\Models\Bed;
use App\Models\IcalFeed;
use App\Models\PaymentImplementation;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExtendedDomainSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PaymentProvider::cases() as $provider) {
            PaymentImplementation::query()->updateOrCreate(
                ['provider' => $provider->value],
                [
                    'label' => $provider->getLabel(),
                    'is_enabled' => false,
                    'is_test_mode' => $provider->hasCredentialSettings(),
                    'demo_info' => $provider->demoInfo(),
                    'test_credentials' => match ($provider) {
                        PaymentProvider::Barion => [
                            'pos_key' => 'TEST-POS-KEY',
                            'payee_email' => 'merchant@example.com',
                        ],
                        PaymentProvider::KhSzep => [
                            'store_name' => 'sdk_test',
                            'api_user' => 'sdk_test',
                            'api_password' => '86af3-80e4f-f8228-9498f-910ad',
                            'api_url' => 'https://system-test.paymentgateway.hu/api/payment/',
                            'pocket_id' => '3',
                        ],
                        PaymentProvider::OtpSzep => [
                            'store_name' => 'sdk_test',
                            'api_user' => 'sdk_test',
                            'api_password' => '86af3-80e4f-f8228-9498f-910ad',
                            'api_url' => 'https://system-test.paymentgateway.hu/api/payment/',
                            'pocket_id' => '08',
                        ],
                        PaymentProvider::Stripe => [
                            'publishable_key' => 'pk_test_...',
                            'secret_key' => 'sk_test_...',
                            'webhook_secret' => 'whsec_...',
                        ],
                        PaymentProvider::Teya => [
                            'merchant_id' => 'TEYA-DEMO',
                            'secret_key' => 'TEYA-SECRET',
                            'api_url' => 'https://demo.nevogate.com/',
                        ],
                        PaymentProvider::Cash => null,
                    },
                ],
            );
        }

        $workers = Accommodation::query()->where('slug', 'munkasszallas-a-szarny')->first();

        if ($workers) {
            $roomA = Room::query()->updateOrCreate(
                ['accommodation_id' => $workers->id, 'code' => 'A1'],
                [
                    'name' => 'A szárny – 1. szoba',
                    'capacity' => 4,
                    'ifa_per_person_night' => 300,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            );

            foreach (['Ágy 1', 'Ágy 2', 'Ágy 3', 'Ágy 4'] as $index => $bedName) {
                Bed::query()->updateOrCreate(
                    ['room_id' => $roomA->id, 'name' => $bedName],
                    [
                        'code' => 'A1-'.($index + 1),
                        'bed_type' => 'single',
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ],
                );
            }

            $roomB = Room::query()->updateOrCreate(
                ['accommodation_id' => $workers->id, 'code' => 'A2'],
                [
                    'name' => 'A szárny – 2. szoba',
                    'capacity' => 4,
                    'ifa_per_person_night' => 300,
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            );

            foreach (['Ágy 1', 'Ágy 2', 'Ágy 3', 'Ágy 4'] as $index => $bedName) {
                Bed::query()->updateOrCreate(
                    ['room_id' => $roomB->id, 'name' => $bedName],
                    [
                        'code' => 'A2-'.($index + 1),
                        'bed_type' => 'single',
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }

        foreach (Accommodation::query()->where('type', '!=', 'workers_lodging')->get() as $accommodation) {
            IcalFeed::query()->updateOrCreate(
                [
                    'accommodation_id' => $accommodation->id,
                    'name' => 'Alap export',
                ],
                [
                    'export_token' => Str::random(40),
                    'is_active' => true,
                ],
            );
        }
    }
}
