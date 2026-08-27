<?php

use App\Enums\PaymentProvider;
use App\Models\PaymentImplementation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $provider = PaymentProvider::Cash;

        PaymentImplementation::query()->updateOrCreate(
            ['provider' => $provider->value],
            [
                'label' => $provider->getLabel(),
                'is_enabled' => false,
                'is_test_mode' => false,
                'demo_info' => $provider->demoInfo(),
                'test_credentials' => null,
                'live_credentials' => null,
            ],
        );
    }

    public function down(): void
    {
        PaymentImplementation::query()
            ->where('provider', PaymentProvider::Cash->value)
            ->delete();
    }
};
