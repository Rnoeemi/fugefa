@if ($accommodation)
    <div class="ts-dyn-details__inner">
        @if (($showTitle ?? true) && filled($title))
            <p class="ts-dyn-details__eyebrow" data-ts-text="title">{!! $title !!}</p>
        @endif
        <div class="ts-dyn-details__grid">
            <div>
                <p class="ts-dyn-details__type">{{ $accommodation->type->getLabel() }}</p>
                <h2>{{ $accommodation->name }}</h2>
                <div class="ts-dyn-details__body">{{ $accommodation->description ?: 'A részletes leírás hamarosan elérhető.' }}</div>
                <div class="ts-dyn-details__actions">
                    <a href="{{ route('accommodations.show', $accommodation) }}">Teljes oldal</a>
                    <a class="is-primary" href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}">Foglalás</a>
                </div>
            </div>
            <aside>
                <h3>Árak</h3>
                <ul>
                    <li>Alapár: {{ number_format((float) ($accommodation->base_price ?? 0), 0, ',', ' ') }} Ft / éj</li>
                    <li>IFA: {{ number_format((float) $accommodation->ifa_per_person_night, 0, ',', ' ') }} Ft / fő / éj</li>
                    <li>Kapacitás: {{ $accommodation->capacity }} fő</li>
                    <li>Min. éjszakák: {{ $accommodation->min_nights }}</li>
                </ul>
                <h3>Jellemzők</h3>
                @if (! empty($accommodation->amenities))
                    <ul>
                        @foreach ($accommodation->amenities as $amenity)
                            <li>{{ $amenity }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="ts-dyn-empty">A jellemzők feltöltése folyamatban.</p>
                @endif
            </aside>
        </div>
    </div>
@else
    <p class="ts-dyn-empty">Nincs megjeleníthető szállás.</p>
@endif
