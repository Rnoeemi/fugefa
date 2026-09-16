@extends('layouts.site')

@section('title', 'Kapcsolat – '.$siteSettings->site_name)

@push('head')
    <style>{!! \App\Support\GrapesJs\SiteDynamicBlockStyles::css() !!}</style>
@endpush

@section('content')
    <section class="bg-pine-deep pt-28 text-white">
        <div class="section-narrow px-5 pb-12 md:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/50">Kapcsolat</p>
            <h1 class="mt-3 font-display text-4xl md:text-5xl">Írjon nekünk</h1>
            <p class="mt-4 max-w-2xl text-white/70">
                Ha bármilyen kérdése van, keressen minket bizalommal.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="section-narrow grid gap-12 md:grid-cols-2">
            <div class="reveal">
                <h2 class="section-title">Elérhetőségek</h2>
                <ul class="mt-6 space-y-3 text-ink/80">
                    @if ($siteSettings->address)
                        <li><strong class="text-pine">Cím:</strong> {{ $siteSettings->address }}</li>
                    @endif
                    @if ($siteSettings->phone)
                        <li><strong class="text-pine">Telefon:</strong> <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}">{{ $siteSettings->phone }}</a></li>
                    @endif
                    @if ($siteSettings->email)
                        <li><strong class="text-pine">E-mail:</strong> <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a></li>
                    @endif
                </ul>
                <p class="mt-8 text-sm text-ink/60">
                    Munkásszállással kapcsolatos megkereséseket telefonon vagy e-mailben fogadunk.
                </p>
            </div>

            <div class="reveal border border-pine/10 bg-white p-6 md:p-8">
                @if (session('status'))
                    <div class="mb-5 rounded-sm border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('contact.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="field-label" for="name">Név</label>
                        <input id="name" name="name" type="text" class="field" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="field" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div>
                        <label class="field-label" for="phone">Telefon</label>
                        <input id="phone" name="phone" type="tel" class="field" value="{{ old('phone') }}" autocomplete="tel">
                    </div>
                    <div>
                        <label class="field-label" for="message">Üzenet</label>
                        <textarea id="message" name="message" rows="6" class="field" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="ts-dyn-contact-form__consent">
                        <label class="ts-dyn-contact-form__consent-label" for="contact-privacy">
                            <input
                                id="contact-privacy"
                                class="ts-dyn-contact-form__consent-input"
                                name="privacy_accepted"
                                type="checkbox"
                                value="1"
                                required
                                @checked(old('privacy_accepted'))
                            >
                            <span class="ts-dyn-contact-form__consent-text">
                                Elolvastam és elfogadom az
                                <a href="{{ $privacyHref ?? '/oldal/adatkezelesi-tajekoztato' }}" target="_blank" rel="noopener noreferrer">adatkezelési tájékoztatót</a>.
                            </span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary">Küldés</button>
                </form>
            </div>
        </div>
    </section>
@endsection
