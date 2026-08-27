window.TSB_LAYOUT_WIRES = (function () {
    const frame = '<rect class="tsb-layout-wire__frame" x="4" y="4" width="152" height="92" rx="4"/>';
    const svg = (inner) => `<svg viewBox="0 0 160 100" class="tsb-layout-wire" aria-hidden="true">${frame}${inner}</svg>`;

    const wires = {
        // Hero / media band
        'hero-bottom': svg(`
            <rect class="tsb-layout-wire__shade" x="4" y="4" width="152" height="92" rx="4"/>
            <rect class="tsb-layout-wire__bar" x="16" y="62" width="36" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="16" y="70" width="70" height="7" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="80" width="54" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="86" width="22" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip tsb-layout-wire__chip--ghost" x="42" y="86" width="22" height="5" rx="1"/>`),
        'hero-center': svg(`
            <rect class="tsb-layout-wire__shade" x="4" y="4" width="152" height="92" rx="4"/>
            <rect class="tsb-layout-wire__bar" x="62" y="36" width="36" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="40" y="44" width="80" height="7" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="48" y="55" width="64" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="58" y="64" width="22" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip tsb-layout-wire__chip--ghost" x="84" y="64" width="22" height="5" rx="1"/>`),
        'hero-split': svg(`
            <rect class="tsb-layout-wire__panel" x="4" y="4" width="76" height="92" rx="4"/>
            <rect class="tsb-layout-wire__media" x="80" y="4" width="76" height="92"/>
            <rect class="tsb-layout-wire__bar" x="16" y="34" width="36" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="16" y="42" width="52" height="7" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="62" width="22" height="5" rx="1"/>`),
        'media-left': svg(`
            <rect class="tsb-layout-wire__shade" x="4" y="4" width="152" height="92" rx="4"/>
            <rect class="tsb-layout-wire__bar" x="16" y="38" width="40" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="16" y="48" width="72" height="7" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="64" width="24" height="6" rx="1"/>`),
        'media-center': svg(`
            <rect class="tsb-layout-wire__shade" x="4" y="4" width="152" height="92" rx="4"/>
            <rect class="tsb-layout-wire__bar" x="50" y="38" width="60" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="45" y="50" width="70" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="68" y="62" width="24" height="6" rx="1"/>`),
        'media-split': svg(`
            <rect class="tsb-layout-wire__panel" x="4" y="4" width="76" height="92" rx="4"/>
            <rect class="tsb-layout-wire__media" x="80" y="4" width="76" height="92"/>
            <rect class="tsb-layout-wire__bar" x="16" y="40" width="48" height="6" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="56" width="24" height="6" rx="1"/>`),

        // CTA / booking
        'cta-row': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="40" width="48" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="50" width="58" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="118" y="44" width="26" height="10" rx="2"/>`),
        'cta-center': svg(`
            <rect class="tsb-layout-wire__bar" x="50" y="34" width="60" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="45" y="46" width="70" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="67" y="58" width="26" height="10" rx="2"/>`),
        'cta-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="32" width="56" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="42" width="70" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="54" width="26" height="10" rx="2"/>`),
        'booking-row': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="40" width="52" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="50" width="64" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="112" y="42" width="32" height="12" rx="2"/>`),
        'booking-center': svg(`
            <rect class="tsb-layout-wire__bar" x="48" y="34" width="64" height="6" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="64" y="52" width="32" height="12" rx="2"/>`),
        'booking-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="34" width="64" height="6" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="52" width="32" height="12" rx="2"/>`),

        // Cards / lists / features
        'cards-3': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="28" width="40" height="52" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="28" width="40" height="52" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="106" y="28" width="40" height="52" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="20" y="38" width="28" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="66" y="38" width="28" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="112" y="38" width="28" height="4" rx="1"/>`),
        'cards-center': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="16" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="22" y="32" width="34" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="63" y="32" width="34" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="104" y="32" width="34" height="48" rx="2"/>`),
        'list-rows': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="20" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="42" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="64" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="22" y="26" width="40" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="22" y="48" width="40" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="22" y="70" width="40" height="4" rx="1"/>`),
        'icons-row': svg(`
            <circle class="tsb-layout-wire__chip" cx="36" cy="42" r="8"/>
            <circle class="tsb-layout-wire__chip" cx="80" cy="42" r="8"/>
            <circle class="tsb-layout-wire__chip" cx="124" cy="42" r="8"/>
            <rect class="tsb-layout-wire__bar" x="22" y="58" width="28" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="66" y="58" width="28" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="110" y="58" width="28" height="3" rx="1"/>`),

        // FAQ
        'faq-stack': svg(`
            <rect class="tsb-layout-wire__panel" x="24" y="18" width="112" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="42" width="112" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="66" width="112" height="18" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="32" y="24" width="70" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="32" y="48" width="60" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="32" y="72" width="66" height="4" rx="1"/>`),
        'faq-two-col': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="20" width="64" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="20" width="64" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="12" y="42" width="64" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="42" width="64" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="12" y="64" width="64" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="64" width="64" height="16" rx="2"/>`),
        'faq-split': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="28" width="48" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="40" width="52" height="3" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="80" y="18" width="66" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="80" y="40" width="66" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="80" y="62" width="66" height="16" rx="2"/>`),

        // Quote
        'quote-marquee': svg(`
            <rect class="tsb-layout-wire__panel" x="-10" y="30" width="70" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="68" y="30" width="70" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="146" y="30" width="40" height="40" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="78" y="42" width="50" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="88" y="52" width="30" height="3" rx="1"/>`),
        'quote-single': svg(`
            <rect class="tsb-layout-wire__bar" x="35" y="36" width="90" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="45" y="48" width="70" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="60" y="64" width="40" height="3" rx="1"/>`),
        'quote-grid': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="28" width="42" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="59" y="28" width="42" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="106" y="28" width="42" height="48" rx="2"/>`),

        // Text / legal
        'text-left': svg(`
            <rect class="tsb-layout-wire__bar" x="28" y="28" width="50" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="28" y="42" width="90" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="28" y="50" width="84" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="28" y="58" width="70" height="3" rx="1"/>`),
        'text-center': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="28" width="50" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="35" y="42" width="90" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="40" y="50" width="80" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="48" y="58" width="64" height="3" rx="1"/>`),
        'text-wide': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="26" width="60" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="40" width="132" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="48" width="128" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="56" width="118" height="3" rx="1"/>`),
        'legal-narrow': svg(`
            <rect class="tsb-layout-wire__bar" x="36" y="22" width="40" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="36" width="88" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="44" width="88" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="52" width="80" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="60" width="84" height="2" rx="1"/>`),
        'legal-wide': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="22" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="36" width="132" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="44" width="132" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="52" width="120" height="2" rx="1"/>`),
        'legal-two-col': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="18" width="40" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="34" width="60" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="42" width="56" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="86" y="34" width="60" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="86" y="42" width="54" height="2" rx="1"/>`),

        // Contact
        'contact-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="36" y="22" width="40" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="40" width="88" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="52" width="70" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="64" width="78" height="4" rx="1"/>`),
        'contact-cards': svg(`
            <rect class="tsb-layout-wire__panel" x="30" y="18" width="100" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="30" y="42" width="100" height="18" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="30" y="66" width="100" height="18" rx="2"/>`),
        'contact-inline': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="28" width="40" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="52" width="40" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="60" y="52" width="40" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="106" y="52" width="40" height="4" rx="1"/>`),

        // Forms
        'form-split': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="22" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="36" width="52" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="48" width="44" height="3" rx="1"/>
            <rect class="tsb-layout-wire__field" x="84" y="18" width="62" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="84" y="36" width="62" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="84" y="54" width="62" height="20" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="84" y="80" width="28" height="8" rx="2"/>`),
        'form-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="24" y="14" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="24" y="24" width="70" height="3" rx="1"/>
            <rect class="tsb-layout-wire__field" x="24" y="38" width="112" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="24" y="56" width="112" height="12" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="24" y="78" width="28" height="8" rx="2"/>`),
        'form-only': svg(`
            <rect class="tsb-layout-wire__field" x="40" y="18" width="80" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="40" y="36" width="80" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="40" y="54" width="80" height="20" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="66" y="80" width="28" height="8" rx="2"/>`),
        'search-panel': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="34" width="136" height="32" rx="3"/>
            <rect class="tsb-layout-wire__field" x="20" y="42" width="28" height="16" rx="2"/>
            <rect class="tsb-layout-wire__field" x="52" y="42" width="28" height="16" rx="2"/>
            <rect class="tsb-layout-wire__field" x="84" y="42" width="28" height="16" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="118" y="44" width="22" height="12" rx="2"/>`),
        'search-stack': svg(`
            <rect class="tsb-layout-wire__field" x="36" y="16" width="88" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="36" y="34" width="88" height="12" rx="2"/>
            <rect class="tsb-layout-wire__field" x="36" y="52" width="88" height="12" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="36" y="74" width="32" height="10" rx="2"/>`),
        'search-compact': svg(`
            <rect class="tsb-layout-wire__panel" x="16" y="38" width="128" height="24" rx="2"/>
            <rect class="tsb-layout-wire__field" x="22" y="44" width="22" height="12" rx="1"/>
            <rect class="tsb-layout-wire__field" x="48" y="44" width="22" height="12" rx="1"/>
            <rect class="tsb-layout-wire__field" x="74" y="44" width="22" height="12" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="102" y="45" width="34" height="10" rx="1"/>`),

        // Gallery / media
        'gallery-grid': svg(`
            <rect class="tsb-layout-wire__media" x="12" y="28" width="30" height="30" rx="1"/>
            <rect class="tsb-layout-wire__media" x="47" y="28" width="30" height="30" rx="1"/>
            <rect class="tsb-layout-wire__media" x="82" y="28" width="30" height="30" rx="1"/>
            <rect class="tsb-layout-wire__media" x="117" y="28" width="30" height="30" rx="1"/>`),
        'gallery-featured': svg(`
            <rect class="tsb-layout-wire__media" x="12" y="18" width="78" height="64" rx="1"/>
            <rect class="tsb-layout-wire__media" x="96" y="18" width="52" height="30" rx="1"/>
            <rect class="tsb-layout-wire__media" x="96" y="52" width="52" height="30" rx="1"/>`),
        'gallery-mosaic': svg(`
            <rect class="tsb-layout-wire__media" x="12" y="18" width="70" height="36" rx="1"/>
            <rect class="tsb-layout-wire__media" x="88" y="18" width="30" height="36" rx="1"/>
            <rect class="tsb-layout-wire__media" x="122" y="18" width="26" height="36" rx="1"/>
            <rect class="tsb-layout-wire__media" x="12" y="60" width="136" height="22" rx="1"/>`),
        'dyn-gallery-3': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="26" width="42" height="48" rx="1"/>
            <rect class="tsb-layout-wire__media" x="62" y="26" width="42" height="48" rx="1"/>
            <rect class="tsb-layout-wire__media" x="110" y="26" width="36" height="48" rx="1"/>`),

        // Stats / buttons / social
        'stats-center': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="18" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="16" y="36" width="40" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="36" width="40" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="104" y="36" width="40" height="40" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="26" y="48" width="20" height="8" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="70" y="48" width="20" height="8" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="114" y="48" width="20" height="8" rx="1"/>`),
        'stats-left': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="18" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="14" y="36" width="42" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="62" y="36" width="42" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="110" y="36" width="36" height="40" rx="2"/>`),
        'stats-row': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="40" width="40" height="6" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="68" y="28" width="26" height="44" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="98" y="28" width="26" height="44" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="128" y="28" width="18" height="44" rx="2"/>`),
        'buttons-center': svg(`
            <rect class="tsb-layout-wire__bar" x="50" y="30" width="60" height="6" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="48" y="52" width="28" height="12" rx="2"/>
            <rect class="tsb-layout-wire__chip tsb-layout-wire__chip--ghost" x="84" y="52" width="28" height="12" rx="2"/>`),
        'buttons-left': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="30" width="60" height="6" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="52" width="28" height="12" rx="2"/>
            <rect class="tsb-layout-wire__chip tsb-layout-wire__chip--ghost" x="50" y="52" width="28" height="12" rx="2"/>`),
        'buttons-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="50" y="22" width="60" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="55" y="40" width="50" height="12" rx="2"/>
            <rect class="tsb-layout-wire__chip tsb-layout-wire__chip--ghost" x="55" y="60" width="50" height="12" rx="2"/>`),
        'social-center': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="28" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="40" y="52" width="24" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="68" y="52" width="24" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="96" y="52" width="24" height="10" rx="2"/>`),
        'social-left': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="28" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="16" y="52" width="24" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="44" y="52" width="24" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="72" y="52" width="24" height="10" rx="2"/>`),
        'social-row': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="40" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="90" y="38" width="20" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="114" y="38" width="20" height="10" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="138" y="38" width="8" height="10" rx="2"/>`),

        // Video / map / split
        'video-boxed': svg(`
            <rect class="tsb-layout-wire__bar" x="40" y="14" width="80" height="5" rx="1"/>
            <rect class="tsb-layout-wire__media" x="30" y="28" width="100" height="56" rx="2"/>
            <polygon class="tsb-layout-wire__play" points="72,48 72,64 88,56"/>`),
        'video-full': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="14" width="60" height="5" rx="1"/>
            <rect class="tsb-layout-wire__media" x="12" y="28" width="136" height="56" rx="2"/>
            <polygon class="tsb-layout-wire__play" points="72,48 72,64 88,56"/>`),
        'video-split': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="32" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="44" width="52" height="3" rx="1"/>
            <rect class="tsb-layout-wire__media" x="80" y="20" width="66" height="60" rx="2"/>
            <polygon class="tsb-layout-wire__play" points="104,42 104,58 118,50"/>`),
        'map-side': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="28" width="44" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="40" width="48" height="3" rx="1"/>
            <rect class="tsb-layout-wire__media" x="78" y="18" width="68" height="64" rx="2"/>
            <circle class="tsb-layout-wire__pin" cx="112" cy="50" r="5"/>`),
        'map-top': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="12" width="132" height="48" rx="2"/>
            <circle class="tsb-layout-wire__pin" cx="80" cy="36" r="5"/>
            <rect class="tsb-layout-wire__bar" x="14" y="70" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="80" width="70" height="3" rx="1"/>`),
        'map-only': svg(`
            <rect class="tsb-layout-wire__media" x="12" y="14" width="136" height="72" rx="2"/>
            <circle class="tsb-layout-wire__pin" cx="80" cy="50" r="6"/>`),
        'split-image-right': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="30" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="42" width="54" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="14" y="58" width="24" height="8" rx="2"/>
            <rect class="tsb-layout-wire__media" x="84" y="18" width="62" height="64" rx="2"/>`),
        'split-image-left': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="18" width="62" height="64" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="90" y="30" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="90" y="42" width="54" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="90" y="58" width="24" height="8" rx="2"/>`),
        'split-stacked': svg(`
            <rect class="tsb-layout-wire__media" x="30" y="12" width="100" height="40" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="30" y="62" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="30" y="72" width="70" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="30" y="82" width="24" height="8" rx="2"/>`),
        'featured-image-left': svg(`
            <rect class="tsb-layout-wire__media" x="12" y="18" width="70" height="64" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="92" y="28" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="92" y="40" width="52" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="92" y="58" width="28" height="8" rx="2"/>`),
        'featured-image-right': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="28" width="48" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="40" width="52" height="3" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="14" y="58" width="28" height="8" rx="2"/>
            <rect class="tsb-layout-wire__media" x="78" y="18" width="70" height="64" rx="2"/>`),
        'featured-stacked': svg(`
            <rect class="tsb-layout-wire__media" x="24" y="12" width="112" height="42" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="24" y="64" width="60" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="24" y="78" width="28" height="8" rx="2"/>`),

        // Amenities / nearby / steps / checkin / logos / pricing
        'chips-row': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="22" width="50" height="5" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="24" y="48" width="28" height="10" rx="5"/>
            <rect class="tsb-layout-wire__chip" x="58" y="48" width="24" height="10" rx="5"/>
            <rect class="tsb-layout-wire__chip" x="88" y="48" width="26" height="10" rx="5"/>
            <rect class="tsb-layout-wire__chip" x="120" y="48" width="20" height="10" rx="5"/>`),
        'amenities-grid': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="28" width="42" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="62" y="28" width="42" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="110" y="28" width="36" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="56" width="42" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="62" y="56" width="42" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="110" y="56" width="36" height="20" rx="2"/>`),
        'amenities-list': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="30" y="24" width="100" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="30" y="40" width="100" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="30" y="56" width="100" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="30" y="72" width="100" height="4" rx="1"/>`),
        'nearby-2': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="22" width="64" height="28" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="22" width="62" height="28" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="56" width="64" height="28" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="56" width="62" height="28" rx="2"/>`),
        'nearby-4': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="34" width="32" height="36" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="48" y="34" width="32" height="36" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="34" width="32" height="36" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="120" y="34" width="28" height="36" rx="2"/>`),
        'nearby-list': svg(`
            <rect class="tsb-layout-wire__panel" x="20" y="16" width="120" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="20" y="38" width="120" height="16" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="20" y="60" width="120" height="16" rx="2"/>`),
        'checkin-3': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="28" width="42" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="28" width="42" height="48" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="106" y="28" width="40" height="48" rx="2"/>`),
        'checkin-stack': svg(`
            <rect class="tsb-layout-wire__panel" x="24" y="14" width="112" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="40" width="112" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="66" width="112" height="20" rx="2"/>`),
        'checkin-row': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="36" width="44" height="28" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="36" width="44" height="28" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="108" y="36" width="40" height="28" rx="2"/>`),
        'steps-3': svg(`
            <circle class="tsb-layout-wire__pin" cx="30" cy="30" r="8"/>
            <circle class="tsb-layout-wire__pin" cx="80" cy="30" r="8"/>
            <circle class="tsb-layout-wire__pin" cx="130" cy="30" r="8"/>
            <rect class="tsb-layout-wire__panel" x="12" y="48" width="36" height="34" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="62" y="48" width="36" height="34" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="112" y="48" width="36" height="34" rx="2"/>`),
        'steps-timeline': svg(`
            <line class="tsb-layout-wire__line" x1="28" y1="20" x2="28" y2="84"/>
            <circle class="tsb-layout-wire__pin" cx="28" cy="24" r="5"/>
            <circle class="tsb-layout-wire__pin" cx="28" cy="50" r="5"/>
            <circle class="tsb-layout-wire__pin" cx="28" cy="76" r="5"/>
            <rect class="tsb-layout-wire__bar" x="42" y="20" width="90" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="42" y="46" width="90" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="42" y="72" width="90" height="5" rx="1"/>`),
        'steps-stack': svg(`
            <rect class="tsb-layout-wire__panel" x="24" y="14" width="112" height="22" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="42" width="112" height="22" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="24" y="70" width="112" height="22" rx="2"/>`),
        'logos-row': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="36" width="30" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="50" y="36" width="30" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="86" y="36" width="30" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="122" y="36" width="24" height="28" rx="2"/>`),
        'logos-2x2': svg(`
            <rect class="tsb-layout-wire__media" x="40" y="18" width="36" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="84" y="18" width="36" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="40" y="54" width="36" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="84" y="54" width="36" height="28" rx="2"/>`),
        'logos-left': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="36" width="30" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="50" y="36" width="30" height="28" rx="2"/>
            <rect class="tsb-layout-wire__media" x="86" y="36" width="30" height="28" rx="2"/>`),
        'pricing-table': svg(`
            <rect class="tsb-layout-wire__panel" x="20" y="18" width="120" height="14" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="24" y="40" width="112" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="24" y="52" width="112" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="24" y="64" width="112" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="24" y="76" width="112" height="3" rx="1"/>`),
        'pricing-cards': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="24" width="42" height="56" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="24" width="42" height="56" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="106" y="24" width="40" height="56" rx="2"/>`),
        'pricing-compact': svg(`
            <rect class="tsb-layout-wire__panel" x="36" y="22" width="88" height="12" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="40" y="42" width="80" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="40" y="52" width="80" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="40" y="62" width="80" height="2" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="40" y="72" width="80" height="2" rx="1"/>`),

        // Nav / footer / layout containers
        'nav-brand-left': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="42" width="28" height="8" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="70" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="92" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="114" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="136" y="40" width="10" height="12" rx="2"/>`),
        'nav-brand-center': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="66" y="42" width="28" height="8" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="114" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="136" y="40" width="10" height="12" rx="2"/>`),
        'nav-menu-left': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="36" y="44" width="16" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="118" y="42" width="28" height="8" rx="1"/>`),
        'nav-minimal': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="42" width="28" height="8" rx="1"/>
            <rect class="tsb-layout-wire__chip" x="136" y="40" width="10" height="12" rx="2"/>`),
        'footer-2': svg(`
            <rect class="tsb-layout-wire__bar" x="16" y="28" width="40" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="42" width="50" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="100" y="28" width="40" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="100" y="38" width="36" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="100" y="48" width="30" height="3" rx="1"/>`),
        'footer-3': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="30" width="30" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="60" y="30" width="30" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="110" y="30" width="30" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="60" y="42" width="26" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="110" y="42" width="26" height="3" rx="1"/>`),
        'footer-stacked': svg(`
            <rect class="tsb-layout-wire__bar" x="55" y="28" width="50" height="6" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="50" y="44" width="60" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="58" y="56" width="44" height="3" rx="1"/>`),
        'footermin-split': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="46" width="40" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="100" y="46" width="20" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="126" y="46" width="20" height="4" rx="1"/>`),
        'footermin-center': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="55" y="38" width="50" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="50" y="54" width="60" height="4" rx="1"/>`),
        'footermin-stack': svg(`
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="36" width="50" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="16" y="52" width="30" height="4" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="52" y="52" width="30" height="4" rx="1"/>`),
        'layout-boxed': svg(`
            <rect class="tsb-layout-wire__panel" x="28" y="18" width="104" height="64" rx="2"/>`),
        'layout-narrow': svg(`
            <rect class="tsb-layout-wire__panel" x="48" y="18" width="64" height="64" rx="2"/>`),
        'layout-full': svg(`
            <rect class="tsb-layout-wire__panel" x="8" y="18" width="144" height="64" rx="2"/>`),
        'layout-equal-2': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="18" width="64" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="18" width="64" height="64" rx="2"/>`),
        'layout-wide-left': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="18" width="90" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="110" y="18" width="38" height="64" rx="2"/>`),
        'layout-wide-right': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="18" width="38" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="58" y="18" width="90" height="64" rx="2"/>`),
        'layout-equal-3': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="18" width="42" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="60" y="18" width="42" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="108" y="18" width="40" height="64" rx="2"/>`),
        'layout-featured-center': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="18" width="32" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="50" y="18" width="60" height="64" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="116" y="18" width="32" height="64" rx="2"/>`),

        // Dynamic lists / cards / details
        'accom-grid': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="18" width="42" height="28" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="14" y="46" width="42" height="32" rx="1"/>
            <rect class="tsb-layout-wire__media" x="62" y="18" width="42" height="28" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="62" y="46" width="42" height="32" rx="1"/>
            <rect class="tsb-layout-wire__media" x="110" y="18" width="36" height="28" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="110" y="46" width="36" height="32" rx="1"/>`),
        'accom-grid-2': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="18" width="64" height="36" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="14" y="54" width="64" height="28" rx="1"/>
            <rect class="tsb-layout-wire__media" x="86" y="18" width="60" height="36" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="86" y="54" width="60" height="28" rx="1"/>`),
        'accom-list': svg(`
            <rect class="tsb-layout-wire__media" x="14" y="16" width="40" height="28" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="62" y="22" width="70" height="5" rx="1"/>
            <rect class="tsb-layout-wire__media" x="14" y="54" width="40" height="28" rx="1"/>
            <rect class="tsb-layout-wire__bar" x="62" y="60" width="70" height="5" rx="1"/>`),
        'accom-rows': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="20" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="118" y="24" width="20" height="10" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="44" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="118" y="48" width="20" height="10" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="14" y="68" width="132" height="18" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="118" y="72" width="20" height="10" rx="2"/>`),
        'accom-compact': svg(`
            <rect class="tsb-layout-wire__panel" x="20" y="24" width="120" height="14" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="20" y="44" width="120" height="14" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="20" y="64" width="120" height="14" rx="1"/>`),
        'accom-cards': svg(`
            <rect class="tsb-layout-wire__panel" x="16" y="18" width="128" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="16" y="44" width="128" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="16" y="70" width="128" height="20" rx="2"/>`),
        'appt-grid': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="24" width="42" height="52" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="62" y="24" width="42" height="52" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="110" y="24" width="36" height="52" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="22" y="58" width="26" height="8" rx="2"/>
            <rect class="tsb-layout-wire__chip" x="70" y="58" width="26" height="8" rx="2"/>`),
        'appt-list': svg(`
            <rect class="tsb-layout-wire__panel" x="16" y="18" width="128" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="16" y="44" width="128" height="20" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="16" y="70" width="128" height="20" rx="2"/>`),
        'appt-compact': svg(`
            <rect class="tsb-layout-wire__panel" x="12" y="30" width="32" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="48" y="30" width="32" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="84" y="30" width="32" height="40" rx="2"/>
            <rect class="tsb-layout-wire__panel" x="120" y="30" width="28" height="40" rx="2"/>`),
        'details-aside-right': svg(`
            <rect class="tsb-layout-wire__bar" x="14" y="22" width="70" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="36" width="78" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="14" y="46" width="74" height="3" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="106" y="18" width="40" height="64" rx="2"/>`),
        'details-aside-left': svg(`
            <rect class="tsb-layout-wire__panel" x="14" y="18" width="40" height="64" rx="2"/>
            <rect class="tsb-layout-wire__bar" x="66" y="22" width="70" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="66" y="36" width="78" height="3" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="66" y="46" width="74" height="3" rx="1"/>`),
        'details-stack': svg(`
            <rect class="tsb-layout-wire__bar" x="20" y="16" width="80" height="5" rx="1"/>
            <rect class="tsb-layout-wire__bar tsb-layout-wire__bar--soft" x="20" y="30" width="120" height="3" rx="1"/>
            <rect class="tsb-layout-wire__panel" x="20" y="48" width="120" height="36" rx="2"/>`),
        // Legacy ids (hero JSON still uses bottom/center/split)
    };

    wires.bottom = wires['hero-bottom'];
    wires.center = wires['hero-center'];
    wires.split = wires['hero-split'];
    wires.left = wires['media-left'];
    wires.row = wires['cta-row'];
    wires.stack = wires['cta-stack'];

    return wires;
})();
