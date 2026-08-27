<div class="ts-dyn-cards__inner">
    @if (($showTitle ?? true) && filled($title))
        <h2 class="ts-dyn-cards__title" data-ts-text="title">{!! $title !!}</h2>
    @endif

    <div class="ts-dyn-cards__grid">
        @forelse ($accommodations as $accommodation)
            <article class="ts-dyn-card">
                <a class="ts-dyn-card__media" href="{{ route('accommodations.show', $accommodation) }}">
                    <img src="{{ $accommodation->coverUrl() }}" alt="{{ $accommodation->name }}" loading="lazy">
                </a>
                <div class="ts-dyn-card__body">
                    <p class="ts-dyn-card__type">{{ $accommodation->type->getLabel() }}</p>
                    <h3 class="ts-dyn-card__name">
                        <a href="{{ route('accommodations.show', $accommodation) }}">{{ $accommodation->name }}</a>
                    </h3>
                    <p class="ts-dyn-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags($accommodation->description ?? ''), 140) }}</p>
                    @if ($accommodation->displayPriceFrom())
                        <p class="ts-dyn-card__price">
                            <span class="ts-dyn-card__price-amount">{{ number_format($accommodation->displayPriceFrom(), 0, ',', ' ') }} Ft</span>
                            <span class="ts-dyn-card__price-unit">/ éj</span>
                            @if ($accommodation->min_nights)
                                <span class="ts-dyn-card__price-note">min. {{ $accommodation->min_nights }} éj</span>
                            @endif
                        </p>
                    @endif
                    <div class="ts-dyn-card__actions">
                        <a class="ts-dyn-card__cta" href="{{ route('accommodations.show', $accommodation) }}">Részletek</a>
                        <a class="ts-dyn-card__cta ts-dyn-card__cta--outline" href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}">Foglalás</a>
                    </div>
                </div>
            </article>
        @empty
            <p class="ts-dyn-empty">Jelenleg nincs nyilvánosan foglalható szállás.</p>
        @endforelse
    </div>
</div>
