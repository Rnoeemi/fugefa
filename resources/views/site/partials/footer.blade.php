@php
    $settings = $siteSettings ?? \App\Models\SiteSetting::current();
@endphp

@if ($settings->hasCustomFooter())
    @if ($cssUrl = $settings->footerCssUrl())
        <link rel="stylesheet" href="{{ $cssUrl }}">
    @elseif (filled($settings->footer_css))
        <style>{!! $settings->footer_css !!}</style>
    @endif
    <style>{!! \App\Support\GrapesJs\SiteLayoutDefaults::footerCss() !!}</style>
    {!! \App\Support\GrapesJs\SiteLayoutDefaults::hydrateFooterContact(
        (string) $settings->footer_html,
        $settings
    ) !!}
@else
    @php
        $brandName = $settings->site_name ?? 'Tüsiszállás';
        $footerText = $settings->footer_text ?? null;
    @endphp

    <footer class="mt-auto bg-pine-deep text-white">
        <div class="section-narrow grid gap-10 px-5 py-14 md:grid-cols-[1.4fr_1fr_1fr] md:px-8">
            <div>
                <p class="font-display text-3xl">{{ $brandName }}</p>
                @if ($footerText)
                    <div class="mt-3 max-w-sm text-sm leading-relaxed text-white/70 prose prose-invert prose-sm">
                        {!! $footerText !!}
                    </div>
                @endif
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/50">Elérhetőség</p>
                <ul class="mt-4 space-y-2 text-sm text-white/80">
                    @if ($settings->address)
                        <li>{{ $settings->address }}</li>
                    @endif
                    @if ($settings->phone)
                        <li>Telefon: <a class="hover:text-white" href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}">{{ $settings->phone }}</a></li>
                    @endif
                    @if ($settings->email)
                        <li>E-mail: <a class="hover:text-white" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></li>
                    @endif
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/50">Oldalak</p>
                <ul class="mt-4 space-y-2 text-sm text-white/80">
                    @if ($accommodationModuleEnabled ?? true)
                        <li><a class="hover:text-white" href="{{ route('accommodations.index') }}">Szállások</a></li>
                        <li><a class="hover:text-white" href="{{ url('/foglalas-panel') }}">Foglalás</a></li>
                    @endif
                    <li><a class="hover:text-white" href="{{ route('contact') }}">Kapcsolat</a></li>
                </ul>
            </div>
        </div>
    </footer>
@endif
