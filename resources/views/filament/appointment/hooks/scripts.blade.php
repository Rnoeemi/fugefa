@vite(['resources/js/app.js'])
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
