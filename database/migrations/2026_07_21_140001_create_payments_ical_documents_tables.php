<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_implementations', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('label');
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->longText('test_credentials')->nullable();
            $table->longText('live_credentials')->nullable();
            $table->text('notes')->nullable();
            $table->text('demo_info')->nullable();
            $table->timestamps();

            $table->unique('provider');
        });

        Schema::create('accommodation_payment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('require_deposit')->default(false);
            $table->unsignedTinyInteger('deposit_percent')->nullable();
            $table->boolean('require_full_payment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['accommodation_id', 'starts_on', 'ends_on'], 'acc_pay_rule_range_idx');
        });

        Schema::create('ical_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('export_token', 64)->unique();
            $table->string('import_url')->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamp('last_exported_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('guest_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('id_card_front_path')->nullable();
            $table->string('id_card_back_path')->nullable();
            $table->string('address_card_front_path')->nullable();
            $table->string('document_number')->nullable();
            $table->string('full_name_on_document')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('address_on_card')->nullable();
            $table->boolean('name_matches')->default(false);
            $table->boolean('id_number_matches')->default(false);
            $table->boolean('address_present')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->boolean('ntak_ready')->default(false);
            $table->json('validation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_documents');
        Schema::dropIfExists('ical_feeds');
        Schema::dropIfExists('accommodation_payment_rules');
        Schema::dropIfExists('payment_implementations');
    }
};
