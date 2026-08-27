<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->longText('header_html')->nullable()->after('footer_text');
            $table->longText('header_css')->nullable()->after('header_html');
            $table->json('header_grapes_data')->nullable()->after('header_css');
            $table->longText('footer_html')->nullable()->after('header_grapes_data');
            $table->longText('footer_css')->nullable()->after('footer_html');
            $table->json('footer_grapes_data')->nullable()->after('footer_css');
            $table->string('font_sans')->nullable()->after('footer_grapes_data');
            $table->string('font_display')->nullable()->after('font_sans');
            $table->string('font_size_base')->nullable()->after('font_display');
            $table->string('line_height')->nullable()->after('font_size_base');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'header_html',
                'header_css',
                'header_grapes_data',
                'footer_html',
                'footer_css',
                'footer_grapes_data',
                'font_sans',
                'font_display',
                'font_size_base',
                'line_height',
            ]);
        });
    }
};
