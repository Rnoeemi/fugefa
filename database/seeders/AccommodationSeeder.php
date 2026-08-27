<?php

namespace Database\Seeders;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\AccommodationRatePeriod;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Tüsí Vendégház',
                'slug' => 'tusi-vendeghaz',
                'cover_image' => 'images/site/vendeghaz.jpg',
                'type' => AccommodationType::Guesthouse,
                'description' => "A teljes vendégház bérelhető családoknak és baráti társaságoknak.\nKözösségi nappali, felszerelt konyha és kert várja a vendégeket.",
                'capacity' => 8,
                'base_price' => 45000,
                'ifa_per_person_night' => 500,
                'min_nights' => 2,
                'price_from' => 45000,
                'amenities' => ['Wifi', 'Parkoló', 'Konyha', 'Kert', 'Klíma'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Napfényes szoba',
                'slug' => 'napfenyes-szoba',
                'cover_image' => 'images/site/szoba.jpg',
                'type' => AccommodationType::Room,
                'description' => "Kényelmes kétágyas szoba saját fürdővel.\nIdeális pároknak vagy egyéni utazóknak.",
                'capacity' => 2,
                'base_price' => 18000,
                'ifa_per_person_night' => 500,
                'min_nights' => 1,
                'price_from' => 18000,
                'amenities' => ['Wifi', 'Klíma', 'Fürdőszoba'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Munkásszállás – A szárny',
                'slug' => 'munkasszallas-a-szarny',
                'cover_image' => 'images/site/munkasszallas.jpg',
                'type' => AccommodationType::WorkersLodging,
                'description' => 'Hosszabb távú elhelyezés. Csak adminisztrátor rögzíthet foglalást.',
                'capacity' => 12,
                'base_price' => 8000,
                'ifa_per_person_night' => 0,
                'min_nights' => 7,
                'price_from' => 8000,
                'amenities' => ['Wifi', 'Közös konyha'],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            $accommodation = Accommodation::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item,
            );

            if ($accommodation->slug === 'tusi-vendeghaz') {
                AccommodationRatePeriod::query()->updateOrCreate(
                    [
                        'accommodation_id' => $accommodation->id,
                        'name' => 'Nyári főszezon',
                    ],
                    [
                        'starts_on' => '2026-07-01',
                        'ends_on' => '2026-08-31',
                        'nightly_price' => 55000,
                        'ifa_per_person_night' => 500,
                        'min_nights' => 3,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
