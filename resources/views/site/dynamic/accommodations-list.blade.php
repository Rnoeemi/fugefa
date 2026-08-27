<div class="ts-dyn-list__inner">
    @if (($showTitle ?? true) && filled($title))
        <h2 class="ts-dyn-list__title" data-ts-text="title">{!! $title !!}</h2>
    @endif
    <div class="ts-dyn-list__rows">
        @forelse ($accommodations as $accommodation)
            <article class="ts-dyn-list__row">
                <div>
                    <p class="ts-dyn-list__type">{{ $accommodation->type->getLabel() }}</p>
                    <h3><a href="{{ route('accommodations.show', $accommodation) }}">{{ $accommodation->name }}</a></h3>
                    @if ($accommodation->displayPriceFrom())
                        <p class="ts-dyn-list__price">{{ number_format($accommodation->displayPriceFrom(), 0, ',', ' ') }} Ft / éj</p>
                    @endif
                </div>
                <a class="ts-dyn-list__cta" href="{{ url('/foglalas-panel/foglalas/'.$accommodation->slug) }}">Foglalás</a>
            </article>
        @empty
            <p class="ts-dyn-empty">Nincs megjeleníthető szállás.</p>
        @endforelse
    </div>
</div>
