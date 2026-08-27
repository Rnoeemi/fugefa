<?php

namespace App\Console\Commands;

use App\Models\IcalFeed;
use App\Services\IcalSyncService;
use Illuminate\Console\Command;

class SyncIcalFeedsCommand extends Command
{
    protected $signature = 'ical:sync {--feed= : Egy konkrét iCal feed ID}';

    protected $description = 'Külső iCal feedek importálása (Airbnb / Booking.com stb.)';

    public function handle(IcalSyncService $sync): int
    {
        $query = IcalFeed::query()
            ->with('accommodation')
            ->where('is_active', true)
            ->whereNotNull('import_url');

        if ($this->option('feed')) {
            $query->whereKey($this->option('feed'));
        }

        $feeds = $query->get();
        $total = 0;

        foreach ($feeds as $feed) {
            if ($feed->accommodation?->isAdminOnlyBooking() || ! $feed->accommodation?->supportsIcal()) {
                $this->warn("Kihagyva: {$feed->name} (#{$feed->id})");

                continue;
            }

            $imported = $sync->import($feed);
            $feed->update(['last_imported_at' => now()]);
            $total += $imported;
            $this->info("{$feed->name}: {$imported} esemény");
        }

        $this->info("Összesen importálva: {$total}");

        return self::SUCCESS;
    }
}
