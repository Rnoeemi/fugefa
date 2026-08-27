<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_rate_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->decimal('nightly_price', 10, 2)->nullable();
            $table->decimal('ifa_per_person_night', 10, 2)->nullable();
            $table->unsignedTinyInteger('min_nights')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['accommodation_id', 'starts_on', 'ends_on'], 'acc_rate_period_range_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_rate_periods');
    }
};
