@if ($accommodation)
    <div class="ts-dyn-featured__inner">
        @if (($showTitle ?? true) && filled($title))
            <p class="ts-dyn-featured__eyebrow" data-ts-text="title">{!! $title !!}</p>
        @endif
        <div class="ts-dyn-featured__grid">
            <a class="ts-dyn-featured__media" href="{{ route('accommodations.show', $accommodation) }}">
                <img src="{{ $accommodation->coverUrl() }}" alt="{{ $accommodation->name }}" loading="lazy">
            </a>
            <div class="ts-dyn-featured__body">
                <p class="ts-dyn-featured__type">{{ $accommodation->type->getLabel() }}</p>
                <h2>{{ $accommodation->name }}</h2>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags($accommodation->description ?? ''), 220) }}</p>
                @if ($accommodation->displayPriceFrom())
                    <p class="ts-dyn-featured__price">
                        {{ number_format($accommodation->displayPriceFrom(), 0, ',', ' ') }} Ft / éj
                        · IFA {{ number_format((float) $accommodation->ifa_per_person_night, 0, ',', ' ') }} Ft/fő/éj
                    </p>
                @endif
                <div class="ts-dyn-featured__actions">
                    <a href="{{ route('accommodations.show', $accommodation) }}">Részletek</a>
                    <a class="is-primary" href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}">Foglalás</a>
                </div>
            </div>
        </div>
    </div>
@else
    <p class="ts-dyn-empty">Nincs kiemelhető szállás.</p>
@endif
