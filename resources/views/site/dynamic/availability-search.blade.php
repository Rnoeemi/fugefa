<div class="ts-dyn-search__inner">
    @if (($showTitle ?? true) && filled($title))
        <h2 class="ts-dyn-search__title" data-ts-text="title">{!! $title !!}</h2>
    @endif
    @if (($showText ?? true) && filled($text))
        <p class="ts-dyn-search__text" data-ts-text="text">{!! $text !!}</p>
    @endif

    <form class="ts-dyn-search__form" method="get" action="{{ url('/foglalas-panel/foglalas') }}">
        <div class="ts-dyn-search__field ts-dyn-search__field--accommodation">
            <label for="ts-search-accommodation">Szállás</label>
            <select id="ts-search-accommodation" name="accommodation">
                <option value="">Összes / később választok</option>
                @foreach ($accommodations as $item)
                    <option value="{{ $item->slug }}" @selected($selected?->is($item))>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="ts-dyn-search__field">
            <label for="ts-search-check-in">Érkezés</label>
            <input id="ts-search-check-in" type="date" name="check_in" value="{{ $checkIn }}">
        </div>
        <div class="ts-dyn-search__field">
            <label for="ts-search-check-out">Távozás</label>
            <input id="ts-search-check-out" type="date" name="check_out" value="{{ $checkOut }}">
        </div>
        <div class="ts-dyn-search__field ts-dyn-search__field--guests">
            <label for="ts-search-guests">Vendégek</label>
            <input id="ts-search-guests" type="number" name="guests" min="1" max="20" value="{{ $guests }}">
        </div>
        @if ($showButton ?? true)
            <div class="ts-dyn-search__actions">
                <button type="submit" data-ts-text="button">{!! $button !!}</button>
            </div>
        @endif
    </form>
</div>
