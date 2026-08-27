<?php

namespace App\Http\Controllers;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\SitePage;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function home(): View
    {
        $page = SitePage::homepage();

        if ($page) {
            return view('site.pages.show', [
                'page' => $page,
                'isBuilderPage' => true,
            ]);
        }

        $accommodations = Accommodation::query()
            ->bookableOnline()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('site.home', [
            'accommodations' => $accommodations,
            'types' => collect(AccommodationType::cases())
                ->reject(fn (AccommodationType $type) => $type->isAdminOnlyBooking()),
        ]);
    }

    public function show(string $slug): View
    {
        $page = SitePage::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.pages.show', [
            'page' => $page,
            'isBuilderPage' => true,
        ]);
    }
}
