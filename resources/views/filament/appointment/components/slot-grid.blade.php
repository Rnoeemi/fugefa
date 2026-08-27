@php
    /** @var list<array{time: string, starts_at: string, state: string, selectable: bool, label: string}> $cells */
    $cells = $cells ?? [];
    /** @var list<string> $selected */
    $selected = $selected ?? [];
    $gridKey = $gridKey ?? 'slot-grid';
@endphp

<div class="appointment-slot-grid" wire:key="slot-grid-{{ $gridKey }}">
    <div class="appointment-slot-grid__legend">
        <span><i class="is-outside"></i> Munkaidőn kívül</span>
        <span><i class="is-booked"></i> Foglalt</span>
        <span><i class="is-available"></i> Szabad</span>
        <span><i class="is-selected"></i> Kiválasztott</span>
    </div>

    @if (count($cells) === 0)
        <p class="appointment-slot-grid__empty">Válasszon munkatársat, csomagot és napot.</p>
    @else
        <div class="appointment-slot-grid__rows">
            @foreach ($cells as $cell)
                @php
                    $isSelected = in_array($cell['starts_at'], $selected, true);
                    $state = $isSelected ? 'selected' : $cell['state'];
                    $canClick = (bool) ($cell['selectable'] ?? false);
                @endphp

                @if ($canClick)
                    <button
                        type="button"
                        class="appointment-slot appointment-slot--{{ $state }}"
                        wire:click="selectSlot('{{ $cell['starts_at'] }}')"
                        title="{{ $cell['label'] }}"
                    >
                        {{ $cell['label'] }}
                    </button>
                @else
                    <div
                        class="appointment-slot appointment-slot--{{ $state }} is-disabled"
                        title="{{ $cell['label'] }}"
                    >
                        {{ $cell['label'] }}
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
