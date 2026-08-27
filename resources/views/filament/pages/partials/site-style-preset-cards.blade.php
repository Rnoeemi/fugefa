@php
    use App\Support\SiteColors;
    use App\Support\SiteFonts;
    use App\Support\SiteStylePresets;

    $presets = SiteStylePresets::all();
    $categories = SiteStylePresets::categories();
    $selected = SiteStylePresets::normalizeKey(
        $get('style_preset') ?? data_get($this->data ?? [], 'style_preset') ?? SiteStylePresets::DEFAULT
    );
    $selectedCategory = (string) ($presets[$selected]['category'] ?? 'clean');

    $presetsByCategory = [];
    foreach ($categories as $catKey => $cat) {
        $presetsByCategory[$catKey] = [];
    }
    foreach ($presets as $key => $preset) {
        $catKey = (string) ($preset['category'] ?? 'clean');
        if (! isset($presetsByCategory[$catKey])) {
            $presetsByCategory[$catKey] = [];
        }
        $presetsByCategory[$catKey][$key] = $preset;
    }

    $fontFamilies = [];
    foreach ($presets as $preset) {
        $fontFamilies[] = $preset['font_sans'] ?? null;
        $fontFamilies[] = $preset['font_display'] ?? null;
    }
    $previewFontsUrl = SiteFonts::stylesheet(...$fontFamilies);
    $adminCss = (string) file_get_contents(public_path('css/site-style-presets-admin.css'));
    $categoryHints = collect($categories)
        ->mapWithKeys(fn (array $cat, string $key): array => [$key => $cat['hint']])
        ->all();
@endphp

<style>{!! $adminCss !!}</style>
@if ($previewFontsUrl)
    <style>@import url('{{ $previewFontsUrl }}');</style>
@endif

<div
    class="ssp-root"
    wire:key="style-preset-cards-{{ $selected }}"
    x-data="{ cat: '{{ $selectedCategory }}', hints: @js($categoryHints) }"
>
    <div class="ssp-tabs" role="tablist" aria-label="Stílus kategóriák">
        @foreach ($categories as $catKey => $cat)
            <button
                type="button"
                class="ssp-tab"
                role="tab"
                id="ssp-tab-{{ $catKey }}"
                :class="{ 'is-active': cat === '{{ $catKey }}' }"
                :aria-selected="cat === '{{ $catKey }}' ? 'true' : 'false'"
                :tabindex="cat === '{{ $catKey }}' ? 0 : -1"
                aria-controls="ssp-panel-{{ $catKey }}"
                @click="cat = '{{ $catKey }}'"
            >
                {{ $cat['label'] }}
            </button>
        @endforeach
    </div>

    <p class="ssp-cat-hint" x-text="hints[cat] || ''"></p>

    @foreach ($categories as $catKey => $cat)
        <div
            class="ssp-panel"
            role="tabpanel"
            id="ssp-panel-{{ $catKey }}"
            aria-labelledby="ssp-tab-{{ $catKey }}"
            x-show="cat === '{{ $catKey }}'"
            x-cloak
            @if ($catKey !== $selectedCategory) hidden @endif
            x-bind:hidden="cat !== '{{ $catKey }}'"
        >
            <div class="ssp-grid">
                @foreach (($presetsByCategory[$catKey] ?? []) as $key => $preset)
                    @php
                        $colors = SiteColors::normalize($preset['colors'] ?? null);
                        $fontDisplay = $preset['font_display'] ?? 'Georgia, serif';
                        $fontSans = $preset['font_sans'] ?? 'system-ui, sans-serif';
                        $isActive = $selected === $key;
                        $radiusCard = (string) data_get($preset, 'tokens.--radius-card', '0.75rem');
                        $radiusBtn = (string) data_get($preset, 'tokens.--radius-control', '0.5rem');
                        $shadow = (string) data_get($preset, 'tokens.--shadow-soft', 'none');
                        $shortDesc = filled($preset['tagline'] ?? null)
                            ? (string) $preset['tagline']
                            : (filled($preset['description'] ?? null)
                                ? \Illuminate\Support\Str::before($preset['description'], '.').'.'
                                : '');
                    @endphp

                    <button
                        type="button"
                        class="ssp-card{{ $isActive ? ' is-active' : '' }}"
                        style="
                            --ssp-primary: {{ $colors['primary'] }};
                            --ssp-accent: {{ $colors['accent'] }};
                            --ssp-light: {{ $colors['light'] }};
                            --ssp-text: {{ $colors['text'] }};
                            --ssp-font-display: '{{ $fontDisplay }}', ui-serif, Georgia, serif;
                            --ssp-font-sans: '{{ $fontSans }}', ui-sans-serif, system-ui, sans-serif;
                            --ssp-radius-card: {{ $radiusCard }};
                            --ssp-radius-btn: {{ $radiusBtn }};
                            --ssp-shadow: {{ $shadow }};
                        "
                        wire:click="selectStylePreset('{{ $key }}')"
                        @if ($isActive) aria-current="true" @endif
                    >
                        <div class="ssp-preview" aria-hidden="true">
                            <div class="ssp-preview-sheet">
                                <span class="ssp-preview-title">Aa</span>
                                <span class="ssp-preview-line"></span>
                                <span class="ssp-preview-line is-short"></span>
                                <span class="ssp-preview-btn">Gomb</span>
                            </div>

                            <div class="ssp-swatches">
                                <span class="ssp-swatch" style="background: {{ $colors['primary'] }};"></span>
                                <span class="ssp-swatch" style="background: {{ $colors['accent'] }};"></span>
                                <span class="ssp-swatch" style="background: {{ $colors['light'] }}; border-color: rgba(0,0,0,.08);"></span>
                            </div>

                            <span class="ssp-check" title="Aktív stílus">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.333a1 1 0 0 1-1.432.01L3.29 9.79a1 1 0 1 1 1.42-1.408l3.58 3.612 6.54-6.615a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>

                        <div class="ssp-body">
                            <h3 class="ssp-title">{{ $preset['label'] }}</h3>
                            <p class="ssp-desc">{{ $shortDesc }}</p>
                            <p class="ssp-meta">{{ $fontDisplay }} · {{ $preset['shape'] ?? '' }}</p>
                            <p class="ssp-active-label">Kiválasztva</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach

    <p class="ssp-help">
        Válassz lapfület, majd egy stílust. A preset a térközöket, sarkokat, árnyékokat és tipográfiát állítja be, és felajánlja a hozzáillő színeket.
        Ezeket utána szabadon módosíthatod. A változások mentés után érvényesülnek.
    </p>
</div>
