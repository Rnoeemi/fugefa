<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('slug');
            $table->decimal('base_price', 10, 2)->nullable()->after('capacity');
            $table->decimal('ifa_per_person_night', 10, 2)->default(0)->after('base_price');
            $table->unsignedTinyInteger('min_nights')->default(1)->after('ifa_per_person_night');
        });

        DB::table('accommodations')->orderBy('id')->each(function ($row): void {
            DB::table('accommodations')->where('id', $row->id)->update([
                'base_price' => $row->price_from,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'base_price', 'ifa_per_person_night', 'min_nights']);
        });
    }
};
