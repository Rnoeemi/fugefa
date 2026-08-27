<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-lg font-semibold">Mai érkezések</h2>
            <div class="mt-4 space-y-3">
                @forelse ($this->arrivalsToday() as $booking)
                    <div class="rounded-lg bg-emerald-50 px-3 py-2 text-sm dark:bg-emerald-400/10">
                        <div class="font-semibold">{{ $booking->guest?->name }}</div>
                        <div>{{ $booking->accommodation?->name }} @if($booking->bed) · {{ $booking->bed->label() }} @endif</div>
                        <div class="opacity-70">{{ $booking->guests_count }} fő · {{ $booking->status->getLabel() }}</div>
                    </div>
                @empty
                    <p class="text-sm opacity-60">Nincs mai érkezés.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-lg font-semibold">Mai távozások</h2>
            <div class="mt-4 space-y-3">
                @forelse ($this->departuresToday() as $booking)
                    <div class="rounded-lg bg-amber-50 px-3 py-2 text-sm dark:bg-amber-400/10">
                        <div class="font-semibold">{{ $booking->guest?->name }}</div>
                        <div>{{ $booking->accommodation?->name }} @if($booking->bed) · {{ $booking->bed->label() }} @endif</div>
                    </div>
                @empty
                    <p class="text-sm opacity-60">Nincs mai távozás.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-lg font-semibold">Házban lévő vendégek</h2>
            <div class="mt-4 space-y-3">
                @forelse ($this->inHouse() as $booking)
                    <div class="rounded-lg bg-sky-50 px-3 py-2 text-sm dark:bg-sky-400/10">
                        <div class="font-semibold">{{ $booking->guest?->name }}</div>
                        <div>{{ $booking->accommodation?->name }} @if($booking->bed) · {{ $booking->bed->label() }} @endif</div>
                        <div class="opacity-70">{{ $booking->check_in->format('m.d.') }} – {{ $booking->check_out->format('m.d.') }}</div>
                    </div>
                @empty
                    <p class="text-sm opacity-60">Jelenleg nincs vendég a házban.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
