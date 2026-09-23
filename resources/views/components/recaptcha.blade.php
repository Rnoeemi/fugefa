@php
    $enabled = app(\App\Services\RecaptchaVerifier::class)->isEnabled();
    $siteKey = (string) config('services.recaptcha.site_key');
@endphp

@if ($enabled)
    <div class="recaptcha-field">
        <div
            class="g-recaptcha"
            data-sitekey="{{ $siteKey }}"
            aria-label="Nem vagyok robot ellenőrzés"
        ><!--recaptcha--></div>
        @error('g-recaptcha-response')
            <p class="recaptcha-field__error" role="alert">{{ $message }}</p>
        @enderror
    </div>
@endif
