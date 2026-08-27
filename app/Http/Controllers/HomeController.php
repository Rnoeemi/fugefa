<?php

namespace App\Http\Controllers;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
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
}
