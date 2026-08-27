<?php

namespace App\Filament\Widgets;

use App\Enums\AccommodationType;
use App\Enums\GuestStatus;
use App\Filament\Resources\Accommodations\AccommodationResource;
use App\Filament\Resources\Guests\GuestResource;
use App\Filament\Resources\SitePages\SitePageResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Accommodation;
use App\Models\Bed;
use App\Models\Guest;
use App\Models\GuestDocument;
use App\Models\IcalFeed;
use App\Models\PaymentImplementation;
use App\Models\Room;
use App\Models\SitePage;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -24;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Vendégek, szállások és rendszer';

    protected ?string $description = 'Vendégállomány, kapacitás, dokumentumok és tartalom.';

    protected int | array | null $columns = [
        'default' => 2,
        'md' => 3,
        'xl' => 4,
    ];

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User;
    }

    protected function getStats(): array
    {
        $guestsTotal = Guest::query()->count();
        $blacklisted = Guest::query()->where('status', GuestStatus::Blacklisted)->count();
        $problematic = Guest::query()->where('status', GuestStatus::Problematic)->count();
        $newGuestsMonth = Guest::query()->where('created_at', '>=', now()->startOfMonth())->count();

        $accommodations = Accommodation::query()->count();
        $activeAccommodations = Accommodation::query()->where('is_active', true)->count();
        $guesthouses = Accommodation::query()->where('type', AccommodationType::Guesthouse)->count();
        $roomsType = Accommodation::query()->where('type', AccommodationType::Room)->count();
        $workers = Accommodation::query()->where('type', AccommodationType::WorkersLodging)->count();

        $rooms = Room::query()->count();
        $activeRooms = Room::query()->where('is_active', true)->count();
        $beds = Bed::query()->count();
        $activeBeds = Bed::query()->where('is_active', true)->count();

        $docsTotal = GuestDocument::query()->count();
        $docsValidated = GuestDocument::query()->where('is_validated', true)->count();
        $docsPending = GuestDocument::query()->where('is_validated', false)->count();
        $docsNtak = GuestDocument::query()->where('ntak_ready', true)->count();

        $pages = SitePage::query()->count();
        $publishedPages = SitePage::query()->where('is_published', true)->count();
        $users = User::query()->count();
        $icalFeeds = IcalFeed::query()->count();
        $paymentProviders = PaymentImplementation::query()->where('is_enabled', true)->count();

        return [
            Stat::make('Vendégek', (string) $guestsTotal)
                ->description("Új ebben a hónapban: {$newGuestsMonth}")
                ->descriptionIcon(Heroicon::UserGroup)
                ->color('primary')
                ->url(GuestResource::getUrl('index')),
            Stat::make('Figyelendő vendégek', (string) ($problematic + $blacklisted))
                ->description("Problémás: {$problematic} · Feketelista: {$blacklisted}")
                ->descriptionIcon(Heroicon::ShieldExclamation)
                ->color(($problematic + $blacklisted) > 0 ? 'danger' : 'gray')
                ->url(GuestResource::getUrl('index')),
            Stat::make('Szállások', "{$activeAccommodations} / {$accommodations}")
                ->description("VH: {$guesthouses} · Szoba: {$roomsType} · Munkás: {$workers}")
                ->descriptionIcon(Heroicon::BuildingOffice)
                ->color('success')
                ->url(AccommodationResource::getUrl('index')),
            Stat::make('Szobák', "{$activeRooms} / {$rooms}")
                ->description('Aktív / összes')
                ->descriptionIcon(Heroicon::HomeModern)
                ->color('info')
                ->url(AccommodationResource::getUrl('index')),
            Stat::make('Ágyak', "{$activeBeds} / {$beds}")
                ->description('Munkásszállás kapacitás')
                ->descriptionIcon(Heroicon::Squares2x2)
                ->color('gray'),
            Stat::make('Vendégdokumentumok', (string) $docsTotal)
                ->description("Validált: {$docsValidated} · Vár: {$docsPending} · NTAK: {$docsNtak}")
                ->descriptionIcon(Heroicon::DocumentText)
                ->color($docsPending > 0 ? 'warning' : 'success')
                ->url(GuestResource::getUrl('index')),
            Stat::make('Weboldal oldalak', "{$publishedPages} / {$pages}")
                ->description('Publikált / összes')
                ->descriptionIcon(Heroicon::GlobeAlt)
                ->color('primary')
                ->url(SitePageResource::getUrl('index')),
            Stat::make('Rendszer', "{$users} admin")
                ->description("iCal: {$icalFeeds} · Fizetés: {$paymentProviders} aktív")
                ->descriptionIcon(Heroicon::Cog6Tooth)
                ->color('gray')
                ->url(UserResource::getUrl('index')),
        ];
    }
}
