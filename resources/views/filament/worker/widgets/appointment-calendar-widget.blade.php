<x-filament-widgets::widget class="fi-wi-worker-appointment-calendar">
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
                    Az Ön időpontfoglalásai.
                </p>
            </div>

            <div class="bc__controls">
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
                            @foreach ($day['appointments'] as $appointment)
                                <a
                                    href="{{ $this->appointmentUrl($appointment) }}"
                                    class="bc__event bc__event--{{ $this->statusClass($appointment->status) }}"
                                    title="{{ $appointment->customer_name }} · {{ $appointment->starts_at->format('H:i') }}–{{ $appointment->ends_at->format('H:i') }}"
                                >
                                    <span class="bc__event-name">{{ $appointment->starts_at->format('H:i') }} {{ $appointment->customer_name }}</span>
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
                <span class="bc__dot bc__dot--checked_in"></span> Teljesítve
            </span>
        </div>
    </div>
</x-filament-widgets::widget>
