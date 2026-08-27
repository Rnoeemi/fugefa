<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->time('work_starts_at')->default('09:00:00')->after('slot_duration_minutes');
            $table->time('work_ends_at')->default('17:00:00')->after('work_starts_at');
        });

        Schema::create('worker_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedInteger('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_id', 'is_active', 'sort_order']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('worker_package_id')->nullable()->after('worker_id')->constrained()->nullOnDelete();
            $table->string('package_name')->nullable()->after('worker_package_id');
            $table->unsignedSmallInteger('duration_minutes')->nullable()->after('package_name');
            $table->unsignedInteger('price')->nullable()->after('duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('worker_package_id');
            $table->dropColumn(['package_name', 'duration_minutes', 'price']);
        });

        Schema::dropIfExists('worker_packages');

        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['work_starts_at', 'work_ends_at']);
        });
    }
};
