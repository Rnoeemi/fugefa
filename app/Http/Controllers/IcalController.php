<?php

namespace App\Http\Controllers;

use App\Models\IcalFeed;
use App\Services\IcalSyncService;
use Symfony\Component\HttpFoundation\Response;

class IcalController extends Controller
{
    public function export(string $token, IcalSyncService $sync): Response
    {
        $feed = IcalFeed::query()
            ->where('export_token', $token)
            ->where('is_active', true)
            ->with('accommodation')
            ->firstOrFail();

        abort_unless($feed->accommodation && $feed->accommodation->supportsIcal(), 404);

        $ics = $sync->export($feed->accommodation);
        $feed->update(['last_exported_at' => now()]);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$feed->accommodation->slug.'.ics"',
        ]);
    }
}
