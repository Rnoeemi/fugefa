<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('slug')->unique();
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
            $table->boolean('google_calendar_enabled')->default(false);
            $table->string('google_calendar_id')->nullable();
            $table->longText('google_credentials')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->string('google_event_id')->nullable();
            $table->timestamps();

            $table->index(['worker_id', 'starts_at']);
            $table->index(['worker_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('workers');
    }
};
