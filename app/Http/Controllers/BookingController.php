<?php

namespace App\Http\Controllers;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\GuestStatus;
use App\Http\Requests\StorePublicBookingRequest;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Guest;
use App\Services\BookingAvailabilityService;
use App\Services\BookingMailService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request, ?Accommodation $accommodation = null): View
    {
        if (! $accommodation && $request->filled('accommodation')) {
            $accommodation = Accommodation::query()
                ->bookableOnline()
                ->where('slug', $request->string('accommodation'))
                ->first();
        }

        if ($accommodation) {
            abort_unless(
                $accommodation->is_active && ! $accommodation->isAdminOnlyBooking(),
                404,
            );
        }

        $accommodations = Accommodation::query()
            ->bookableOnline()
            ->with('ratePeriods')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('site.booking.create', [
            'accommodations' => $accommodations,
            'selected' => $accommodation,
            'prefill' => [
                'check_in' => $request->query('check_in'),
                'check_out' => $request->query('check_out'),
                'guests_count' => $request->query('guests', $request->query('guests_count')),
            ],
        ]);
    }

    public function quote(Request $request, PricingService $pricing): JsonResponse
    {
        $validated = $request->validate([
            'accommodation_id' => ['required', 'exists:accommodations,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $accommodation = Accommodation::query()
            ->with('ratePeriods')
            ->findOrFail($validated['accommodation_id']);

        abort_unless(
            $accommodation->is_active && ! $accommodation->isAdminOnlyBooking(),
            404,
        );

        return response()->json(
            $pricing->quote(
                $accommodation,
                $validated['check_in'],
                $validated['check_out'],
                (int) $validated['guests_count'],
            ),
        );
    }

    public function store(
        StorePublicBookingRequest $request,
        BookingAvailabilityService $availability,
        PricingService $pricing,
        BookingMailService $mailer,
    ): RedirectResponse {
        $accommodation = Accommodation::query()
            ->with('ratePeriods')
            ->findOrFail($request->integer('accommodation_id'));

        abort_unless(
            $accommodation->is_active && ! $accommodation->isAdminOnlyBooking(),
            404,
        );

        $guest = Guest::query()->firstOrNew([
            'email' => $request->string('email')->lower()->toString(),
        ]);

        if (! $guest->exists) {
            $guest->status = GuestStatus::Welcome;
        }

        if ($guest->isBlocked()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'A foglalást jelenleg nem tudjuk fogadni. Kérjük, vegye fel velünk a kapcsolatot telefonon.']);
        }

        $guest->fill([
            'name' => $request->string('name')->toString(),
            'phone' => $request->string('phone')->toString(),
        ]);

        if (! $guest->exists) {
            $guest->status = GuestStatus::Welcome;
        }

        $availability->assertCanBook(
            accommodation: $accommodation,
            guest: $guest,
            checkIn: $request->string('check_in')->toString(),
            checkOut: $request->string('check_out')->toString(),
            guestsCount: $request->integer('guests_count'),
            source: BookingSource::Website,
            enforceMinNights: true,
        );

        $quote = $pricing->quote(
            $accommodation,
            $request->string('check_in')->toString(),
            $request->string('check_out')->toString(),
            $request->integer('guests_count'),
        );

        $booking = DB::transaction(function () use ($request, $accommodation, $guest, $quote): Booking {
            $guest->save();

            return Booking::query()->create([
                'accommodation_id' => $accommodation->id,
                'guest_id' => $guest->id,
                'check_in' => $request->string('check_in')->toString(),
                'check_out' => $request->string('check_out')->toString(),
                'guests_count' => $request->integer('guests_count'),
                'status' => BookingStatus::Pending,
                'source' => BookingSource::Website,
                'total_price' => $quote['total'],
                'accommodation_total' => $quote['accommodation_total'],
                'ifa_total' => $quote['ifa_total'],
                'price_breakdown' => $quote,
                'notes' => $request->string('message')->toString() ?: null,
            ]);
        });

        $mailer->sendReceived($booking);
        app(\App\Services\BookingNotificationService::class)->notifyNewBooking($booking);

        $paymentRequirement = $pricing->paymentRequirement(
            $accommodation,
            $quote['total'],
            $request->string('check_in')->toString(),
        );

        if ($paymentRequirement['amount_due'] > 0 && $request->filled('payment_provider')) {
            try {
                $provider = \App\Enums\PaymentProvider::from($request->string('payment_provider')->toString());

                if ($provider->requiresOnlineCheckout()) {
                    $booking->update(['amount_paid' => $paymentRequirement['amount_due']]);
                }

                $url = app(\App\Services\PaymentGatewayService::class)->startCheckout($booking, $provider);

                if ($provider->requiresOnlineCheckout()) {
                    return redirect()->away($url);
                }
            } catch (\Throwable) {
                // fallback: thanks page
            }
        }

        return redirect()
            ->route('booking.thanks', $booking)
            ->with('status', 'Foglalási igényét rögzítettük.');
    }

    public function thanks(Booking $booking): View
    {
        abort_unless($booking->source === BookingSource::Website, 404);

        $booking->load(['accommodation', 'guest']);

        return view('site.booking.thanks', compact('booking'));
    }
}
