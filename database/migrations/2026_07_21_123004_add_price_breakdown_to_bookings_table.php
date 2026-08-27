<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('ifa_total', 10, 2)->nullable()->after('total_price');
            $table->decimal('accommodation_total', 10, 2)->nullable()->after('total_price');
            $table->json('price_breakdown')->nullable()->after('ifa_total');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['ifa_total', 'accommodation_total', 'price_breakdown']);
        });
    }
};
