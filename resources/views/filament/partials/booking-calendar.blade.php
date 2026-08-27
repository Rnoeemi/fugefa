@assets
    <link rel="stylesheet" href="{{ asset('css/booking-calendar.css') }}?v={{ @filemtime(public_path('css/booking-calendar.css')) }}">
@endassets

<div class="bc">
    <div class="bc__toolbar">
        <div>
            <h2 class="bc__title">
                {{ $this->currentMonth()->translatedFormat('Y. F') }}
            </h2>
            <p class="bc__subtitle">
                Aktív foglalások megjelenítése szállásonként.
            </p>
        </div>

        <div class="bc__controls">
            <x-filament::input.wrapper class="bc__select">
                <x-filament::input.select wire:model.live="accommodationId">
                    <option value="">Összes szállás</option>
                    @foreach ($this->accommodations() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::button color="gray" wire:click="previousMonth" icon="heroicon-m-chevron-left">
                Előző
            </x-filament::button>
            <x-filament::button color="gray" wire:click="goToday">
                Ma
            </x-filament::button>
            <x-filament::button color="gray" wire:click="nextMonth" icon="heroicon-m-chevron-right" icon-position="after">
                Következő
            </x-filament::button>
        </div>
    </div>

    <div class="bc__frame">
        <div class="bc__weekdays">
            @foreach (['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'] as $dayLabel)
                <div class="bc__weekday">{{ $dayLabel }}</div>
            @endforeach
        </div>

        <div class="bc__grid">
            @foreach ($this->calendarDays() as $day)
                <div @class([
                    'bc__day',
                    'bc__day--muted' => ! $day['isCurrentMonth'],
                    'bc__day--today' => $day['date']->isToday(),
                ])>
                    <div class="bc__day-num">
                        {{ $day['date']->format('j') }}
                    </div>

                    <div class="bc__events">
                        @foreach ($day['bookings'] as $booking)
                            <a
                                href="{{ $this->bookingUrl($booking) }}"
                                class="bc__event bc__event--{{ $this->statusClass($booking->status) }}"
                                title="{{ $booking->guest?->name }} · {{ $booking->accommodation?->name }}"
                            >
                                <span class="bc__event-name">{{ $booking->guest?->name }}</span>
                                <span class="bc__event-meta">· {{ $booking->accommodation?->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bc__legend">
        <span class="bc__legend-item">
            <span class="bc__dot bc__dot--pending"></span> Függőben
        </span>
        <span class="bc__legend-item">
            <span class="bc__dot bc__dot--confirmed"></span> Megerősítve
        </span>
        <span class="bc__legend-item">
            <span class="bc__dot bc__dot--checked_in"></span> Bejelentkezett
        </span>
    </div>
</div>
