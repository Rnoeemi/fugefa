<div class="ts-dyn-map__inner">
    <div class="ts-dyn-map__copy">
        @if (($showTitle ?? true) && filled($title))
            <h2 data-ts-text="title">{!! $title !!}</h2>
        @endif
        @if ($settings->address)
            <p>{{ $settings->address }}</p>
        @endif
        @if (($showText ?? true) && filled($text))
            <p class="ts-dyn-map__note" data-ts-text="text">{!! $text !!}</p>
        @endif
        @if ($settings->phone)
            <p><a href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}">{{ $settings->phone }}</a></p>
        @endif
    </div>
    <div class="ts-dyn-map__frame">
        <iframe src="{{ $embedUrl }}" title="Térkép" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </div>
</div>
