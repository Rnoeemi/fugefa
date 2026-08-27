<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Mai időpontok</x-slot>

        <div class="space-y-4">
            @forelse ($this->todayAppointments() as $appointment)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                {{ $appointment->customer_name }}
                                @if ($appointment->customer_phone)
                                    · {{ $appointment->customer_phone }}
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-gray-500">{{ $appointment->customer_email }}</p>
                        </div>
                        <span class="text-xs font-semibold uppercase tracking-wide text-teal-700 dark:text-teal-300">
                            {{ $appointment->status->getLabel() }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Ma nincs időpont.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
