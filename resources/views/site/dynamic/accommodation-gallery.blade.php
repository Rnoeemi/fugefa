@if ($accommodation)
    <div class="ts-dyn-gallery__inner">
        @if (($showTitle ?? true) && filled($title))
            <h2 class="ts-dyn-gallery__title" data-ts-text="title">{!! $title !!}</h2>
        @endif
        @if (count($images))
            <div class="ts-dyn-gallery__grid">
                @foreach ($images as $image)
                    <figure>
                        <img src="{{ $image }}" alt="{{ $accommodation->name }}" loading="lazy">
                    </figure>
                @endforeach
            </div>
        @else
            <p class="ts-dyn-empty">Nincs megjeleníthető kép.</p>
        @endif
    </div>
@else
    <p class="ts-dyn-empty">Nincs megjeleníthető szállás.</p>
@endif
