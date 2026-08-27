<div class="ts-dyn-appointment__inner">
    @if (($showTitle ?? true) && filled($title))
        <h2 class="ts-dyn-appointment__title" data-ts-text="title">{!! $title !!}</h2>
    @endif
    @if (($showText ?? true) && filled($text))
        <p class="ts-dyn-appointment__text" data-ts-text="text">{!! $text !!}</p>
    @endif

    @if ($workers->isEmpty())
        <p class="ts-dyn-empty">Jelenleg nincs elérhető munkatárs.</p>
    @else
        <div class="ts-dyn-appointment__grid">
            @foreach ($workers as $worker)
                <article class="ts-dyn-appointment__card">
                    <p class="ts-dyn-appointment__role">{{ $worker->title ?: 'Munkatárs' }}</p>
                    <h3>{{ $worker->name }}</h3>
                    @if (filled($worker->bio))
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($worker->bio), 120) }}</p>
                    @endif
                    <p class="ts-dyn-appointment__meta">{{ $worker->slot_duration_minutes }} perces időpontok</p>
                    @if ($showButton ?? true)
                        <a class="ts-dyn-appointment__cta" href="{{ url('/idopontfoglalas/'.$worker->slug) }}" data-ts-text="button">{{ $button }}</a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
