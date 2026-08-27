<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_implementations', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_enabled');
        });

        $defaultId = DB::table('payment_implementations')
            ->where('is_enabled', true)
            ->orderBy('id')
            ->value('id');

        if ($defaultId) {
            DB::table('payment_implementations')
                ->where('id', $defaultId)
                ->update(['is_default' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('payment_implementations', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
