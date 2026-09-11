@vite(['resources/js/app.js', 'resources/js/guest-booking-calendar.js'])
<script type="module" src="{{ asset('js/guest-document-ocr.js') }}"></script>
@include('site.partials.theme-head')
<script src="{{ asset('js/ts-nav.js') }}?v=3"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.TsNav?.init?.(document);
    });
    window.addEventListener('load', () => {
        window.TsNav?.init?.(document);
    });
</script>
