<?php

namespace Database\Seeders;

use App\Enums\AccommodationType;
use App\Enums\PaymentProvider;
use App\Models\Accommodation;
use App\Models\AccommodationPaymentRule;
use App\Models\AccommodationRatePeriod;
use App\Models\PaymentImplementation;
use App\Models\Room;
use App\Models\SitePage;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * A `php artisan site:export-snapshot` által mentett JSON-ökből tölti vissza
 * a webhely tartalmát (oldalak, fejléc/lábléc, szállások, menü, fizetési módok flagjei).
 *
 * Deploy előtt: futtasd az exportot lokálisan, commitold a database/seeders/data mappát,
 * és másold fel a képeket is (public/images/site + storage/app/public).
 */
class CurrentSiteSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $dir = database_path('seeders/data');

        if (! File::isDirectory($dir) || ! File::exists($dir.'/site-settings.json')) {
            $this->command?->warn('Nincs site snapshot (database/seeders/data). Futtasd: php artisan site:export-snapshot');

            return;
        }

        $this->seedSettings($this->readJson($dir.'/site-settings.json'));
        $this->seedPages($this->readJson($dir.'/site-pages.json'));
        $this->seedAccommodations($this->readJson($dir.'/accommodations.json'));
        $this->seedPayments($this->readJson($dir.'/payments.json'));
        $this->seedMenus($this->readJson($dir.'/menus.json'));

        $this->command?->info('CurrentSiteSnapshotSeeder kész.');
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    protected function readJson(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException('Hiányzó snapshot fájl: '.$path);
        }

        $decoded = json_decode(File::get($path), true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Érvénytelen JSON: '.$path);
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function seedSettings(array $payload): void
    {
        $settings = SiteSetting::current();
        $settings->forceFill($payload)->save();
    }

    /**
     * @param  list<array<string, mixed>>  $pages
     */
    protected function seedPages(array $pages): void
    {
        $keepSlugs = [];

        foreach ($pages as $pageData) {
            $slug = (string) ($pageData['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $keepSlugs[] = $slug;

            SitePage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $pageData['title'] ?? $slug,
                    'html' => $pageData['html'] ?? '',
                    'css' => $pageData['css'] ?? '',
                    'grapes_data' => $pageData['grapes_data'] ?? null,
                    'seo' => $pageData['seo'] ?? null,
                    'is_published' => (bool) ($pageData['is_published'] ?? true),
                    'is_homepage' => (bool) ($pageData['is_homepage'] ?? false),
                    'sort_order' => (int) ($pageData['sort_order'] ?? 0),
                ],
            );
        }

        // Opcionális: ne töröljük a snapshotban nem szereplő oldalakat (biztonság).
        unset($keepSlugs);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function seedAccommodations(array $items): void
    {
        foreach ($items as $item) {
            $slug = (string) ($item['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $type = $item['type'] ?? AccommodationType::Guesthouse->value;
            if ($type instanceof AccommodationType) {
                $type = $type->value;
            }

            $accommodation = Accommodation::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $item['name'] ?? $slug,
                    'cover_image' => $item['cover_image'] ?? null,
                    'type' => $type,
                    'description' => $item['description'] ?? null,
                    'capacity' => (int) ($item['capacity'] ?? 1),
                    'base_price' => $item['base_price'] ?? null,
                    'ifa_per_person_night' => $item['ifa_per_person_night'] ?? 0,
                    'min_nights' => (int) ($item['min_nights'] ?? 1),
                    'price_from' => $item['price_from'] ?? null,
                    'amenities' => $item['amenities'] ?? [],
                    'is_active' => (bool) ($item['is_active'] ?? true),
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                ],
            );

            $roomNames = [];
            foreach ($item['rooms'] ?? [] as $index => $roomData) {
                $name = (string) ($roomData['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                Room::query()->updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'name' => $name,
                    ],
                    [
                        'code' => $roomData['code'] ?? null,
                        'capacity' => $roomData['capacity'] ?? null,
                        'ifa_per_person_night' => $roomData['ifa_per_person_night'] ?? null,
                        'is_active' => (bool) ($roomData['is_active'] ?? true),
                        'sort_order' => (int) ($roomData['sort_order'] ?? $index),
                        'notes' => $roomData['notes'] ?? null,
                        'images' => $roomData['images'] ?? [],
                    ],
                );
            }

            foreach ($item['rate_periods'] ?? [] as $period) {
                AccommodationRatePeriod::query()->updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'name' => $period['name'] ?? 'Árperiódus',
                        'starts_on' => $period['starts_on'] ?? null,
                    ],
                    [
                        'ends_on' => $period['ends_on'] ?? null,
                        'nightly_price' => $period['nightly_price'] ?? null,
                        'ifa_per_person_night' => $period['ifa_per_person_night'] ?? null,
                        'min_nights' => $period['min_nights'] ?? null,
                        'is_active' => (bool) ($period['is_active'] ?? true),
                    ],
                );
            }

            foreach ($item['payment_rules'] ?? [] as $rule) {
                AccommodationPaymentRule::query()->updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'name' => $rule['name'] ?? 'Fizetési szabály',
                        'starts_on' => $rule['starts_on'] ?? null,
                    ],
                    [
                        'ends_on' => $rule['ends_on'] ?? null,
                        'require_deposit' => (bool) ($rule['require_deposit'] ?? false),
                        'deposit_percent' => $rule['deposit_percent'] ?? null,
                        'require_full_payment' => (bool) ($rule['require_full_payment'] ?? false),
                        'is_active' => (bool) ($rule['is_active'] ?? true),
                    ],
                );
            }
        }
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function seedPayments(array $items): void
    {
        // Minden ismert provider legyen jelen (credential nélkül).
        foreach (PaymentProvider::cases() as $provider) {
            PaymentImplementation::query()->firstOrCreate(
                ['provider' => $provider->value],
                [
                    'label' => $provider->getLabel(),
                    'is_enabled' => false,
                    'is_default' => false,
                    'is_test_mode' => $provider->hasCredentialSettings(),
                    'demo_info' => $provider->demoInfo(),
                ],
            );
        }

        foreach ($items as $item) {
            $provider = (string) ($item['provider'] ?? '');
            if ($provider === '' || ! PaymentProvider::tryFrom($provider)) {
                continue;
            }

            PaymentImplementation::query()->updateOrCreate(
                ['provider' => $provider],
                [
                    'label' => $item['label'] ?? PaymentProvider::from($provider)->getLabel(),
                    'is_enabled' => (bool) ($item['is_enabled'] ?? false),
                    'is_default' => (bool) ($item['is_default'] ?? false),
                    'is_test_mode' => (bool) ($item['is_test_mode'] ?? true),
                    'notes' => $item['notes'] ?? null,
                    'demo_info' => $item['demo_info'] ?? PaymentProvider::from($provider)->demoInfo(),
                ],
            );
        }
    }

    /**
     * @param  array{locations?: list<array<string, mixed>>, menus?: list<array<string, mixed>>, items?: list<array<string, mixed>>}  $payload
     */
    protected function seedMenus(array $payload): void
    {
        if (! DB::getSchemaBuilder()->hasTable('fmm_menus')) {
            return;
        }

        DB::transaction(function () use ($payload): void {
            // Teljes menü-csere a snapshot alapján (stabil deploy).
            DB::table('fmm_menu_items')->delete();
            DB::table('fmm_menus')->delete();
            DB::table('fmm_menu_locations')->delete();

            foreach ($payload['locations'] ?? [] as $location) {
                unset($location['created_at'], $location['updated_at']);
                DB::table('fmm_menu_locations')->insert($location);
            }

            foreach ($payload['menus'] ?? [] as $menu) {
                unset($menu['created_at'], $menu['updated_at']);
                DB::table('fmm_menus')->insert($menu);
            }

            foreach ($payload['items'] ?? [] as $item) {
                unset($item['created_at'], $item['updated_at']);

                if (($item['linkable_type'] ?? null) === SitePage::class) {
                    $slug = (string) ($item['linkable_slug'] ?? '');
                    unset($item['linkable_slug']);
                    if ($slug !== '') {
                        $item['linkable_id'] = SitePage::query()->where('slug', $slug)->value('id');
                    }
                } else {
                    unset($item['linkable_slug']);
                }

                // Lokális abszolút URL → relatív / production-barát
                if (is_string($item['url'] ?? null) && str_contains((string) $item['url'], '://')) {
                    $path = parse_url((string) $item['url'], PHP_URL_PATH) ?: '/';
                    $item['url'] = $path === '' ? '/' : $path;
                }

                DB::table('fmm_menu_items')->insert($item);
            }
        });
    }
}
