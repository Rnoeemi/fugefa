<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(): View
    {
        $accommodations = Accommodation::query()
            ->bookableOnline()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('site.accommodations.index', compact('accommodations'));
    }

    public function show(Accommodation $accommodation): View
    {
        abort_unless(
            $accommodation->is_active && ! $accommodation->isAdminOnlyBooking(),
            404,
        );

        $accommodation->load([
            'rooms' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name'),
        ]);

        return view('site.accommodations.show', compact('accommodation'));
    }

    public function availability(Request $request, Accommodation $accommodation, BookingAvailabilityService $availability): JsonResponse
    {
        abort_unless(
            $accommodation->is_active && ! $accommodation->isAdminOnlyBooking(),
            404,
        );

        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after:from'],
        ]);

        return response()->json([
            'occupied' => $availability->occupiedDates(
                $accommodation,
                $validated['from'],
                $validated['to'],
            ),
        ]);
    }
}
