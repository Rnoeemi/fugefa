<?php

namespace App\Console\Commands;

use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Services\SitePageCssWriter;
use App\Services\SiteThemeService;
use Illuminate\Console\Command;

class WritePublicCssCommand extends Command
{
    protected $signature = 'site:write-public-css';

    protected $description = 'Téma-, fejléc-, lábléc- és oldal-CSS újraírása az adatbázisból';

    public function handle(SiteThemeService $theme, SitePageCssWriter $writer): int
    {
        $settings = SiteSetting::current();

        $theme->writeThemeCss($settings);
        $theme->writeCustomCss($settings);
        $writer->writeLayoutPart('header', (string) ($settings->header_css ?? ''));
        $writer->writeLayoutPart('footer', (string) ($settings->footer_css ?? ''));

        $pages = SitePage::query()->orderBy('id')->get();
        foreach ($pages as $page) {
            $writer->write($page, (string) ($page->css ?? ''));
        }

        $this->info('CSS kiírva: téma, egyedi, fejléc, lábléc, '.$pages->count().' oldal.');

        return self::SUCCESS;
    }
}
