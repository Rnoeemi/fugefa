<?php

namespace App\Console\Commands;

use App\Models\Accommodation;
use App\Models\PaymentImplementation;
use App\Models\SitePage;
use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportSiteSnapshotCommand extends Command
{
    protected $signature = 'site:export-snapshot
                            {--path=database/seeders/data : Célmappa a JSON fájloknak}';

    protected $description = 'A jelenlegi webhely tartalom exportálása seeder JSON-be (deploy / seed)';

    public function handle(): int
    {
        $relative = trim(str_replace('\\', '/', (string) $this->option('path')), '/');
        $dir = base_path($relative);
        File::ensureDirectoryExists($dir);

        $settings = SiteSetting::current();
        $settingsPayload = $settings->only([
            'site_name',
            'phone',
            'email',
            'notification_email',
            'contact_notification_email',
            'address',
            'hero_image',
            'footer_text',
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
            'typography',
            'global_colors',
            'button_styles',
            'style_preset',
            'custom_css',
            'modules',
        ]);

        $pages = SitePage::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (SitePage $page) => $page->only([
                'title',
                'slug',
                'html',
                'css',
                'grapes_data',
                'seo',
                'is_published',
                'is_homepage',
                'sort_order',
            ]))
            ->values()
            ->all();

        $accommodations = Accommodation::query()
            ->with([
                'rooms' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'ratePeriods',
                'paymentRules',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Accommodation $accommodation) {
                return [
                    ...$accommodation->only([
                        'name',
                        'slug',
                        'cover_image',
                        'type',
                        'description',
                        'capacity',
                        'base_price',
                        'ifa_per_person_night',
                        'min_nights',
                        'price_from',
                        'amenities',
                        'is_active',
                        'sort_order',
                    ]),
                    'type' => $accommodation->type?->value,
                    'rooms' => $accommodation->rooms->map(fn ($room) => $room->only([
                        'name',
                        'code',
                        'capacity',
                        'ifa_per_person_night',
                        'is_active',
                        'sort_order',
                        'notes',
                        'images',
                    ]))->values()->all(),
                    'rate_periods' => $accommodation->ratePeriods->map(fn ($period) => [
                        'name' => $period->name,
                        'starts_on' => optional($period->starts_on)?->format('Y-m-d'),
                        'ends_on' => optional($period->ends_on)?->format('Y-m-d'),
                        'nightly_price' => $period->nightly_price,
                        'ifa_per_person_night' => $period->ifa_per_person_night,
                        'min_nights' => $period->min_nights,
                        'is_active' => $period->is_active,
                    ])->values()->all(),
                    'payment_rules' => $accommodation->paymentRules->map(fn ($rule) => [
                        'name' => $rule->name,
                        'starts_on' => optional($rule->starts_on)?->format('Y-m-d'),
                        'ends_on' => optional($rule->ends_on)?->format('Y-m-d'),
                        'require_deposit' => $rule->require_deposit,
                        'deposit_percent' => $rule->deposit_percent,
                        'require_full_payment' => $rule->require_full_payment,
                        'is_active' => $rule->is_active,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();

        $payments = PaymentImplementation::query()
            ->orderBy('id')
            ->get()
            ->map(fn (PaymentImplementation $item) => [
                'provider' => $item->provider->value,
                'label' => $item->label,
                'is_enabled' => $item->is_enabled,
                'is_default' => $item->is_default,
                'is_test_mode' => $item->is_test_mode,
                'notes' => $item->notes,
                'demo_info' => $item->demo_info,
                // Éles kulcsok NEM mennek seedbe.
                'test_credentials' => null,
                'live_credentials' => null,
            ])
            ->values()
            ->all();

        $pageIdToSlug = SitePage::query()->pluck('slug', 'id')->all();

        $menus = [
            'locations' => DB::table('fmm_menu_locations')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'menus' => DB::table('fmm_menus')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'items' => DB::table('fmm_menu_items')->orderBy('id')->get()->map(function ($row) use ($pageIdToSlug) {
                $item = (array) $row;
                if (($item['linkable_type'] ?? null) === SitePage::class && filled($item['linkable_id'] ?? null)) {
                    $item['linkable_slug'] = $pageIdToSlug[(int) $item['linkable_id']] ?? null;
                }

                return $item;
            })->all(),
        ];

        $meta = [
            'exported_at' => now()->toIso8601String(),
            'app_url' => config('app.url'),
            'notes' => [
                'A képfájlok (public/images/site, storage/app/public) külön másolandók a szerverre.',
                'Fizetési API kulcsokat az adminban kell újra beállítani.',
            ],
        ];

        $this->writeJson($dir.'/meta.json', $meta);
        $this->writeJson($dir.'/site-settings.json', $settingsPayload);
        $this->writeJson($dir.'/site-pages.json', $pages);
        $this->writeJson($dir.'/accommodations.json', $accommodations);
        $this->writeJson($dir.'/payments.json', $payments);
        $this->writeJson($dir.'/menus.json', $menus);

        $this->info('Snapshot exportálva: '.$dir);
        $this->line('  pages='.count($pages).' accommodations='.count($accommodations).' payments='.count($payments));

        return self::SUCCESS;
    }

    protected function writeJson(string $path, mixed $data): void
    {
        File::put(
            $path,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)."\n"
        );
    }
}
