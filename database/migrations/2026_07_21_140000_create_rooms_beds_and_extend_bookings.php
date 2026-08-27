<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedTinyInteger('capacity')->default(1);
            $table->decimal('ifa_per_person_night', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['accommodation_id', 'is_active'], 'rooms_acc_active_idx');
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('bed_type')->default('single');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['room_id', 'is_active'], 'beds_room_active_idx');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('accommodation_id')->constrained()->nullOnDelete();
            $table->foreignId('bed_id')->nullable()->after('room_id')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('adults_count')->nullable()->after('guests_count');
            $table->unsignedTinyInteger('children_count')->nullable()->after('adults_count');
            $table->string('payment_status')->default('unpaid')->after('source');
            $table->string('payment_provider')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_provider');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_reference');
            $table->string('ical_uid')->nullable()->unique()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bed_id');
            $table->dropConstrainedForeignId('room_id');
            $table->dropColumn([
                'adults_count',
                'children_count',
                'payment_status',
                'payment_provider',
                'payment_reference',
                'amount_paid',
                'ical_uid',
            ]);
        });

        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
    }
};
