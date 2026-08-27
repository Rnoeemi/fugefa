<div class="ts-dyn-contact-form__inner">
    <div class="ts-dyn-contact-form__info">
        @if (($showTitle ?? true) && filled($title))
            <h2 data-ts-text="title">{!! $title !!}</h2>
        @endif
        @if (($showText ?? true) && filled($text))
            <p data-ts-text="text">{!! $text !!}</p>
        @endif
        <ul>
            @if ($settings->address)
                <li><strong>Cím:</strong> {{ $settings->address }}</li>
            @endif
            @if ($settings->phone)
                <li><strong>Telefon:</strong> <a href="tel:{{ preg_replace('/\s+/', '', $settings->phone) }}">{{ $settings->phone }}</a></li>
            @endif
            @if ($settings->email)
                <li><strong>E-mail:</strong> <a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></li>
            @endif
        </ul>
    </div>
    <form class="ts-dyn-contact-form__form" method="post" action="{{ route('contact.store') }}">
        @csrf

        @if (session('status'))
            <div class="ts-dyn-contact-form__status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="ts-dyn-contact-form__errors" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="ts-contact-name">Név</label>
            <input id="ts-contact-name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
        </div>
        <div>
            <label for="ts-contact-email">E-mail</label>
            <input id="ts-contact-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
        </div>
        <div>
            <label for="ts-contact-message">Üzenet</label>
            <textarea id="ts-contact-message" name="message" rows="5" required>{{ old('message') }}</textarea>
        </div>

        @php
            $privacyUrl = filled($privacyHref ?? null)
                ? (string) $privacyHref
                : '/oldal/adatkezelesi-tajekoztato';
        @endphp
        <div class="ts-dyn-contact-form__consent">
            <label class="ts-dyn-contact-form__consent-label" for="ts-contact-privacy">
                <input
                    id="ts-contact-privacy"
                    class="ts-dyn-contact-form__consent-input"
                    name="privacy_accepted"
                    type="checkbox"
                    value="1"
                    required
                    @checked(old('privacy_accepted'))
                >
                <span class="ts-dyn-contact-form__consent-text">
                    Elolvastam és elfogadom az
                    <a href="{{ $privacyUrl }}" target="_blank" rel="noopener noreferrer">adatkezelési tájékoztatót</a>.
                </span>
            </label>
        </div>

        @if ($showButton ?? true)
            <button type="submit" data-ts-text="button">{!! $button !!}</button>
        @endif
    </form>
</div>
