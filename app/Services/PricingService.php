<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\AccommodationPaymentRule;
use App\Models\Room;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class PricingService
{
    /**
     * @return array{
     *     nights: int,
     *     min_nights: int,
     *     accommodation_total: float,
     *     ifa_total: float,
     *     total: float,
     *     currency: string,
     *     adults_count: int,
     *     children_count: int,
     *     ifa_liable_guests: int,
     *     lines: list<array{date: string, nightly_price: float, ifa: float, source: string}>
     * }
     */
    public function quote(
        Accommodation $accommodation,
        CarbonInterface|string $checkIn,
        CarbonInterface|string $checkOut,
        int $guestsCount = 1,
        ?int $adultsCount = null,
        ?int $childrenCount = null,
        ?Room $room = null,
    ): array {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();
        $nights = (int) $checkIn->diffInDays($checkOut);

        $adultsCount ??= $guestsCount;
        $childrenCount ??= max(0, $guestsCount - $adultsCount);
        $ifaLiable = max(0, $adultsCount); // 18 év felett

        $ifaRate = $room
            ? $room->resolvedIfaPerPersonNight()
            : (float) ($accommodation->ifa_per_person_night ?? 0);

        $lines = [];
        $accommodationTotal = 0.0;
        $ifaTotal = 0.0;
        $cursor = $checkIn->copy();

        while ($cursor->lt($checkOut)) {
            $resolved = $this->resolveForDate($accommodation, $cursor);
            $nightlyIfa = $ifaRate * $ifaLiable;

            // Időszaki IFA felülírás, ha nincs szoba-szintű IFA
            if (! $room && isset($resolved['ifa'])) {
                $nightlyIfa = ((float) $resolved['ifa']) * $ifaLiable;
            }

            $lines[] = [
                'date' => $cursor->toDateString(),
                'nightly_price' => $resolved['nightly_price'],
                'ifa' => $nightlyIfa,
                'source' => $resolved['source'],
            ];

            $accommodationTotal += $resolved['nightly_price'];
            $ifaTotal += $nightlyIfa;
            $cursor->addDay();
        }

        return [
            'nights' => $nights,
            'min_nights' => $this->minNightsFor($accommodation, $checkIn),
            'accommodation_total' => round($accommodationTotal, 2),
            'ifa_total' => round($ifaTotal, 2),
            'total' => round($accommodationTotal + $ifaTotal, 2),
            'currency' => 'HUF',
            'adults_count' => $adultsCount,
            'children_count' => $childrenCount,
            'ifa_liable_guests' => $ifaLiable,
            'lines' => $lines,
        ];
    }

    public function minNightsFor(Accommodation $accommodation, CarbonInterface|string $checkIn): int
    {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $period = $this->periodFor($accommodation, $checkIn);

        if ($period && $period->min_nights !== null) {
            return max(1, (int) $period->min_nights);
        }

        return max(1, (int) ($accommodation->min_nights ?: 1));
    }

    public function paymentRuleFor(Accommodation $accommodation, CarbonInterface|string $checkIn): ?AccommodationPaymentRule
    {
        $checkIn = Carbon::parse($checkIn)->startOfDay();

        $rules = $accommodation->relationLoaded('paymentRules')
            ? $accommodation->paymentRules
            : $accommodation->paymentRules()->where('is_active', true)->get();

        return $rules
            ->filter(fn (AccommodationPaymentRule $rule): bool => $rule->is_active && $rule->covers($checkIn))
            ->sortByDesc('starts_on')
            ->first();
    }

    /**
     * @return array{amount_due: float, require_deposit: bool, require_full_payment: bool, deposit_percent: int|null}
     */
    public function paymentRequirement(Accommodation $accommodation, float $total, CarbonInterface|string $checkIn): array
    {
        $rule = $this->paymentRuleFor($accommodation, $checkIn);

        if (! $rule) {
            return [
                'amount_due' => 0.0,
                'require_deposit' => false,
                'require_full_payment' => false,
                'deposit_percent' => null,
            ];
        }

        if ($rule->require_full_payment) {
            return [
                'amount_due' => $total,
                'require_deposit' => false,
                'require_full_payment' => true,
                'deposit_percent' => 100,
            ];
        }

        if ($rule->require_deposit) {
            $percent = max(1, min(100, (int) ($rule->deposit_percent ?: 30)));

            return [
                'amount_due' => round($total * ($percent / 100), 2),
                'require_deposit' => true,
                'require_full_payment' => false,
                'deposit_percent' => $percent,
            ];
        }

        return [
            'amount_due' => 0.0,
            'require_deposit' => false,
            'require_full_payment' => false,
            'deposit_percent' => null,
        ];
    }

    /**
     * @return array{nightly_price: float, ifa: float, source: string, min_nights: int}
     */
    public function resolveForDate(Accommodation $accommodation, CarbonInterface|string $date): array
    {
        $date = Carbon::parse($date)->startOfDay();
        $period = $this->periodFor($accommodation, $date);

        $basePrice = (float) ($accommodation->base_price ?? $accommodation->price_from ?? 0);
        $baseIfa = (float) ($accommodation->ifa_per_person_night ?? 0);
        $baseMin = max(1, (int) ($accommodation->min_nights ?: 1));

        if (! $period) {
            return [
                'nightly_price' => $basePrice,
                'ifa' => $baseIfa,
                'source' => 'base',
                'min_nights' => $baseMin,
            ];
        }

        return [
            'nightly_price' => $period->nightly_price !== null ? (float) $period->nightly_price : $basePrice,
            'ifa' => $period->ifa_per_person_night !== null ? (float) $period->ifa_per_person_night : $baseIfa,
            'source' => $period->name ?: 'period',
            'min_nights' => $period->min_nights !== null ? max(1, (int) $period->min_nights) : $baseMin,
        ];
    }

    protected function periodFor(Accommodation $accommodation, CarbonInterface $date): ?\App\Models\AccommodationRatePeriod
    {
        $periods = $accommodation->relationLoaded('ratePeriods')
            ? $accommodation->ratePeriods
            : $accommodation->ratePeriods()->where('is_active', true)->get();

        return $periods
            ->filter(fn ($period): bool => $period->is_active && $period->covers($date))
            ->sortByDesc('starts_on')
            ->first();
    }
}
