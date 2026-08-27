<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_implementations')) {
            return;
        }

        Schema::table('payment_implementations', function (Blueprint $table) {
            $table->longText('test_credentials')->nullable()->change();
            $table->longText('live_credentials')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_implementations')) {
            return;
        }

        Schema::table('payment_implementations', function (Blueprint $table) {
            $table->json('test_credentials')->nullable()->change();
            $table->json('live_credentials')->nullable()->change();
        });
    }
};
