<div class="ts-dyn-booking-cta__inner">
    @if (($showTitle ?? true) || ($showText ?? true))
        <div>
            @if (($showTitle ?? true) && filled($title))
                <h2 data-ts-text="title">{!! $title !!}</h2>
            @endif
            @if (($showText ?? true) && filled($text))
                <p data-ts-text="text">{!! $text !!}</p>
            @endif
        </div>
    @endif
    @if ($showButton ?? true)
        <a href="{{ route('accommodations.index') }}" data-ts-text="button">{!! $button !!}</a>
    @endif
</div>
